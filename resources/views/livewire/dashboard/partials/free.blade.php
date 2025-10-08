<div class="space-y-6">

  {{-- 現在ステータス --}}
  <div class="alert alert-info">
    現在：<b>無料会員様</b>
  </div>

  {{-- 見出し --}}
  <div class="bg-white shadow-sm sm:rounded-lg p-8 text-center">
    <h2 class="text-2xl font-bold mb-3">✨ ベーカリスタサークル 有料メンバーのご案内</h2>
    <p class="text-gray-600 leading-relaxed max-w-2xl mx-auto">
      ベーカリスタサークルは、パンづくりを愛する人たちが集う特別なコミュニティ。<br>
      有料メンバーになると、限定ルームへの参加や、製粉工場からの特別配信、<br>
      会員限定の素材・レシピ情報など、より深く学べる体験が待っています。
    </p>
  </div>

  {{-- 特典カード群 --}}
  <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      {{-- 限定ルーム --}}
      <div class="card bg-base-100 shadow-md border overflow-hidden">
          <div class="w-full h-32 bg-gradient-to-r from-primary/40 to-secondary/30 flex items-center justify-center text-5xl text-white">
              💬
          </div>
          <div class="card-body">
              <h3 class="card-title text-lg">限定ルーム参加</h3>
              <p class="text-sm text-gray-600 mt-1">
                  プロ・愛好家が交流するルームに参加可能。質問・相談・作品共有など。
              </p>
          </div>
      </div>

      {{-- 特別イベント --}}
      <div class="card bg-base-100 shadow-md border overflow-hidden">
          <div class="w-full h-32 bg-gradient-to-r from-accent/40 to-primary/40 flex items-center justify-center text-5xl text-white">
              🎥
          </div>
          <div class="card-body">
              <h3 class="card-title text-lg">限定イベント配信</h3>
              <p class="text-sm text-gray-600 mt-1">
                  石臼製粉のライブ配信や、小麦づくりの現場を体験できる限定動画。
              </p>
          </div>
      </div>

      {{-- 会員限定素材 --}}
      <div class="card bg-base-100 shadow-md border overflow-hidden">
          <div class="w-full h-32 bg-gradient-to-r from-warning/40 to-error/30 flex items-center justify-center text-5xl text-white">
              🌾
          </div>
          <div class="card-body">
              <h3 class="card-title text-lg">限定素材・レシピ</h3>
              <p class="text-sm text-gray-600 mt-1">
                  会員だけが購入できる限定クラフト小麦や、特別レシピ情報をお届け。
              </p>
          </div>
      </div>
  </div>

  {{-- CTAセクション --}}
  <div class="text-center py-10">
      <h3 class="text-xl font-semibold mb-3">月額 ¥3,300（税込）で仲間とつながろう</h3>
      <p class="text-gray-600 mb-6">すぐにオンラインで登録できます。いつでもキャンセル可能。</p>
      <form method="POST" action="{{ route('billing.subscribe') }}" class="space-y-4 text-center">
                  @csrf
                  {{-- ベーシックプラン固定 --}}
                  <input type="hidden" name="price" value="{{ config('services.stripe.prices.basic') }}">

                  <p class="text-gray-700 mb-2">
                      <b>ベーシックプラン</b><br>
                  </p>

                  <button type="submit" class="btn btn-primary w-full md:w-64 text-lg">
                      申し込む
                  </button>
              </form>
      <p class="text-xs text-gray-500 mt-2">
          ※ Stripeによる安全な決済システムを使用しています
      </p>
  </div>

</div>
