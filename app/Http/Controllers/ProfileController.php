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

            // 1. 新しいファイルをmedia_filesに登録
            $media = MediaFile::create([
                'owner_type' => get_class($user),
                'owner_id'   => $user->id,
                'type'       => 'avatar',
                'path'       => $file->store('avatars', 'public'),
                'mime'       => $file->getMimeType(),
                'size'       => $file->getSize(),
                'alt'        => $user->name . 'のプロフィール画像',
            ]);

            // 2. 既存のアバターrelationを削除（1対1管理）
            MediaRelation::where('mediable_type', get_class($user))
                ->where('mediable_id', $user->id)
                ->whereIn('media_file_id', function ($query) {
                    $query->select('id')
                          ->from('media_files')
                          ->where('type', 'avatar');
                })
                ->delete();

            // 3. 新しいrelationを登録
            MediaRelation::create([
                'mediable_type' => get_class($user),
                'mediable_id'   => $user->id,
                'media_file_id' => $media->id,
                'type'          => 'avatar',
                'sort_order'    => 0,
            ]);
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
            $stripeName = $user->display_name ?: $user->name;

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
