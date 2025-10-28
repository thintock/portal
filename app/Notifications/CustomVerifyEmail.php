<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class CustomVerifyEmail extends BaseVerifyEmail
{
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('【ベーカリスタポータル】メールアドレスの確認をお願いします')
            ->greeting('こんにちは ' . ($notifiable->name ?? '') . ' さん')
            ->line('ベーカリスタポータルへのご登録ありがとうございます。')
            ->line('以下のボタンをクリックして、メールアドレスの確認を完了してください。')
            ->action('メールアドレスを承認する', $this->verificationUrl($notifiable))
            ->line('このメールに心当たりがない場合は、破棄してください。')
            ->salutation('ベーカリスタ株式会社');
    }
}
