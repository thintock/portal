<div class="space-y-6">

  {{-- ベイクル案内カード（集約版） --}}
  <div class="card bg-base-100 shadow-md border">
      <div class="card-body text-center space-y-5">

          {{-- 見出し --}}
          <div class="flex items-center justify-center gap-3">
            <div class="w-10 h-10 rounded-full overflow-hidden bg-base-200 flex items-center justify-center">
                <img src="{{ asset('images/bakele_logo.png') }}" alt="ベイクル ロゴ" class="w-full h-full object-cover">
            </div>
            <h2 class="text-2xl font-bold">
                ベイクルのご案内
            </h2>
          </div>
          {{-- 説明 --}}
          <p class="text-gray-600 leading-relaxed max-w-2xl mx-auto">
              ベーカリスタサークル（ベイクル）は、パンづくりを愛する人たちが集まる<br class="hidden sm:block">
              「学び」と「交流」を大切にしたオンラインコミュニティです。<br>
          </p>

          {{-- CTA --}}
          <div class="pt-4">
              <a href="{{ route('billing.show') }}"
                 class="btn btn-primary btn-wide">
                  ベイクルに興味がある方はこちら
              </a>
          </div>

          {{-- 注記 --}}
          <p class="text-xs text-gray-500">
              ※ 募集は毎月25日12:00から月末までの期間限定です
          </p>
      </div>
  </div>

</div>
