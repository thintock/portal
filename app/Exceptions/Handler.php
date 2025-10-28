<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        //
    }

    /**
     * カスタムレンダー処理
     */
    public function render($request, Throwable $exception)
    {
        // 🔸 有効期限切れの署名URLアクセス時（403 Invalid Signature）
        if ($exception instanceof InvalidSignatureException) {
            // ログインユーザーならメール確認ページへ
            if (auth()->check()) {
                return redirect()
                    ->route('verification.notice')
                    ->with('warning', '認証リンクの有効期限が切れています。新しい確認メールを再送信してください。');
            }

            // 未ログインならログインページに誘導
            return redirect()
                ->route('login')
                ->with('warning', '認証リンクの有効期限が切れています。ログイン後に再送信してください。');
        }

        // 親クラスの処理を維持
        return parent::render($request, $exception);
    }
}
