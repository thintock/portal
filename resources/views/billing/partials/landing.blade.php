{{-- resources/views/billing/partials/landing.blade.php --}}

{{-- ヒーローセクション --}}
<section class="hero min-h-[90vh] bg-gradient-to-br from-primary/20 via-base-100 to-accent/10 px-6 md:px-10">
    <div class="hero-content flex flex-col lg:flex-row-reverse items-center gap-10 max-w-6xl mx-auto py-10">
        {{-- 画像 --}}
        <div class="w-20 h-20 flex items-center justify-center">
            <img src="{{ asset('images/bakele_logo_rv.svg') }}" alt="ベイクル ロゴ" class="w-full h-full">
        </div>
        {{-- テキスト --}}
        <div class="text-center lg:text-left">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold leading-snug mb-4">
                パンづくりを愛する仲間と、<br class="sm:hidden">つながろう。
            </h1>
            <p class="text-base sm:text-lg text-gray-700 leading-relaxed mb-6">
                ベーカリスタサークルは、パン作りを愛する人たちが集う<br class="hidden sm:block">
                “学びと交流” のオンラインコミュニティ。<br>
                全国の職人・ホームベーカーと一緒に、<br class="hidden sm:block">
                あなたの「焼く」をもっと自由に。
            </p>

            {{-- ▼ 募集中 --}}
                <div class="flex justify-center lg:justify-start mb-4">
                    <a href="#register-form"
                       class="btn btn-primary btn-lg shadow-lg w-full sm:w-auto">
                        今すぐ有料会員になる
                    </a>
                </div>
                <p class="text-sm text-gray-500">
                    募集期間：毎月25日12:00〜月末
                </p>

            <p class="text-xs sm:text-sm text-gray-500">
                月額 ¥2,980（税込）で始めるプレミアムメンバーシップ。<br class="hidden sm:block">
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
    
        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))]
                    from-white/50 via-transparent to-transparent blur-3xl"></div>
    
        <div class="relative max-w-4xl mx-auto px-6 text-center">
    
            {{-- ▼ 募集後：申込フォーム --}}
            <div id="recruiting-form" class="{{ $isRecruiting ? '' : 'hidden' }}">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-600 mb-4">
                    今すぐ参加して<br class="sm:hidden">仲間とつながろう
                </h2>
    
                <p class="text-base sm:text-lg text-gray-600 mb-12">
                    下記のフォームからStripe決済を完了すると、<br class="hidden sm:block">
                    すぐにベーカリスタサークルのすべての機能をご利用いただけます。
                </p>
    
                <div class="mx-auto max-w-md bg-white/95 backdrop-blur-md shadow-2xl rounded-2xl
                            p-6 sm:p-8 border border-white/40">
                    <form method="POST" action="{{ route('billing.subscribe') }}"
                          class="space-y-5 text-left text-gray-700">
                        @csrf
    
                        <div>
                            <x-input-label for="price" :value="__('プランを選択')"
                                           class="text-gray-800 font-semibold" />
                            <select name="price" id="price"
                                    class="select select-bordered w-full mt-1
                                           focus:ring-2 focus:ring-primary bg-white text-gray-800">
                                @if ($prices['basic'] ?? false)
                                    <option value="{{ $prices['basic'] }}">
                                        サークル会員（月額 ¥2,980）
                                    </option>
                                @endif
                            </select>
                        </div>
    
                        <div class="pt-6">
                            <button type="submit"
                                    class="btn btn-accent w-full text-white shadow-lg
                                           hover:shadow-xl transition btn-sm md:btn-lg">
                                Stripe決済に進む
                            </button>
                        </div>
                    </form>
                </div>
    
                <p class="mt-10 text-sm text-gray-600">
                    月額 <span class="font-bold text-error">¥2,980（税込）</span>
                    — いつでもキャンセルできます。
                </p>
            </div>
    
            {{-- ▼ 募集前：カウントダウン --}}
            <div id="countdown-block" class="{{ $isRecruiting ? 'hidden' : '' }}">
                <p class="text-sm text-gray-600 mb-2">
                    ベイクルの会員募集期間は毎月25日12:00から月末22:59までです。
                </p>
    
                <p class="text-md text-gray-600 mb-2 font-bold">
                    次回募集開始まで
                </p>
    
                <div class="mx-auto max-w-md bg-white/95 backdrop-blur-md shadow-2xl rounded-2xl
                            p-6 sm:p-8 border border-white/40">
                    <div id="countdown"
                         class="flex justify-center gap-3 text-2xl sm:text-4xl
                                font-bold text-primary animate-pulse">
                        <span><span id="days">--</span>日</span>
                        <span><span id="hours">--</span>時間</span>
                        <span><span id="minutes">--</span>分</span>
                        <span><span id="seconds">--</span>秒</span>
                    </div>
                </div>
    
                <p class="mt-3 text-xs text-gray-600 font-bold">
                    毎月25日 12:00 に募集を開始します
                </p>
            </div>
    
        </div>
    </section>

    {{-- カウントダウンJS --}}
    @if(!$isRecruiting && $nextRecruitingAt)
<script>
    const target = new Date("{{ $nextRecruitingAt->format('Y-m-d H:i:s') }}").getTime();

    const countdownBlock = document.getElementById('countdown-block');
    const recruitingForm = document.getElementById('recruiting-form');

    const timer = setInterval(() => {
        const now = new Date().getTime();
        const diff = target - now;

        if (diff <= 0) {
            clearInterval(timer);

            // UI切替
            countdownBlock.classList.add('hidden');
            recruitingForm.classList.remove('hidden');
            recruitingForm.classList.add('animate-fade-in');

            // 状態ズレ防止（保険）
            setTimeout(() => {
                location.reload();
            }, 8000);

            return;
        }

        document.getElementById('days').textContent =
            Math.floor(diff / (1000 * 60 * 60 * 24));
        document.getElementById('hours').textContent =
            Math.floor((diff / (1000 * 60 * 60)) % 24);
        document.getElementById('minutes').textContent =
            Math.floor((diff / (1000 * 60)) % 60);
        document.getElementById('seconds').textContent =
            Math.floor((diff / 1000) % 60);

    }, 1000);
</script>
@endif


