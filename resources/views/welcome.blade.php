<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ベーカリスタサークル | 仲間とつながる、パン好きのための場所</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-base-100 text-base-content">

    {{-- ヒーローセクション --}}
    <section class="hero min-h-screen bg-gradient-to-br from-primary/20 via-base-100 to-accent/10">
        <div class="hero-content flex-col lg:flex-row-reverse max-w-6xl">
            {{-- 画像 --}}
            <img src="{{ asset('images/bakerista_circle_gray.png') }}" width="180px" alt="Bakerista Circle" class="drop-shadow-lg">

            {{-- テキスト --}}
            <div>
                <h1 class="text-4xl lg:text-5xl font-bold mb-4 leading-tight">
                    パンを愛する仲間とつながろう。
                </h1>
                <p class="py-4 text-lg leading-relaxed text-gray-700">
                    ベーカリスタサークルは、パン作りを愛する人たちが集う<br>
                    “学びと交流” のオンラインコミュニティ。<br>
                    全国の職人・ホームベーカーと一緒に、<br>
                    あなたの「焼く」をもっと自由に。
                </p>

                <div class="flex flex-wrap gap-4 mt-6">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg shadow-lg">
                        今すぐ有料会員になる
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline btn-lg">
                        すでに会員の方はこちら
                    </a>
                </div>

                <p class="text-xs text-gray-500 mt-3">月額 ¥3,300（税込）で始めるプレミアムメンバーシップ。<br>いつでもキャンセル可能・オンラインで登録完結</p>
            </div>
        </div>
    </section>

    {{-- コンセプトセクション --}}
    <section class="py-20 bg-base-200">
        <div class="max-w-5xl mx-auto text-center space-y-8">
            <h2 class="text-3xl font-bold">ベーカリスタサークルとは？</h2>
            <p class="text-lg text-gray-600 leading-relaxed">
                北海道・室蘭発のクラフト小麦粉ブランド「ベーカリスタ」が運営する、<br>
                パンを愛する人のための月額制コミュニティです。<br>
                毎月の特別講座、会員限定ライブ、交流ルームなどを通して、<br>
                “パン作りを暮らしの真ん中に” をテーマに活動しています。
            </p>

            <div class="grid md:grid-cols-3 gap-8 text-left">
                <div class="card bg-white shadow-md border hover:shadow-lg transition">
                    <div class="card-body">
                        <h3 class="card-title text-lg">🧑‍🍳 仲間とつながる</h3>
                        <p>全国のベーカーと出会える。ルームで情報交換や作品共有ができます。</p>
                    </div>
                </div>

                <div class="card bg-white shadow-md border hover:shadow-lg transition">
                    <div class="card-body">
                        <h3 class="card-title text-lg">🎥 学ぶ・体験する</h3>
                        <p>講師陣によるオンライン講座、ライブ配信イベントなどを毎月開催。</p>
                    </div>
                </div>

                <div class="card bg-white shadow-md border hover:shadow-lg transition">
                    <div class="card-body">
                        <h3 class="card-title text-lg">📦 会員特典</h3>
                        <p>クラフト小麦粉の限定販売・先行予約・特別割引などの特典をご用意。</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTAセクション --}}
    <section class="py-24 bg-gradient-to-r from-primary/40 to-secondary/50 text-center text-white">
        <div class="max-w-3xl mx-auto">
            <h2 class="text-4xl font-bold mb-6 leading-relaxed">
                パンをもっと楽しみ、<br class="hidden md:block">仲間とつながる新しい体験を。
            </h2>
            <p class="mb-8 text-lg">
                月額 ¥3,300（税込）で始めるプレミアムメンバーシップ。<br>
                今すぐオンラインで登録できます。いつでもキャンセル可能。
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('billing.show') }}" class="btn btn-accent btn-lg text-white shadow-lg">
                    有料会員登録をする
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline btn-lg text-white border-white">
                    すでに会員の方はこちら
                </a>
            </div>
        </div>
    </section>

    {{-- フッター --}}
    <footer class="footer footer-center p-6 bg-base-300 text-base-content text-sm">
        <p>© {{ date('Y') }} ベーカリスタ株式会社 — Bakerista Inc.</p>
    </footer>

</body>
</html>
