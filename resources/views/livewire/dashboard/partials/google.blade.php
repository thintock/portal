<div class="card bg-base-100 shadow-sm">
  <div class="card-body p-4 sm:p-6">
    <h2 class="text-base sm:text-lg font-bold">イベントカレンダー</h2>

    <div class="mt-4 w-full overflow-hidden rounded-xl border border-base-200">
      {{-- mobile --}}
      <iframe
        title="Bakerista Calendar (Mobile)"
        src="https://calendar.google.com/calendar/embed?src=f9b4ca2a530dbf1925c391d6e9be13fd9fe8591297919aa0e03d74db7b6607ee%40group.calendar.google.com&ctz=Asia%2FTokyo&mode=MONTH"
        class="w-full block h-[50vh] sm:hidden"
        style="border:0"
        frameborder="0"
        scrolling="no"
        loading="lazy"
      ></iframe>
      <span class="w-full text-xs block text-right sm:hidden">プラスマークを押して追加すると☝️＋<br>ご自身のGoogleカレンダーに表示されます。<br>後で削除もできます。</span>
      {{-- desktop --}}
      <iframe
        title="Bakerista Calendar (Desktop)"
        src="https://calendar.google.com/calendar/embed?src=f9b4ca2a530dbf1925c391d6e9be13fd9fe8591297919aa0e03d74db7b6607ee%40group.calendar.google.com&ctz=Asia%2FTokyo&mode=MONTH"
        class="w-full hidden sm:block h-[600px]"
        style="border:0"
        frameborder="0"
        scrolling="no"
        loading="lazy"
      ></iframe>
      <span class="w-full text-xs hidden sm:block pl-6">☝️ ご自身のGoogleカレンダーに表示されます。後で削除もできます。</span>
    </div>
  </div>
</div>