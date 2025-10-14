@section('title', 'ご利用状況')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ご利用状況
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6">
                @if (session('status'))
                    <div class="alert alert-success mb-4">{{ session('status') }}</div>
                @endif

                {{-- =======================================
                    ① サブスク未契約（ベーカリスタサークル加入ページ）
                ======================================= --}}
                @if(!$subscription)
                <div class="relative bg-gradient-to-b from-[#F9F7F2] to-white p-4 sm:p-8 rounded-2xl shadow-xl border border-[#E8E2D9]">
                    {{-- 背景装飾（薄い小麦モチーフ） --}}
                    <div class="absolute inset-0 opacity-10 bg-[url('/images/wheat_pattern.svg')] bg-cover bg-center rounded-2xl pointer-events-none"></div>
                
                    {{-- ヘッダー --}}
                    <div class="relative text-center mb-6">
                        <div class="inline-block bg-[#EADBC8] text-[#3C3C3C] text-sm font-semibold tracking-widest px-4 py-1 rounded-full uppercase shadow-sm">
                            bakerista circle
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-[#3C3C3C] mt-3">ベーカリスタサークルへようこそ</h2>
                        <p class="text-[#6C6C6C] mt-2 text-sm sm:text-base leading-relaxed">
                            パンづくりを愛する仲間とつながり、学び、表現する。<br>
                            あなたの“パンのある生き方”をここから広げていきましょう。
                        </p>
                    </div>
                
                    {{-- プラン選択フォーム --}}
                    <form method="POST" action="{{ route('billing.subscribe') }}" class="relative space-y-5 mt-8">
                        @csrf
                
                        <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-[#E8E2D9] shadow-sm">
                            <label class="block text-left">
                                <!--<span class="font-semibold text-[#3C3C3C]">プランを選択</span>-->
                                <select name="price" class="select select-bordered w-full mt-2 text-[#3C3C3C] bg-white" required>
                                    @if ($prices['basic'] ?? false)
                                        <option value="{{ $prices['basic'] }}">サークル会員（月額）</option>
                                    @endif
                                    @if ($prices['premium'] ?? false)
                                        <option value="{{ $prices['premium'] }}">未使用</option>
                                    @endif
                                </select>
                            </label>
                        </div>
                
                        {{-- CTAボタン --}}
                        <button type="submit" class="btn w-full bg-[#3C3C3C] hover:bg-[#5A5A5A] text-white text-lg font-semibold py-3 rounded-xl transition-all duration-300">
                            サークルに参加する
                        </button>
                
                        {{-- 補足メッセージ --}}
                        <p class="text-center text-xs text-[#8C8C8C] mt-3">
                            ※ 外部サイト（Stripe）にて安全に決済を行います。
                        </p>
                    </form>
                {{-- 下部装飾エリア --}}
                    <div class="relative mt-10 text-center">
                        <p class="text-sm text-[#7C7C7C] italic">
                            「Slow, Small, Simple, Sustainable」<br>
                            ベーカリスタサークルは、ゆっくりと、ていねいに、<br class="sm:hidden">パンと生きる人を応援します。
                        </p>
                    </div>
                </div>

                {{-- =======================================
                    ② 現在契約中（active, ends_at null）
                ======================================= --}}
                @elseif($subscription->stripe_status === 'active' && is_null($subscription->ends_at))
                <div class="relative bg-gradient-to-br from-[#FFF9F2] via-[#FAF3E5] to-[#FDFBF7] p-4 sm:p-8 rounded-2xl shadow-xl border border-[#E8E2D9] overflow-hidden">
                
                    {{-- 背景装飾（淡い光と粒子） --}}
                    <div class="absolute inset-0 bg-[url('/images/light_texture.svg')] opacity-10 bg-cover bg-center pointer-events-none"></div>
                    <div class="absolute -top-10 -left-10 w-40 h-40 bg-[#EADBC8]/40 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-[#F5DCC0]/30 rounded-full blur-3xl"></div>
                
                    {{-- ヘッダー --}}
                    <div class="relative text-center mb-8">
                        <div class="inline-block bg-[#EADBC8] text-[#3C3C3C] text-sm font-semibold tracking-widest px-4 py-1 rounded-full uppercase shadow-sm">
                            bakerista circle member
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-bold text-[#3C3C3C] mt-3">
                            ようこそ、彩りあふれる毎日へ。
                        </h2>
                        <p class="text-[#6C6C6C] mt-3 text-sm sm:text-base leading-relaxed">
                            今日も誰かのパンが、誰かの笑顔になる。<br>
                            あなたの手から生まれる香りが、この世界を少し幸せにします。
                        </p>
                    </div>
                
                    {{-- 現在のプラン情報 --}}
                    <div class="relative bg-white/80 backdrop-blur-sm border border-[#E8E2D9] rounded-xl p-6 shadow-sm">
                        <p class="text-2xl font-bold text-[#3C3C3C] text-center">
                            @if($subscription->stripe_price === ($prices['basic'] ?? ''))
                                サークル会員
                            @elseif($subscription->stripe_price === ($prices['premium'] ?? ''))
                                未使用
                            @else
                                ご契約中プラン
                            @endif
                        </p>
                        <p class="text-sm text-[#7C7C7C] text-center mt-2">
                            あなたのメンバーシップは現在有効です。
                        </p>
                    </div>
                
                    {{-- 支払い情報ボタン --}}
                    <div class="relative mt-8">
                        <a href="{{ route('billing.portal') }}" 
                           class="btn w-full bg-[#3C3C3C] hover:bg-[#5A5A5A] text-white text-lg font-semibold py-3 rounded-xl transition-all duration-300 shadow-md">
                            支払い情報の確認・変更
                        </a>
                    </div>
                
                    {{-- メッセージエリア --}}
                    <div class="relative mt-10 text-center">
                        <p class="text-[#6C6C6C] text-sm leading-relaxed">
                            ベーカリスタサークルは、あなたの「好き」を応援する仲間たちと、<br class="sm:hidden">
                            新しいアイデアや出会いを育む場所です。<br>
                            小麦のように、ゆっくりと、しなやかに、あなたらしく。
                        </p>
                    </div>
                </div>
                {{-- =======================================
                    ③ 解約予定（active, ends_atあり）
                ======================================= --}}
                @elseif($subscription->stripe_status === 'active' && !is_null($subscription->ends_at))
                <div class="relative bg-gradient-to-b from-[#FFFBF6] via-[#FFF6EC] to-[#FDFBF8] p-8 rounded-2xl shadow-xl border border-[#E8E2D9] overflow-hidden">
                
                    {{-- 背景装飾（柔らかな光と粒子） --}}
                    <div class="absolute inset-0 bg-[url('/images/light_texture.svg')] opacity-10 bg-cover bg-center pointer-events-none"></div>
                    <div class="absolute -top-10 -left-10 w-40 h-40 bg-[#F4DEC7]/40 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-[#F5DCC0]/30 rounded-full blur-3xl"></div>
                
                    {{-- ヘッダー --}}
                    <div class="relative text-center mb-8">
                        <div class="inline-block bg-[#EADBC8] text-[#3C3C3C] text-sm font-semibold tracking-widest px-4 py-1 rounded-full uppercase shadow-sm">
                            bakerista circle
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-bold text-[#3C3C3C] mt-3">
                            また、あなたと歩みたい。
                        </h2>
                        <p class="text-[#6C6C6C] mt-3 text-sm sm:text-base leading-relaxed">
                            パンを焼く香りも、仲間との会話も、<br>
                            いつでもここに戻ってこられる場所があります。
                        </p>
                    </div>
                
                    {{-- 現在のプラン情報 --}}
                    <div class="relative bg-white/80 backdrop-blur-sm border border-[#E8E2D9] rounded-xl p-6 shadow-sm mb-6">
                        <p class="font-semibold text-[#3C3C3C] mb-2 text-center">現在のご契約プラン</p>
                        <p class="text-2xl font-bold text-[#3C3C3C] text-center">
                            @if($subscription->stripe_price === ($prices['basic'] ?? ''))
                                サークル会員
                            @elseif($subscription->stripe_price === ($prices['premium'] ?? ''))
                                未使用
                            @else
                                ご契約中プラン
                            @endif
                        </p>
                        <p class="text-sm text-[#7C7C7C] text-center mt-2">
                            ご契約は <strong class="text-[#3C3C3C]">{{ $subscription->ends_at?->format('Y年m月d日') }}</strong> に終了予定です。
                        </p>
                    </div>
                
                    {{-- 契約継続の案内ボックス --}}
                    <div class="relative bg-[#FFF6E5] border border-[#F4DDB4] rounded-xl p-5 mb-6 text-center shadow-sm">
                        <p class="text-[#7C5E2E] text-sm sm:text-base leading-relaxed font-medium">
                            解約を取り消して<br class="sm:hidden">ベーカリスタサークルを続けませんか？
                        </p>
                        <form method="POST" action="{{ route('billing.portal') }}" class="mt-4">
                            @csrf
                            <button class="btn bg-[#3C3C3C] hover:bg-[#5A5A5A] text-white font-semibold px-6 py-2 rounded-lg transition-all duration-300">
                                ご契約を継続する
                            </button>
                        </form>
                        <p class="text-xs text-[#8C7B64] mt-3 italic">
                            *ご契約が終了すると、会員番号がリセットされます。
                            *再契約すると、これまでの特典とコミュニティアクセスがそのまま引き継がれます。
                        </p>
                    </div>
                
                    {{-- 支払い情報ボタン --}}
                    <div class="relative">
                        <a href="{{ route('billing.portal') }}" 
                           class="btn w-full bg-white text-[#3C3C3C] border border-[#E8E2D9] hover:bg-[#F8F4EE] text-lg font-semibold py-3 rounded-xl transition-all duration-300 shadow-sm">
                            支払い情報の確認・変更
                        </a>
                    </div>
                
                    {{-- メッセージエリア --}}
                    <div class="relative mt-10 text-center">
                        <p class="text-[#6C6C6C] text-sm leading-relaxed">
                            あなたのパンが、誰かの一日をやさしく変えている。<br>
                            その小さな奇跡を、これからも一緒に紡いでいけますように。
                        </p>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
