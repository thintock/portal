<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
    
        // 1) バリデーション済みデータを取得
        $data = $request->validated();
    
        // 2) 追加整形
        // チェックボックス: 未送信なら false
        $data['email_notification'] = (bool) ($data['email_notification'] ?? false);
    
        // 国コードは大文字2桁に正規化（例: jp -> JP）
        if (!empty($data['country'])) {
            $data['country'] = strtoupper(substr($data['country'], 0, 2));
        }
        
        // avatar がアップロードされた場合
        if ($request->hasFile('avatar')) {
            $media = \App\Models\MediaFile::uploadAndCreate(
                $request->file('avatar'),
                $user,
                'avatar',
                null,
                'avatars'
            );
            $user->avatar_media_id = $media->id;
        }
        
        // 3) 代入
        $user->fill($data);
    
        // 4) メール変更時は再認証フラグをクリア
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
    
        // 5) 保存
        $user->save();
    
        // 6) Stripe 顧客情報にも反映（任意：税計算・請求書の住所/電話に使われます）
        try {
            $user->createOrGetStripeCustomer(); // 未作成なら作る
    
            // 名前の優先度: display_name > name
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
                // 必要に応じて tax_exempt や tax_id_data などもここで
            ]);
        } catch (\Throwable $e) {
            // ログだけ残して処理は継続（ユーザー更新は成功させる）
            \Log::warning('Stripe customer update failed: '.$e->getMessage(), ['user_id' => $user->id]);
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

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
