<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
    
    public function changeEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);
    
        $user = $request->user();
        $old = $user->email;
    
        if ($old !== $request->email) {
            $user->email = $request->email;
            $user->email_verified_at = null;
            $user->save();
    
            // 新しいメールに認証メールを送る
            $user->sendEmailVerificationNotification();
    
            return back()->with('status', 'email-updated');
        }
    
        // 同じメールは更新しない
        return back()->with('warning', '同じメールアドレスです。');
    }

}
