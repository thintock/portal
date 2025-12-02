<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-br from-primary/40 via-accent/30 to-secondary/40 px-4">
        <div class="w-full max-w-md bg-white shadow-xl rounded-xl p-8">
            {{-- ロゴ --}}
            <div class="text-center mb-6">
                <img src="{{ asset('images/bakerista_logo.png') }}" alt="Bakerista" class="mx-auto w-24 mb-2">
                <h1 class="text-2xl font-bold text-primary">ログイン</h1>
                <p class="text-gray-500 text-sm mt-1">ベーカリスタポータルへようこそ</p>
            </div>

            {{-- ステータスメッセージ --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            {{-- フォーム --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- メールアドレス --}}
                <div>
                    <x-input-label for="email" :value="__('Eメール')" class="text-gray-800 font-semibold" />
                    <x-text-input id="email" type="email" name="email"
                        class="input input-bordered w-full mt-1"
                        :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- パスワード --}}
                <div x-data="{ showPassword: false }">
                    <x-input-label for="password" :value="__('パスワード')" class="text-gray-800 font-semibold" />
                
                    <div class="relative mt-1">
                        <x-text-input id="password" x-bind:type="showPassword ? 'text' : 'password'" name="password"
                            class="input input-bordered w-full focus:ring-2 focus:ring-primary pr-10"
                            required autocomplete="current-password" />
                
                        <button type="button" @click="showPassword = !showPassword"
                            :aria-label="showPassword ? 'パスワードを隠す' : 'パスワードを表示する'"
                            class="absolute inset-y-0 right-0 p-3 flex items-center text-gray-500 hover:text-gray-800">
                            
                            {{-- 非表示アイコン --}}
                            <svg x-show="!showPassword" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M1.5 1.5l21 21"/>
                            </svg>
                
                            {{-- 表示アイコン --}}
                            <svg x-show="showPassword" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-error text-sm" />
                </div>

                {{-- ログイン状態保持 --}}
                <div class="flex items-center mt-2">
                    <label for="remember_me" class="label cursor-pointer justify-start gap-2">
                        <input id="remember_me" type="checkbox" name="remember" class="checkbox checkbox-sm checkbox-primary" checked/>
                        <span class="label-text text-sm text-gray-600">ログイン状態を保持する</span>
                    </label>
                </div>

                {{-- ボタン・リンク --}}
                <div class="pt-4">
                    <button type="submit" class="btn btn-primary w-full">
                        {{ __('ログイン') }}
                    </button>
                </div>

                {{-- パスワードリセット --}}
                @if (Route::has('password.request'))
                    <div class="text-center mt-3">
                        <a href="{{ route('password.request') }}" class="link link-secondary text-sm">
                            パスワードをお忘れの方はこちら
                        </a>
                    </div>
                @endif

                {{-- 新規登録リンク --}}
                <div class="text-center mt-3">
                    <a href="{{ route('register') }}" class="link link-primary text-sm">
                        アカウントをお持ちでない方はこちらから登録
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
