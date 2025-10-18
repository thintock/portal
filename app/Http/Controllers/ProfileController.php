<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\MediaFile;
use App\Models\MediaRelation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // ✅ 現在のアバターを取得
        // media_files.type = 'avatar' を基準に取得
        $avatar = $user->mediaFiles()
            ->where('media_files.type', 'avatar')
            ->orderBy('media_relations.sort_order', 'asc')
            ->first();

        return view('profile.edit', [
            'user'   => $user,
            'avatar' => $avatar,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // チェックボックス
        $data['email_notification'] = (bool) ($data['email_notification'] ?? false);

        // 国コード正規化
        if (!empty($data['country'])) {
            $data['country'] = strtoupper(substr($data['country'], 0, 2));
        }

        /**
         * ================================
         * ✅ Avatarのアップロード処理
         * ================================
         */
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $disk = config('filesystems.default'); // s3 or public
    
            DB::transaction(function () use ($user, $file, $disk) {
                // 1️⃣ 既存アバターrelation削除（1対1管理）
                MediaRelation::where('mediable_type', get_class($user))
                    ->where('mediable_id', $user->id)
                    ->whereIn('media_file_id', function ($query) {
                        $query->select('id')
                              ->from('media_files')
                              ->where('type', 'avatar');
                    })
                    ->delete();
    
                // 2️⃣ MediaFile::uploadAndCreateを使用して登録
                $media = MediaFile::uploadAndCreate(
                    $file,
                    $user,
                    'avatar',
                    $disk,
                    'avatars/' . $user->id
                );
    
                // 3️⃣ 新しいrelationを登録
                MediaRelation::create([
                    'mediable_type' => get_class($user),
                    'mediable_id'   => $user->id,
                    'media_file_id' => $media->id,
                    'type'          => 'avatar',
                    'sort_order'    => 0,
                ]);
            });
        }

        /**
         * ================================
         * ✅ 基本情報の更新
         * ================================
         */
        $user->fill($data);

        // メール変更時は再認証
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        /**
         * ================================
         * ✅ Stripe 顧客情報更新
         * ================================
         */
        try {
            $user->createOrGetStripeCustomer();
            // 氏名が両方存在する場合は「姓＋名」を使用、どちらか欠けている場合は補完
            if (!empty($user->last_name) || !empty($user->first_name)) {
                $stripeName = trim(($user->last_name ?? '') . ' ' . ($user->first_name ?? ''));
            } else {
                // 両方nullまたは空ならニックネーム(name)を使用
                $stripeName = $user->name;
            }

            $user->updateStripeCustomer([
                'name'    => $stripeName,
                'email'   => $user->email,
                'phone'   => $user->phone,
                'address' => [
                    'line1'       => $user->address2,
                    'line2'       => $user->address3,
                    'city'        => $user->address1,
                    'state'       => $user->prefecture,
                    'postal_code' => $user->postal_code,
                    'country'     => $user->country ?? 'JP',
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('Stripe customer update failed: '.$e->getMessage(), [
                'user_id' => $user->id,
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();

        // メディアも削除
        MediaRelation::where('mediable_type', get_class($user))
            ->where('mediable_id', $user->id)
            ->delete();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
