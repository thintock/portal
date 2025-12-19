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
        <span class="font-semibold">入会から1ヶ月間のみ</span>
        表示されますが、<br class="hidden sm:block">
        その後もフッターメニューや右上メニューから、いつでもご確認いただけます。
      </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <a href="{{ route('pages.show', 'omoi') }}" class="btn btn-secondary w-full">ごあいさつ</a>
      <a href="{{ route('pages.show', 'guideline') }}" class="btn btn-accent w-full">コミュニティガイドライン</a>

    </div>
  </div>

  @endif
</div>
