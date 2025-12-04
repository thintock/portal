{{-- resources/views/billing/partials/landing.blade.php --}}

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

            <div class="flex flex-col sm:flex-row justify-center gap-4 mb-4">
                <a href="#register-form" class="btn btn-primary btn-sm sm:btn-md md:btn-lg shadow-lg w-full">
                    今すぐ有料会員になる
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
            北海道発のクラフト小麦粉ブランド「ベーカリスタ」が運営する、<br class="hidden sm:block">
            パンづくりを愛する人のための月額制コミュニティです。<br>
            毎月の特別講座、会員限定ライブ、交流ルームなどを通して、<br>
            “パン作りを暮らしの真ん中に” をテーマに活動しています。
        </p>

        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3 mt-10">
            <div class="card bg-white shadow-md border hover:shadow-lg transition transform hover:-translate-y-1">
                <div class="card-body">
                    <h3 class="card-title text-lg sm:text-xl">仲間とつながる</h3>
                    <p>全国のベーカーと出会える。ルームで情報交換や作品共有ができます。</p>
                </div>
            </div>

            <div class="card bg-white shadow-md border hover:shadow-lg transition transform hover:-translate-y-1">
                <div class="card-body">
                    <h3 class="card-title text-lg sm:text-xl">学ぶ・体験する</h3>
                    <p>講師陣によるオンライン講座、ライブ配信イベントなどを毎月開催。</p>
                </div>
            </div>

            <div class="card bg-white shadow-md border hover:shadow-lg transition transform hover:-translate-y-1">
                <div class="card-body">
                    <h3 class="card-title text-lg sm:text-xl">会員特典</h3>
                    <p>クラフト小麦粉の限定販売・先行予約・特別割引などの特典をご用意。</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Stripeプラン選択フォーム --}}
<section id="register-form" class="py-24 bg-gradient-to-br from-primary/40 via-accent/30 to-secondary/40 text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-20 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/50 via-transparent to-transparent blur-3xl"></div>

    <div class="relative max-w-4xl mx-auto px-6 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold mb-4 drop-shadow-lg">
            今すぐ参加して、<br class="sm:hidden">仲間とつながろう。
        </h2>
        <p class="text-base sm:text-lg text-white/90 mb-12">
            下記のフォームからStripe決済を完了すると、<br class="hidden sm:block">
            すぐにベーカリスタサークルのすべての機能をご利用いただけます。
        </p>

        <div class="mx-auto max-w-md bg-white/95 backdrop-blur-md shadow-2xl rounded-2xl p-6 sm:p-8 border border-white/40">
            <form method="POST" action="{{ route('billing.subscribe') }}" class="space-y-5 text-left text-gray-700">
                @csrf
                <div>
                    <x-input-label for="price" :value="__('プランを選択')" class="text-gray-800 font-semibold" />
                    <select name="price" id="price" class="select select-bordered w-full mt-1 focus:ring-2 focus:ring-primary bg-white text-gray-800">
                        @if ($prices['basic'] ?? false)
                            <option value="{{ $prices['basic'] }}">サークル会員（月額 ¥2,980）</option>
                        @endif
                    </select>
                </div>
                <div class="pt-6">
                    <button type="submit" class="btn btn-accent w-full text-white shadow-lg hover:shadow-xl transition btn-sm md:btn-lg">
                        {{ __('Stripe決済に進む') }}
                    </button>
                </div>
            </form>
        </div>
        <p class="mt-10 text-sm text-white/90">
            月額 <span class="font-bold text-white">¥2,980（税込）</span> — いつでもキャンセルできます。再入会も簡単。
        </p>
    </div>
</section>
