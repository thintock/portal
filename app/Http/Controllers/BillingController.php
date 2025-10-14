<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Subscription;

class BillingController extends Controller
{
    /** プラン選択画面 */
    public function show()
    {
        $user = Auth::user();
        
        $prices = config('services.stripe.prices');
        
        // 現在のユーザーのsubscriptionを取得（type = 'default'）
        $subscription = Subscription::where('user_id', $user->id)
            ->where('type', 'default')
            ->latest()
            ->first();
        
        return view('billing.show', compact('prices', 'subscription'));
    }

    /** Checkout に遷移してサブスク開始 */
    public function subscribe(Request $request)
    {
        $request->validate([
            'price' => ['required', 'string'], // price_xxx
        ]);
    
        $user = $request->user();
    
        if ($user->subscribed('default')) {
            // 既に契約中ならポータルへ誘導
            return redirect()->route('billing.portal')->with('status', '既に入会済みです。');
        }
    
        $priceId = $request->input('price');
    
        // 事前にStripe顧客情報を更新
        $user->updateStripeCustomer([
            'name'    => $user->display_name ?? $user->name,
            'email'   => $user->email,
            'address' => [
                'line1'       => $user->address2,   // 町名番地
                'line2'       => $user->address3,   // 建物部屋番号
                'city'        => $user->address1,   // 市区町村
                'state'       => $user->prefecture, // 都道府県
                'postal_code' => $user->postal_code,
                'country'     => $user->country ?? 'JP',
            ],
            'phone' => $user->phone,
        ]);
    
        return $user->newSubscription('default', [$priceId])
            ->checkout([
                'success_url' => route('billing.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => route('billing.cancel'),
                'allow_promotion_codes' => true,
            ]);
    }


    /** 成功画面（Webhookが最終確定を処理） */
    public function success(Request $request)
    {
        // 必要なら $request->get('session_id') で Session 参照可能
        return view('billing.success');
    }

    /** キャンセル画面 */
    public function cancel()
    {
        return view('billing.cancel');
    }

    /** Stripe Billing Portal（解約/支払方法変更など） */
    public function portal(Request $request)
    {
        $user = $request->user();

        // Stripe Customer を必ず紐付け（未作成なら作成）
        if (! $user->hasStripeId()) {
            $user->createOrGetStripeCustomer();
        }

        // 戻り先URL（ポータル終了後に戻す先）
        $returnUrl = route('dashboard'); // 例：ダッシュボードに戻す

        // 1) 直接URL生成→リダイレクト
        $url = $user->billingPortalUrl($returnUrl);
        return redirect()->away($url);

    }
}
