<div class="max-w-4xl mx-auto px-3 sm:px-6 py-6 space-y-4">

    <div class="flex items-center justify-between gap-3">
        <div class="space-y-1">
            <h1 class="text-xl sm:text-2xl font-bold">新着投稿一覧</h1>
        </div>

        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline">
            ← ダッシュボードへ
        </a>
    </div>

    <div class="card bg-base-100 shadow-sm max-w-xl">
        <div class="card-body p-3 sm:p-4">
            <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                <input
                    type="text"
                    class="input input-bordered w-full"
                    placeholder="本文を検索"
                    wire:model.live.debounce.400ms="q"
                />
                <input type="hidden" wire:model="roomId" />
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm">
        <div class="card-body p-0">
            <ul class="divide-y">
              @forelse($posts as $post)
                @php
                  // latest.blade.php と同じ：1枚目の「画像」だけ拾う（動画等は除外）
                  $firstImage = optional($post->mediaFiles)
                    ->first(function ($m) {
                      $path = $m->path ?? '';
                      return preg_match('/\.(jpe?g|png|webp|gif)$/i', $path);
                    });
            
                  $thumbUrl = $firstImage
                    ? Storage::url($firstImage->path)
                    : asset('images/bakele_logo.png');
                @endphp
            
                <li class="hover:bg-base-200/40 transition" wire:key="post-{{ $post->id }}">
                  <a href="{{ route('posts.show', $post) }}" class="block p-3 sm:p-4">
                    <div class="grid grid-cols-12 gap-3 items-start">
            
                      {{-- 左：サムネ（1枚目 or ダミー） --}}
                      <div class="col-span-3 sm:col-span-2">
                        <div class="w-full aspect-square rounded-lg overflow-hidden border bg-gray-50">
                          <img src="{{ $thumbUrl }}" class="w-full h-full object-cover" alt="thumb">
                        </div>
                      </div>
            
                      {{-- 右：テキスト --}}
                      <div class="col-span-9 sm:col-span-10 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                          <div class="font-bold text-primary text-sm min-w-0 truncate">
                            [{{ $post->room->name }}]
                          </div>
                          <div class="shrink-0 text-xs text-base-content/50 whitespace-nowrap">
                            {{ $post->created_at->format('Y/m/d H:i') }}
                          </div>
                        </div>
            
                        {{-- モバイル：100 / PC：200（現行踏襲） --}}
                        <div class="text-xs text-base-content mt-1">
                          <span class="sm:hidden">
                            {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 100) }}
                          </span>
                          <span class="hidden sm:inline">
                            {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 350) }}
                          </span>
                          <span class="badge badge-sm bg-gray-100 text-gray-600 border-none">
                            by {{ $post->user->name }}
                          </span>
                        </div>
                      </div>
            
                    </div>
                  </a>
                </li>
              @empty
                <li class="p-6 text-center text-sm text-base-content/60">
                  まだ投稿がありません。
                </li>
              @endforelse
            </ul>


            {{-- 無限スクロール用トリガー --}}
            @if($posts->hasMorePages())
                <div class="p-4">
                    <div class="flex items-center justify-center gap-2 text-sm text-base-content/60">
                        <span class="loading loading-spinner loading-sm"></span>
                        <span>読み込み中…</span>
                    </div>
                </div>

                <div
                    x-data
                    x-init="
                        let observer = new IntersectionObserver((entries) => {
                          entries.forEach(entry => {
                            if (entry.isIntersecting) {
                              observer.unobserve($el); // 連打防止
                              Livewire.dispatch('load-more-posts');
                              setTimeout(() => observer.observe($el), 300); // DOM更新の猶予
                            }
                          });
                        }, { root: null, threshold: 0.1 });
                        observer.observe($el);
                    "
                    class="h-10"
                ></div>
            @else
                <div class="p-4 text-center text-xs text-base-content/50">
                    これ以上投稿はありません。
                </div>
            @endif

        </div>
    </div>

</div>
