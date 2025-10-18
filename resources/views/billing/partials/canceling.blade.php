<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        ご利用状況
    </h2>
</x-slot>

{{-- resources/views/billing/partials/cancelling.blade.php --}}
<div class="p-1 space-y-6">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
    <div class="relative bg-gradient-to-br from-[#FFF7F4] via-[#FAEFE7] to-[#FDFBF7] p-4 sm:p-8 rounded-2xl shadow-xl border border-[#E8E2D9] overflow-hidden">

        {{-- 背景装飾（柔らかな光と粒子） --}}
        <div class="absolute inset-0 bg-[url('/images/light_texture.svg')] opacity-10 bg-cover bg-center pointer-events-none"></div>
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-[#FDEBD2]/40 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-[#EBD5C0]/30 rounded-full blur-3xl"></div>

        {{-- ヘッダー --}}
        <div class="relative text-center mb-8">
            <div class="inline-block bg-base-300 text-sm font-semibold tracking-widest px-4 py-1 rounded-full uppercase shadow-sm">
                bakerista circle
            </div>
            <h2 class="text-3xl sm:text-4xl font-bold mt-3">
                また、あなたと歩みたい。
            </h2>
            <p class="text-[#6C6C6C] mt-3 text-sm sm:text-base leading-relaxed">
                パンを焼く香りも、仲間との会話も、<br>
                いつでもここに戻ってこられる場所があります。
            </p>
        </div>

        {{-- 現在のプラン情報 --}}
        <div class="relative bg-base-100 backdrop-blur-sm border border-[#E8E2D9] rounded-xl p-6 shadow-sm">
            <p class="text-2xl font-bold text-[#3C3C3C] text-center">
                @if($subscription->stripe_price === ($prices['basic'] ?? ''))
                    サークル会員
                @elseif($subscription->stripe_price === ($prices['premium'] ?? ''))
                    プレミアム会員
                @else
                    ご契約中プラン
                @endif
            </p>
            <p class="text-sm text-[#7C7C7C] text-center mt-2">
                ご契約は <span class="font-semibold text-[#3C3C3C]">{{ $subscription->ends_at?->format('Y年n月j日') }}</span> に終了予定です。
            </p>
        </div>

        {{-- 再契約ボタン --}}
        <div class="relative mt-8 text-center">
            <a href="{{ route('billing.portal') }}" 
               class="btn bg-[#3C3C3C] hover:bg-[#5A5A5A] text-white text-lg font-semibold py-3 rounded-xl transition-all duration-300 shadow-md">
                解約を取り消して続ける
            </a>
        </div>

        {{-- 注意文 --}}
        <div class="relative mt-5 text-center text-xs text-[#7C7C7C] space-y-1">
            <p>* ご契約が終了すると、会員番号がリセットされます。</p>
            <p>* 再契約すると、これまでの特典とコミュニティアクセスがそのまま引き継がれます。</p>
        </div>

        {{-- メッセージエリア --}}
        <div class="relative mt-10 text-center">
            <p class="text-[#6C6C6C] text-sm leading-relaxed">
                あなたのパンが、誰かの一日をやさしく変えている。<br>
                その小さな奇跡を、これからも一緒に紡いでいけますように。
            </p>
        </div>
    </div>
  </div>
</div>
