<div>
  {{-- Livewireイベント登録 --}}
  <div x-data x-init="window.addEventListener('redirect', e => {
      const to = e?.detail?.url;
      if (to) window.location.assign(to);
  })"></div>

  {{-- モーダル --}}
  <dialog class="modal {{ $showModal ? 'modal-open' : '' }}" @click.self="$wire.close()">
    <div class="modal-box w-11/12 max-w-2xl bg-base-100 shadow-lg rounded-2xl relative">

      {{-- ヘッダー --}}
      <div class="flex justify-between items-center mb-3 border-b pb-2">
        <h3 class="font-semibold text-lg">通知一覧</h3>
        <button class="btn btn-sm btn-ghost" wire:click="close">✕</button>
      </div>

      {{-- タブ切替（DaisyUI: btn-groupバージョン） --}}
      <div class="btn-group mb-3 w-full justify-center">
        <button type="button"
                wire:click="$set('filter','all')"
                class="btn btn-sm {{ $filter==='all' ? 'btn-active btn-primary text-white' : 'btn-outline' }}">
            全て
        </button>
      
        <button type="button"
                wire:click="$set('filter','comment')"
                class="btn btn-sm {{ $filter==='comment' ? 'btn-active btn-primary text-white' : 'btn-outline' }}">
            コメント
        </button>
      
        <button type="button"
                wire:click="$set('filter','reaction')"
                class="btn btn-sm {{ $filter==='reaction' ? 'btn-active btn-primary text-white' : 'btn-outline' }}">
            いいね
        </button>
      </div>


      {{-- 操作ボタン --}}
      <div class="flex justify-between items-center mb-3">
        <button class="btn btn-xs btn-outline"
                wire:click="markAllAsRead"
                onclick="return confirm('通知をすべて既読にしますか？')">
          全て既読にする
        </button>
        <span class="text-xs text-neutral/50">最新30件を表示</span>
      </div>

      {{-- 通知リスト --}}
      <div class="max-h-96 overflow-y-auto space-y-2">
        @forelse($this->notifications as $n)
          <a href="#"
           wire:click.prevent="markAsReadAndRedirect({{ $n['id'] }})"
           wire:key="notif-{{ $n['id'] }}"
           class="block p-3 rounded-lg border transition duration-150
                  {{ $n['read_at'] ? 'bg-neutral/5 border-neutral/30 hover:bg-neutral/10' : 'bg-info/5 border-info/30 hover:bg-info/10' }}">

            <div class="flex space-x-3 items-start">

              {{-- アバター --}}
              <div class="relative w-10 h-10 shrink-0">
                <div class="w-10 h-10 rounded-full overflow-hidden bg-base-200 flex items-center justify-center border-2 border-base-300">
                  @if($n['avatar'])
                    <img src="{{ Storage::url($n['avatar']) }}" alt="avatar" class="w-full h-full object-cover">
                  @else
                    <span class="text-sm font-semibold text-gray-600">
                      {{ mb_substr($n['sender'], 0, 1) }}
                    </span>
                  @endif
                </div>
                <span class="absolute -top-1 -right-1 text-xs bg-base-100 border border-base-300 rounded-full px-1.5 py-0.5 shadow-sm">
                  {{ $n['icon'] }}
                </span>
              </div>

              {{-- 本文 --}}
              <div class="flex-1">
                <p class="text-sm font-semibold text-primary">
                  {{ $n['sender'] }}
                  <span class="text-xs text-neutral font-normal">さん</span>
                  <span class="text-xs text-neutral/50 mt-1">{{ $n['created_at'] }}</span>
                </p>

                <p class="font-semibold text-sm text-neutral-800">{{ $n['title'] }}</p>

                @if($n['message'])
                  <p class="text-sm text-neutral-600 mt-0.5">{{ $n['message'] }}</p>
                @endif
              </div>
            </div>
          </a>
        @empty
          <p class="text-center text-neutral/50 py-6 text-sm">通知はありません。</p>
        @endforelse
      </div>
    </div>
  </dialog>
</div>
