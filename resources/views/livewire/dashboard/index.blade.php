@section('title', 'ホーム')
<div class="px-1 pt-6 pb-8">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
    @if($page && $page->body1)
        <div class="prose max-w-none">
            {!! $page->body1 !!}
        </div>
    @endif
    <livewire:dashboard.partials.announcements />
    @auth
        @if($user->role === 'admin')
          @include('livewire.dashboard.partials.paid')
          @include('livewire.dashboard.partials.room')
          <livewire:events.section />
        @elseif($user->role === 'guest')
          @include('livewire.dashboard.partials.guest')
          @include('livewire.dashboard.partials.room')
          <livewire:events.section />
        @elseif($user->subscribed('default'))
          @include('livewire.dashboard.partials.paid')
          @include('livewire.dashboard.partials.room')
          <livewire:events.section />
        @else
          @include('livewire.dashboard.partials.free')
        @endif
    @endauth
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

    @if($page && $page->body2)
        <div class="prose max-w-none">
            {!! $page->body2 !!}
        </div>
    @endif
    
    @if($page && $page->body3)
        <div class="prose max-w-none">
            {!! $page->body3 !!}
        </div>
    @endif
    
  </div>
</div>
