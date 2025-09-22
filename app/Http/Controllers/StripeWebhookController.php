<?php

namespace App\Http\Controllers;

use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
use App\Models\User;
use App\Models\MemberNumberHistory;

class StripeWebhookController extends CashierWebhookController
{
    /**
     * この処理は、サブスクが契約された時に会員番号を割り振るためのロジック
     */
    protected function handleCustomerSubscriptionCreated(array $payload)
    {
        $customerId = $payload['data']['object']['customer'] ?? null;
        if ($customerId) {
            $user = User::where('stripe_id', $customerId)->first();
            
            if ($user && !$user->member_number) {
                // historiesテーブルから最新番号を取得
                $next = (MemberNumberHistory::max('number') ?? 0) + 1;
    
                // users に現在の会員番号を保存
                $user->member_number = $next;
                $user->save();
    
                // histories に記録
                MemberNumberHistory::create([
                    'user_id'     => $user->id,
                    'number'      => $next,
                    'assigned_at' => now(),
                ]);
            }
        }
        return parent::handleCustomerSubscriptionCreated($payload);
    }

    /**
     * この処理は、サブスクがキャンセルされた時に会員番号を取り消すためのロジック
     */
    protected function handleCustomerSubscriptionDeleted(array $payload)
    {
        
        $customerId = $payload['data']['object']['customer'] ?? null;
        if ($customerId) {
            $user = User::where('stripe_id', $customerId)->first();
            
            if ($user) {
                $user->member_number = null;
                $user->save();
                
            }
        }

        return parent::handleCustomerSubscriptionDeleted($payload);
    }
}
