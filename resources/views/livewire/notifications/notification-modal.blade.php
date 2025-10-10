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

      {{-- 操作ボタン --}}
      <div class="flex justify-between items-center mb-3">
        <button class="btn btn-xs btn-outline" wire:click="markAllAsRead"
          onclick="return confirm('全ての通知を既読にしますか？')">全て既読にする</button>
        <span class="text-xs text-neutral/50">最新30件を表示</span>
      </div>

      {{-- 通知リスト --}}
      <div class="max-h-96 overflow-y-auto space-y-2">
        @forelse($notifications as $n)
          @php
            switch($n->type) {
                case 'comment':
                    $icon = '💬';
                    $title = 'コメントが届きました';
                    break;
                case 'reaction':
                    $icon = '❤️';
                    $title = 'リアクションがありました';
                    break;
                case 'reply':
                    $icon = '↩️';
                    $title = '返信が届きました';
                    break;
                case 'message':
                    $icon = '✉️';
                    $title = 'メッセージが届きました';
                    break;
                default:
                    $icon = '🔔';
                    $title = 'お知らせ';
            }
            $sender = $n->sender ?? null;
              $avatar = null;
              if ($sender) {
                  $avatar = $sender->mediaFiles()
                      ->where('media_files.type', 'avatar')
                      ->first();
              }
          @endphp

          <a href="#"
             @click.prevent="$wire.markAsReadAndRedirect({{ $n->id }})"
             class="block p-3 rounded-lg border transition duration-150
                    {{ $n->read_at ? 'bg-neutral/5 border-neutral/30 hover:bg-neutral/10' : 'bg-info/5 border-info/30 hover:bg-info/10' }}">
            <div class="flex space-x-3 items-start">
              {{-- アバター＋通知アイコン --}}
              <div class="relative w-10 h-10 shrink-0">
                {{-- アバター画像 --}}
                <div class="w-10 h-10 rounded-full overflow-hidden bg-base-200 flex items-center justify-center border-2 border-base-300">
                  @if($avatar)
                    <img src="{{ Storage::url($avatar->path) }}" alt="avatar" class="w-full h-full object-cover">
                  @else
                    <span class="text-sm font-semibold text-gray-600">
                      {{ mb_substr($sender->display_name ?? '？', 0, 1) }}
                    </span>
                  @endif
                </div>
                {{-- 通知タイプアイコン（右上に重ねる） --}}
                <span class="absolute -top-1 -right-1 text-xs bg-base-100 border border-base-300 rounded-full px-1.5 py-0.5 shadow-sm">
                  {{ $icon }}
                </span>
              </div>
              {{-- 本文 --}}
              <div class="flex-1">
                {{-- 送信者名 --}}
                <p class="text-sm font-semibold text-primary">
                  {{ $n->sender->display_name ?? 'ユーザー名未登録' }} <span class="text-xs text-neutral font-normal">さん</span> <span class="text-xs text-neutral/50 mt-1">{{ $n->created_at?->diffForHumans() ?? '' }}</span>
                </p>
              
                {{-- 通知タイトル --}}
                <p class="font-semibold text-sm text-neutral-800">{{ $title }}</p>
              
                {{-- 通知内容 --}}
                @if($n->message)
                  <p class="text-sm text-neutral-600 mt-0.5">
                    {{ Str::limit(strip_tags($n->message), 100) }}
                  </p>
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
