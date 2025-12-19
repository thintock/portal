<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Subscription;
use Carbon\Carbon;

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
        // ===== 募集期間判定 =====
        $now = Carbon::now('Asia/Tokyo');

        // 今月25日12:00
        $start = $now->copy()->day(25)->setTime(12, 0, 0);

        // 今月末22:59:59
        $end = $now->copy()->endOfMonth()->setTime(22, 59, 59);

        // 月末を過ぎていたら次回は翌月
        if ($now->gt($end)) {
            $start = $start->addMonth();
            $end   = $end->addMonth()->endOfMonth()->setTime(22, 59, 59);
        }

        $isRecruiting = $now->between($start, $end);

        // 次回募集開始（カウントダウン用）
        $nextRecruitingAt = $isRecruiting
            ? null
            : $start;

        return view('billing.show', compact(
            'prices',
            'subscription',
            'isRecruiting',
            'nextRecruitingAt'
        ));
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
        
        // 姓名結合
        $fullName = trim(
            ($user->last_name ?? '') . ' ' . ($user->first_name ?? '')
        );
        // null の場合は name カラム fallback
        if (empty($fullName)) {
            $fullName = $user->name;
        }
        
        // 事前にStripe顧客情報を更新
        $user->updateStripeCustomer([
            'name'    => $fullName,
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
                // Stripe Tax を有効化
                'automatic_tax' => ['enabled' => true],
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
        $user = Auth::user();
    
        // 現在のsubscriptionを取得
        $subscription = Subscription::where('user_id', $user->id)
            ->where('type', 'default')
            ->latest()
            ->first();
    
        return view('billing.cancel', compact('subscription'));
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
