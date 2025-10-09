<!DOCTYPE html>
<html lang="ja" data-theme="cupcake">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ベーカリスタサークル | 仲間とつながるパンのコミュニティ</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <meta name="theme-color" content="#ffffff">
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-base-100 text-base-content font-sans antialiased">

    {{-- ヒーローセクション --}}
    <section class="hero min-h-[90vh] bg-gradient-to-br from-primary/20 via-base-100 to-accent/10 px-6 md:px-10">
        <div class="hero-content flex flex-col lg:flex-row-reverse items-center gap-10 max-w-6xl mx-auto py-10">
            {{-- 画像 --}}
            <img src="{{ asset('images/bakerista_circle_gray.png') }}" 
                 alt="Bakerista Circle"
                 class="w-40 md:w-52 lg:w-60 drop-shadow-lg mb-6 lg:mb-0">

            {{-- テキスト --}}
            <div class="text-center lg:text-left">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold leading-snug mb-4">
                    パンを愛する仲間と、<br class="sm:hidden">つながろう。
                </h1>
                <p class="text-base sm:text-lg text-gray-700 leading-relaxed mb-6">
                    ベーカリスタサークルは、パン作りを愛する人たちが集う<br class="hidden sm:block">
                    “学びと交流” のオンラインコミュニティ。<br>
                    全国の職人・ホームベーカーと一緒に、<br class="hidden sm:block">
                    あなたの「焼く」をもっと自由に。
                </p>

                <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4 mb-4">
                    <a href="#register-form" class="btn btn-primary btn-sm sm:btn-md md:btn-lg shadow-lg">
                        今すぐ有料会員になる
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline btn-sm sm:btn-md md:btn-lg">
                        すでに会員の方はこちら
                    </a>
                </div>

                <p class="text-xs sm:text-sm text-gray-500">
                    月額 ¥3,300（税込）で始めるプレミアムメンバーシップ。<br class="hidden sm:block">
                    いつでもキャンセル可能・オンラインで登録完結。
                </p>
            </div>
        </div>
    </section>

    {{-- コンセプトセクション --}}
    <section class="py-20 bg-base-200 px-6">
        <div class="max-w-5xl mx-auto text-center space-y-10">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-800">
                ベーカリスタサークルとは？
            </h2>
            <p class="text-base sm:text-lg text-gray-600 leading-relaxed max-w-3xl mx-auto">
                北海道・室蘭発のクラフト小麦粉ブランド「ベーカリスタ」が運営する、<br class="hidden sm:block">
                パンを愛する人のための月額制コミュニティです。<br>
                毎月の特別講座、会員限定ライブ、交流ルームなどを通して、<br>
                “パン作りを暮らしの真ん中に” をテーマに活動しています。
            </p>

            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3 mt-10">
                <div class="card bg-white shadow-md border hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="card-body">
                        <h3 class="card-title text-lg sm:text-xl">🧑‍🍳 仲間とつながる</h3>
                        <p>全国のベーカーと出会える。ルームで情報交換や作品共有ができます。</p>
                    </div>
                </div>

                <div class="card bg-white shadow-md border hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="card-body">
                        <h3 class="card-title text-lg sm:text-xl">🎥 学ぶ・体験する</h3>
                        <p>講師陣によるオンライン講座、ライブ配信イベントなどを毎月開催。</p>
                    </div>
                </div>

                <div class="card bg-white shadow-md border hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="card-body">
                        <h3 class="card-title text-lg sm:text-xl">📦 会員特典</h3>
                        <p>クラフト小麦粉の限定販売・先行予約・特別割引などの特典をご用意。</p>
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
                今すぐ参加して、<br class="sm:hidden">仲間とつながろう。
            </h2>
            <p class="text-base sm:text-lg text-white/90 mb-12">
                下記フォームから会員登録を完了すると、<br class="hidden sm:block">
                すぐにベーカリスタサークルのすべての機能をご利用いただけます。
            </p>

            {{-- 登録フォーム --}}
            <div class="mx-auto max-w-md bg-white/95 backdrop-blur-md shadow-2xl rounded-2xl p-6 sm:p-8 border border-white/40">
                <form method="POST" action="{{ route('register') }}" class="space-y-5 text-left text-gray-700">
                    @csrf

                    {{-- 名前 --}}
                    <div>
                        <x-input-label for="name" :value="__('お名前')" class="text-gray-800 font-semibold" />
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
                            {{ __('ベーカリスタサークルに参加する') }}
                        </button>
                    </div>

                    {{-- ログインリンク --}}
                    <div class="text-center mt-6">
                        <a href="{{ route('login') }}" class="link link-hover text-sm text-primary">
                            すでに会員の方はこちらからログイン
                        </a>
                    </div>
                </form>
            </div>

            <p class="mt-10 text-sm text-white/90">
                月額 <span class="font-bold text-white">¥3,300（税込）</span> — いつでもキャンセル可能・オンラインで登録完結。
            </p>
        </div>
    </section>

    {{-- フッター --}}
    <footer class="footer footer-center p-6 bg-base-300 text-base-content text-xs sm:text-sm">
        <p>© {{ date('Y') }} ベーカリスタ株式会社 — Bakerista Inc.</p>
    </footer>

</body>
</html>
