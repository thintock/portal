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

        // default サブスク契約中か確認
        if (! $user || ! $user->subscribed('default')) {
            return redirect()->route('billing.show')
                ->with('error', 'このページは有料会員のみアクセス可能です。');
        }

        return $next($request);
    }
}
