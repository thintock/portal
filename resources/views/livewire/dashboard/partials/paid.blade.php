<div>
  @if($showPaidIntroBanners)

  {{-- PC時のみ横幅80%、モバイルはフル幅 --}}
  <div class="w-full sm:w-4/5 mx-auto">

    {{-- 導入メッセージ --}}
    <div class="mb-4 ">
      <p class="text-sm sm:text-base text-gray-700 leading-relaxed">
        ベイクルにご入会いただいたみなさまへ。<br class="sm:hidden">
        <span class="font-semibold">
          この2つのページは、はじめに必ず目を通してほしい大切な内容です。
        </span><br>
        このバナーは
        <span class="font-semibold">入会から一定期間</span>
        表示されますが、<br class="hidden sm:block">
        その後もフッターメニューや右上メニューから、いつでもご確認いただけます。
      </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <a href="{{ route('pages.show', 'omoi') }}" class="btn btn-secondary w-full">ごあいさつ</a>
      <a href="{{ route('pages.show', 'guideline') }}" class="btn btn-accent w-full">コミュニティガイドライン</a>
    </div>
    {{-- 会員情報入力のお願いカード（新規追加） --}}
        <div class="text-center space-y-4 mt-4">
  
            {{-- 見出し --}}
            <h2 class="text-xl sm:text-2xl font-bold text-primary">
                まずはプロフィールを完成させましょう
            </h2>
  
            {{-- 説明 --}}
            <p class="text-gray-600 leading-relaxed max-w-2xl mx-auto">
                毎月月末時点のご登録住所に<br class="hidden sm:block">
                翌月に商品を発送いたします。<br class="hidden sm:block">
                お名前・住所などの会員情報の入力をお願いします。
            </p>
  
            {{-- CTA --}}
            <div class="pt-2">
                <a href="{{ route('profile.edit') }}"
                   class="btn btn-primary btn-wide">
                    会員情報を編集する
                </a>
            </div>
    </div>
  </div>

  @endif
</div>
