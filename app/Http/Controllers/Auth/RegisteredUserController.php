<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\Page;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $user = Auth::user();
        $page = Page::where('slug', 'register')->first();
        return view('auth.register', compact('user', 'page'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'user_type' => 'home',
            'user_status' => 'active',
        ]);

        event(new Registered($user));

        Auth::login($user);
        
        // Stripe 顧客を作成して同期
        try {
            $user->createOrGetStripeCustomer();
    
            $stripeName = $user->display_name ?: $user->name;
    
            $user->updateStripeCustomer([
                'name'    => $stripeName,
                'email'   => $user->email,
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Stripe customer creation failed: '.$e->getMessage(), ['user_id' => $user->id]);
        }

        return redirect(route('dashboard', absolute: false));
    }
}
