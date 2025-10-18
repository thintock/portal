<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        ご利用状況
    </h2>
</x-slot>
{{-- resources/views/billing/partials/active.blade.php --}}
<div class="p-1 space-y-6">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
    <div class="relative bg-gradient-to-br from-[#FFF9F2] via-[#FAF3E5] to-[#FDFBF7] p-4 sm:p-8 rounded-2xl shadow-xl border border-[#E8E2D9] overflow-hidden">
    
        {{-- 背景装飾（淡い光と粒子） --}}
        <div class="absolute inset-0 bg-[url('/images/light_texture.svg')] opacity-10 bg-cover bg-center pointer-events-none"></div>
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-base-200/40 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-[#F5DCC0]/30 rounded-full blur-3xl"></div>
    
        {{-- ヘッダー --}}
        <div class="relative text-center mb-8">
            <div class="inline-block bg-base-300 text-sm font-semibold tracking-widest px-4 py-1 rounded-full uppercase shadow-sm">
                bakerista circle member
            </div>
            <h2 class="text-3xl sm:text-4xl font-bold mt-3">
                ようこそ、彩りあふれる毎日へ。
            </h2>
            <p class="text-[#6C6C6C] mt-3 text-sm sm:text-base leading-relaxed">
                今日も誰かのパンが、誰かの笑顔になる。<br>
                あなたの手から生まれる香りが、この世界を少し幸せにします。
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
                あなたのメンバーシップは現在有効です。
            </p>
        </div>
    
        {{-- 支払い情報ボタン --}}
        <div class="relative mt-8 text-center">
            <a href="{{ route('billing.portal') }}" 
               class="btn bg-[#3C3C3C] hover:bg-[#5A5A5A] text-white text-lg font-semibold py-3 rounded-xl transition-all duration-300 shadow-md">
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
  </div>
</div>
