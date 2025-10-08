<div>
  {{-- 🔹 Livewire とは別に JS イベントリスナーだけをグローバル登録 --}}
  <div x-data
       x-init="
         window.addEventListener('redirect', (event) => {
           const to = event?.detail?.url;
           if (to) { window.location.assign(to); }
         });
       ">
  </div>
  {{-- モーダル本体 --}}
  <dialog class="modal {{ $showModal ? 'modal-open' : '' }}">
    <div class="modal-box w-11/12 max-w-2xl bg-base-100 shadow-lg rounded-2xl relative">

      {{-- ヘッダー --}}
      <div class="flex justify-between items-center mb-3 border-b pb-2">
        <h3 class="font-semibold text-lg">通知一覧</h3>
        <button class="btn btn-sm btn-ghost" wire:click="close">✕</button>
      </div>

      {{-- 操作ボタン --}}
      <div class="flex justify-between items-center mb-3">
        <button class="btn btn-xs btn-outline" wire:click="markAllAsRead"
                onclick="return confirm('全ての通知を既読にしますか？')">全て既読にする</button>
        <span class="text-xs text-gray-400">最新30件を表示</span>
      </div>

      {{-- 通知リスト --}}
      <div class="max-h-96 overflow-y-auto space-y-2">
        @forelse($notifications as $n)
          <a href="#"
             @click.prevent="$wire.markAsReadAndRedirect({{ $n->id }})"
             class="block p-3 rounded-lg border transition duration-150
                    {{ $n->read_at ? 'bg-gray-100 border-gray-200 hover:bg-gray-200' : 'bg-blue-50 border-blue-200 hover:bg-blue-100' }}">
            <div>
              <p class="text-sm text-gray-800">{{ $n->message }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ $n->created_at?->diffForHumans() ?? '' }}</p>
            </div>
          </a>
        @empty
          <p class="text-center text-gray-500 py-6 text-sm">通知はありません。</p>
        @endforelse
      </div>

      {{-- フッター --}}
      <div class="modal-action">
        <button class="btn btn-neutral" wire:click="close">閉じる</button>
      </div>
    </div>
  </dialog>

  {{-- JSリダイレクト --}}
  <script>
    window.addEventListener('redirect', event => {
      window.location.href = event.detail.url;
    });
  </script>
</div>
