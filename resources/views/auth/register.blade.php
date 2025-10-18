<x-guest-layout>
    {{-- ヒーローセクション --}}
    <section class="hero min-h-[90vh] bg-gradient-to-br from-primary/20 via-base-100 to-accent/10 px-6 md:px-10">
        <div class="hero-content flex flex-col lg:flex-row-reverse items-center gap-10 max-w-6xl mx-auto py-10">
            {{-- 画像 --}}
            <img src="{{ asset('images/bakerista_circle_gray.png') }}" 
                 alt="Bakerista Portal"
                 class="w-40 md:w-52 lg:w-60 drop-shadow-lg mb-6 lg:mb-0">

            {{-- テキスト --}}
            <div class="text-center lg:text-left">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold leading-snug mb-4">
                    ベーカリスタと、<br class="sm:hidden">もっと近くに。
                </h1>
                <p class="text-base sm:text-lg text-gray-700 leading-relaxed mb-6">
                    「ベーカリスタポータル」は、<br class="hidden sm:block">
                    ベーカリスタとお客様をつなぐ、<br class="hidden sm:block">
                    新しいコミュニケーションの中心です。<br>
                    登録は無料。あなた専用のページから<br class="hidden sm:block">
                    さまざまなサービスにアクセスできます。
                </p>

                <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4 mb-4">
                    <a href="#register-form" class="btn btn-primary btn-sm sm:btn-md md:btn-lg shadow-lg">
                        無料で登録する
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline btn-sm sm:btn-md md:btn-lg">
                        ログイン
                    </a>
                </div>

                <p class="text-xs sm:text-sm text-gray-500">
                    会員登録は無料。メールアドレスだけで簡単に始められます。
                </p>
            </div>
        </div>
    </section>

    {{-- コンセプトセクション --}}
    <section class="py-20 bg-base-200 px-6">
        <div class="max-w-5xl mx-auto text-center space-y-10">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-800">
                ベーカリスタポータルとは？
            </h2>
            <p class="text-base sm:text-lg text-gray-600 leading-relaxed max-w-3xl mx-auto">
                北海道発のクラフト小麦粉ブランド「ベーカリスタ」が運営する、<br class="hidden sm:block">
                お客様専用のオンラインサービスです。<br>
                会員登録することで、ベーカリスタの最新情報やご案内を<br class="hidden sm:block">
                よりスムーズに受け取ることができます。
            </p>

            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3 mt-10">
                <div class="card bg-white shadow-md border hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="card-body">
                        <h3 class="card-title text-lg sm:text-xl">💬 つながる</h3>
                        <p>ベーカリスタからのお知らせやメッセージを、あなた専用のページで確認できます。</p>
                    </div>
                </div>

                <div class="card bg-white shadow-md border hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="card-body">
                        <h3 class="card-title text-lg sm:text-xl">🧾 管理する</h3>
                        <p>登録情報や注文履歴をまとめて確認できる便利なマイページを順次拡充予定です。</p>
                    </div>
                </div>

                <div class="card bg-white shadow-md border hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="card-body">
                        <h3 class="card-title text-lg sm:text-xl">🎁 ひろがる</h3>
                        <p>ベーカリスタならではの限定コンテンツや特典情報をお届けします。</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 登録フォームセクション --}}
    <section id="register-form" class="py-24 bg-gradient-to-br from-primary/40 via-accent/30 to-secondary/40 text-white relative overflow-hidden">
        {{-- 背景装飾 --}}
        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/50 via-transparent to-transparent blur-3xl"></div>

        <div class="relative max-w-4xl mx-auto px-6 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold mb-4 drop-shadow-lg">
                無料で登録して、<br class="sm:hidden">ベーカリスタとつながろう。
            </h2>
            <p class="text-base sm:text-lg text-white/90 mb-12">
                登録すると、あなた専用のマイページにログインできるようになります。
            </p>

            {{-- 登録フォーム --}}
            <div class="mx-auto max-w-md bg-white/95 backdrop-blur-md shadow-2xl rounded-2xl p-6 sm:p-8 border border-white/40">
                <form method="POST" action="{{ route('register') }}" class="space-y-5 text-left text-gray-700">
                    @csrf

                    {{-- ニックネーム --}}
                    <div>
                        <x-input-label for="name" :value="__('ニックネーム')" class="text-gray-800 font-semibold" />
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
                    <div>
                        <x-input-label for="password" :value="__('パスワード')" class="text-gray-800 font-semibold" />
                        <x-text-input id="password" type="password" name="password"
                            class="input input-bordered w-full mt-1 focus:ring-2 focus:ring-primary"
                            required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-error text-sm" />
                    </div>

                    {{-- パスワード確認 --}}
                    <div>
                        <x-input-label for="password_confirmation" :value="__('パスワード（確認）')" class="text-gray-800 font-semibold" />
                        <x-text-input id="password_confirmation" type="password" name="password_confirmation"
                            class="input input-bordered w-full mt-1 focus:ring-2 focus:ring-primary"
                            required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-error text-sm" />
                    </div>

                    {{-- 登録ボタン --}}
                    <div class="pt-6">
                        <button type="submit" class="btn btn-accent w-full text-white shadow-lg hover:shadow-xl transition btn-sm md:btn-lg">
                            {{ __('ベーカリスタポータルに登録する（無料）') }}
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

</x-guest-layout>