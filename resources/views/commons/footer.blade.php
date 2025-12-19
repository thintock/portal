<footer class="footer footer-center p-10 bg-base-300 text-base-content text-xs sm:text-sm border-t border-base-200">
    <nav class="flex flex-wrap justify-center gap-6 sm:gap-10 mb-4">
        <a href="https://bakerista.jp/about-us" class="link link-hover hover:text-primary transition" target="_blank">
            会社概要
        </a>
        <a href="{{ url('/pages/legal') }}" class="link link-hover hover:text-primary transition">
            特定商取引法に基づく表記
        </a>
        <a href="{{ url('/pages/guide') }}" class="link link-hover hover:text-primary transition">
            ユーザーガイド
        </a>
        {{-- ▼ サブスク会員のみ表示 --}}
        @auth
            @if(auth()->user()->subscribed('default'))
                <a href="{{ url('/pages/guideline') }}" class="link link-hover hover:text-primary transition">
                    コミュニティガイドライン
                </a>
            @endif
        @endauth
        <a href="{{ url('/pages/terms') }}" class="link link-hover hover:text-primary transition">
            ご利用規約
        </a>
        <a href="https://bakerista.jp/privacy" class="link link-hover hover:text-primary transition" target="_blank">
            プライバシーポリシー
        </a>
    </nav>

    <p class="text-gray-500">
        © {{ date('Y') }} ベーカリスタ株式会社 — Bakerista Mills Corp.
    </p>
</footer>
