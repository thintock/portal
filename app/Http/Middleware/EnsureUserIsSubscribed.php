<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSubscribed
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('billing.show')
                ->with('error', 'ログインしてください。');
        }

        // 管理者は常に許可
        if ($user->role === 'admin') {
            return $next($request);
        }

        // ゲストも許可
        if ($user->role === 'guest') {
            return $next($request);
        }

        // 有料会員かチェック（default サブスク契約中）
        if ($user->subscribed('default')) {
            return $next($request);
        }

        // それ以外は拒否
        return redirect()->route('billing.show')
            ->with('error', 'このページは有料会員のみアクセス可能です。');
    }
}