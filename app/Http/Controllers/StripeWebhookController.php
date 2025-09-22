<?php

namespace App\Http\Controllers;

use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
use App\Models\User;
use App\Models\MemberNumberHistory; // ユーザー会員番号採番モデルへの接続

class StripeWebhookController extends CashierWebhookController
{
    /**
     * この処理は、サブスクが契約された時に会員番号を割り振るためのロジック
     */
    protected function handleCustomerSubscriptionCreated(array $payload)
    {
        Log::info('Webhook[Created] 受信', [
            'event_id' => $payload['id'] ?? null,
            'type'     => $payload['type'] ?? null,
            'customer' => $payload['data']['object']['customer'] ?? null,
        ]);
        
        $customerId = $payload['data']['object']['customer'] ?? null;
        if ($customerId) {
            $user = User::where('stripe_id', $customerId)->first();
            
            Log::info('Webhook[Created] ユーザー検索結果', [
                'customerId' => $customerId,
                'user_found' => $user ? $user->id : null,
            ]);
            
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
                
                Log::info('Webhook[Created] 会員番号を採番', [
                    'user_id'        => $user->id,
                    'assigned_number'=> $next,
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
        Log::info('Webhook[Deleted] 受信', [
            'event_id' => $payload['id'] ?? null,
            'type'     => $payload['type'] ?? null,
            'customer' => $payload['data']['object']['customer'] ?? null,
        ]);
        
        $customerId = $payload['data']['object']['customer'] ?? null;
        if ($customerId) {
            $user = User::where('stripe_id', $customerId)->first();
            
            Log::info('Webhook[Deleted] ユーザー検索結果', [
                'customerId' => $customerId,
                'user_found' => $user ? $user->id : null,
            ]);
            
            if ($user) {
                $user->member_number = null;
                $user->save();
                
                Log::info('Webhook[Deleted] 会員番号を削除', [
                    'user_id' => $user->id,
                ]);
                
            }
        }

        return parent::handleCustomerSubscriptionDeleted($payload);
    }
}
