<div class="max-w-4xl mx-auto px-3 sm:px-6 py-6 space-y-4">

  <div class="flex items-center justify-between gap-3">
    <div class="space-y-1">
      <h1 class="text-xl sm:text-2xl font-bold">保存した投稿</h1>
      <p class="text-xs text-base-content/60">保存箱で整理できます</p>
    </div>

    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline">
      ← ダッシュボードへ
    </a>
  </div>

  {{-- フィルタ --}}
  <div class="card bg-base-100 shadow-sm max-w-xl">
    <div class="card-body p-3 sm:p-4">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:items-center">
        <input type="text" class="input input-bordered w-full" placeholder="本文を検索" wire:model.live.debounce.400ms="q" />
  
        <div class="flex gap-2">
          <select class="select select-bordered w-full" wire:model.live="boxId">
            <option value="">すべて表示</option>
            @foreach($boxes as $box)
              <option value="{{ $box->id }}">{{ $box->name }}</option>
            @endforeach
          </select>
  
          {{-- PC/スマホ共通で右に削除ボタン（選択中のみ表示） --}}
          @if(!empty($boxId))
            <button type="button" class="btn btn-outline btn-error shrink-0" x-on:click.prevent="if(confirm('この保存箱を削除しますか？（この保存箱に入っている保存投稿は未分類になります）')) { $wire.deleteBox() }" wire:loading.attr="disabled" title="保存箱を削除">
              削除
            </button>
          @endif
        </div>
      </div>
    </div>
  </div>


  {{-- 一覧 --}}
  <div class="card bg-base-100 shadow-sm">
    <div class="card-body p-0">
      <ul class="divide-y">
        @forelse($savedPosts as $saved)
          @php
            $post = $saved->post;

            // 1枚目の画像だけ拾う（動画等は除外）
            $firstImage = optional($post->mediaFiles)
              ->first(function ($m) {
                $path = $m->path ?? '';
                return preg_match('/\.(jpe?g|png|webp|gif)$/i', $path);
              });

            $thumbUrl = $firstImage
              ? Storage::url($firstImage->path)
              : asset('images/bakele_logo.png');

            $boxName = $saved->category?->name ?? '未分類';
          @endphp

          <li class="hover:bg-base-200/40 transition" wire:key="saved-{{ $saved->id }}">
            <a href="{{ route('posts.show', $post) }}" class="block p-3 sm:p-4">
              <div class="grid grid-cols-12 gap-3 items-start">

                {{-- 左：サムネ --}}
                <div class="col-span-3 sm:col-span-2">
                  <div class="w-full aspect-square rounded-lg overflow-hidden border bg-gray-50">
                    <img src="{{ $thumbUrl }}" class="w-full h-full object-cover" alt="thumb">
                  </div>
                </div>

                {{-- 右：テキスト --}}
                <div class="col-span-9 sm:col-span-10 min-w-0">
                  <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                      <div class="font-bold text-primary text-sm truncate">
                        [{{ $post->room->name }}]
                      </div>
                    </div>

                    <div class="shrink-0 text-xs text-base-content/50 whitespace-nowrap">
                      {{-- 投稿日時でも保存日時でもOK。おすすめは保存日時 --}}
                      保存日：{{ $saved->created_at->format('Y/m/d') }}
                    </div>
                  </div>

                  <div class="text-xs text-base-content mt-2">
                    <span class="sm:hidden">
                      {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 80) }}
                    </span>
                    <span class="hidden sm:inline">
                      {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 300) }}
                    </span>
                    <span class="badge badge-sm bg-gray-100 text-gray-600 border-none">
                      by {{ $post->user->name }}
                    </span>
                    <span class="badge badge-sm bg-gray-100 text-gray-600 border-none">
                      （{{ $boxName }}）
                    </span>
                  </div>
                </div>

              </div>
            </a>
          </li>
        @empty
          <li class="p-6 text-center text-sm text-base-content/60">
            まだ保存した投稿がありません。
          </li>
        @endforelse
      </ul>

      {{-- 無限スクロール --}}
      @if($savedPosts->hasMorePages())
        <div class="p-4">
          <div class="flex items-center justify-center gap-2 text-sm text-base-content/60">
            <span class="loading loading-spinner loading-sm"></span>
            <span>読み込み中…</span>
          </div>
        </div>

        <div x-data x-init="
            let observer = new IntersectionObserver((entries) => {
              entries.forEach(entry => {
                if (entry.isIntersecting) {
                  observer.unobserve($el);
                  Livewire.dispatch('load-more-saved-posts');
                  setTimeout(() => observer.observe($el), 300);
                }
              });
            }, { root: null, threshold: 0.1 });
            observer.observe($el);
          "
          class="h-10"></div>
      @else
        <div class="p-4 text-center text-xs text-base-content/50">
          これ以上投稿はありません。
        </div>
      @endif

    </div>
  </div>

</div>
