<x-guest-layout>
    
    @if ($page && $page->body1)
        {!! $page->body1 !!}
    @endif

    {{-- 登録フォームセクション --}}
    <section id="register-form" class="py-24 bg-gradient-to-br from-primary/40 via-accent/30 to-secondary/40 text-white relative overflow-hidden">
        {{-- 背景装飾 --}}
        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/50 via-transparent to-transparent blur-3xl"></div>

        <div class="relative max-w-4xl mx-auto px-6 text-center">
            @if ($page && $page->body2)
                {!! $page->body2 !!}
            @endif
            <h2 class="text-3xl sm:text-4xl font-bold mb-4  text-neutral">
                無料で登録して、<br class="sm:hidden">ベーカリスタとつながろう。
            </h2>
            <p class="sm:text-lg text-neutral mb-12">
                登録すると、あなた専用のマイページにログインできるようになります。
            </p>

            {{-- 登録フォーム --}}
            <div class="mx-auto max-w-md bg-white/95 backdrop-blur-md shadow-2xl rounded-2xl p-6 sm:p-8 border border-white/40">
                <form method="POST" action="{{ route('register') }}" class="space-y-5 text-left text-gray-700">
                    @csrf

                    {{-- ニックネーム --}}
                    <div>
                        <x-input-label for="name" :value="__('ニックネーム（後から変更できます）')" class="text-gray-800 font-semibold" />
                        <x-text-input id="name" type="text" name="name"
                            class="input input-bordered w-full mt-1 focus:ring-2 focus:ring-primary"
                            :value="old('name')" required autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-error text-sm" />
                    </div>

                    {{-- メールアドレス --}}
                    <div>
                        <x-input-label for="email" :value="__('Eメール')" class="text-gray-800 font-semibold" />
                        <x-text-input id="email" type="email" name="email"
                            class="input input-bordered w-full mt-1 focus:ring-2 focus:ring-primary"
                            :value="old('email')" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-error text-sm" />
                    </div>

                    {{-- パスワード --}}
                    <div x-data="{ showPassword: false }">
                        <x-input-label for="password" :value="__('パスワード')" class="text-gray-800 font-semibold" />
                        
                        {{-- inputとボタンを相対的なコンテナで囲む --}}
                        <div class="relative mt-1">
                            <x-text-input id="password" x-bind:type="showPassword ? 'text' : 'password'" name="password" class="input input-bordered w-full focus:ring-2 focus:ring-primary pr-10" required autocomplete="new-password" />
                            
                            {{-- トグルボタン (絶対位置) --}}
                            <button type="button" @click="showPassword = !showPassword" :aria-label="showPassword ? 'パスワードを隠す' : 'パスワードを表示する'" class="absolute inset-y-0 right-0 p-3 flex items-center text-gray-500 hover:text-gray-800 focus:outline-none">
                                {{-- 目を閉じたアイコン (デフォルト) --}}
                                <svg x-show="!showPassword" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                    <path d="M1.5 1.5l21 21"/>
                                </svg>
                                {{-- 目を開けたアイコン --}}
                                <svg x-show="showPassword" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-error text-sm" />
                    </div>

                    {{-- パスワード確認 --}}
                    <div x-data="{ showPassword: false }">
                        <x-input-label for="password_confirmation" :value="__('パスワード（確認）')" class="text-gray-800 font-semibold" />
                        
                        {{-- inputとボタンを相対的なコンテナで囲む --}}
                        <div class="relative mt-1">
                            <x-text-input id="password_confirmation" x-bind:type="showPassword ? 'text' : 'password'" name="password_confirmation" class="input input-bordered w-full focus:ring-2 focus:ring-primary pr-10" required autocomplete="new-password" />
                            
                            {{-- トグルボタン (絶対位置) --}}
                            <button type="button" @click="showPassword = !showPassword" :aria-label="showPassword ? 'パスワードを隠す' : 'パスワードを表示する'" class="absolute inset-y-0 right-0 p-3 flex items-center text-gray-500 hover:text-gray-800 focus:outline-none">
                                {{-- 目を閉じたアイコン (デフォルト) --}}
                                <svg x-show="!showPassword" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                    <path d="M1.5 1.5l21 21"/>
                                </svg>
                                {{-- 目を開けたアイコン --}}
                                <svg x-show="showPassword" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-error text-sm" />
                    </div>

                    {{-- 登録ボタン --}}
                    <div class="pt-6">
                        <button type="submit" class="btn btn-accent w-full text-white shadow-lg hover:shadow-xl transition btn-sm md:btn-lg">
                            {{ __('ベーカリスタポータルに登録する') }}
                        </button>
                    </div>

                    {{-- ログインリンク --}}
                    <div class="text-center mt-6">
                        <a href="{{ route('login') }}" class="link link-hover text-sm text-primary">
                            すでに登録済みの方はこちらからログイン
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
    @if ($page && $page->body3)
        {!! $page->body3 !!}
    @endif
</x-guest-layout>