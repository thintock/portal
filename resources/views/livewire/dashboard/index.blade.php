@section('title', 'ホーム')
<div class="p-1 space-y-6">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
    {{-- 現在のステータス表示 --}}
    @auth
        @if($user->role === 'admin')
          @include('livewire.dashboard.partials.admin')
          @include('livewire.dashboard.partials.room')
        @elseif($user->role === 'guest')
          @include('livewire.dashboard.partials.guest')
          @include('livewire.dashboard.partials.room')
        @elseif($user->subscribed('default'))
          @include('livewire.dashboard.partials.paid')
          @include('livewire.dashboard.partials.room')
        @else
          @include('livewire.dashboard.partials.free')
        @endif
    @endauth

    {{-- アクティビティカード群 --}}
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    
      {{-- アクティビティ状況 --}}
      <div class="card bg-base-100 shadow-md border overflow-hidden">
          {{-- カバーエリア --}}
          <div class="w-full h-32 bg-gradient-to-r from-primary/40 to-secondary/40 flex items-center justify-center text-5xl text-white">
              📊
          </div>
          <div class="card-body">
              <h3 class="card-title text-lg">アクティビティ状況</h3>
              <p class="text-sm text-gray-600 mt-1">
                  投稿・コメント・リアクションの状況を確認できます。
              </p>
              <div class="flex justify-end mt-3">
                  <span class="badge badge-secondary">Coming soon</span>
              </div>
          </div>
      </div>
  
      {{-- ショップ機能 --}}
      <div class="card bg-base-100 shadow-md border overflow-hidden">
          <div class="w-full h-32 bg-gradient-to-r from-accent/30 to-primary/40 flex items-center justify-center text-5xl text-white">
              🛒
          </div>
          <div class="card-body">
              <h3 class="card-title text-lg">ショップ機能</h3>
              <p class="text-sm text-gray-600 mt-1">
                  購入履歴・おすすめ商品を確認できます。
              </p>
              <div class="flex justify-end mt-3">
                  <span class="badge badge-secondary">Coming soon</span>
              </div>
          </div>
      </div>
  
      {{-- イベント案内 --}}
      <div class="card bg-base-100 shadow-md border overflow-hidden">
          <div class="w-full h-32 bg-gradient-to-r from-warning/40 to-error/40 flex items-center justify-center text-5xl text-white">
              🎪
          </div>
          <div class="card-body">
              <h3 class="card-title text-lg">イベント案内</h3>
              <p class="text-sm text-gray-600 mt-1">
                  次回のオンライン・オフラインイベントをチェック。
              </p>
              <div class="flex justify-end mt-3">
                  <span class="badge badge-secondary">Coming soon</span>
              </div>
          </div>
      </div>
    
    </div>
  </div>
</div>
