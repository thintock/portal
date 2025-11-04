<footer class="footer footer-center p-10 bg-base-300 text-base-content text-xs sm:text-sm border-t border-base-200">
    <nav class="flex flex-wrap justify-center gap-6 sm:gap-10 mb-4">
        <a href="{{ url('/pages/about') }}" class="link link-hover hover:text-primary transition">
            会社概要
        </a>
        <a href="{{ url('/pages/legal') }}" class="link link-hover hover:text-primary transition">
            特定商取引法に基づく表記
        </a>
        <a href="{{ url('/pages/guide') }}" class="link link-hover hover:text-primary transition">
            ユーザーガイド
        </a>
        <a href="{{ url('/pages/terms') }}" class="link link-hover hover:text-primary transition">
            ご利用規約
        </a>
        <a href="{{ url('/pages/privacy') }}" class="link link-hover hover:text-primary transition">
            個人情報の取り扱いについて
        </a>
    </nav>

    <p class="text-gray-500">
        © {{ date('Y') }} ベーカリスタ株式会社 — Bakerista Mills Corp.
    </p>
</footer>
