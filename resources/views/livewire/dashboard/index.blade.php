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
          @include('livewire.dashboard.partials.monthly')
          <livewire:events.section />
          @include('livewire.dashboard.partials.google')
        @elseif($user->role === 'guest')
          @include('livewire.dashboard.partials.guest')
          @include('livewire.dashboard.partials.room')
          <livewire:events.section />
        @elseif($user->subscribed('default'))
          @include('livewire.dashboard.partials.paid')
          @include('livewire.dashboard.partials.room')
          @include('livewire.dashboard.partials.monthly')
          <livewire:events.section />
          @include('livewire.dashboard.partials.google')
        @else
          @include('livewire.dashboard.partials.free')
        @endif
    @endauth
    

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
