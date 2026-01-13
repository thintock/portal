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
                    <li class="hover:bg-base-200/40 transition">
                        <a href="{{ route('posts.show', $post) }}" class="block p-3 sm:p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 w-full">
                                    <div class="text-sm mb-1 flex justify-between">
                                        <div class="font-bold text-primary">
                                            [{{ $post->room->name }}]
                                        </div>
                                        <div class="shrink-0 text-xs text-base-content/50 whitespace-nowrap">
                                            {{ $post->created_at->format('Y/m/d H:i') }}
                                        </div>
                                    </div>

                                    {{-- モバイル：100 / PC：200（※あなたの現行値のまま） --}}
                                    <div class="text-sm text-base-content">
                                        <span class="sm:hidden">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 100) }}
                                        </span>
                                        <span class="hidden sm:inline">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 200) }}
                                        </span>
                                    </div>

                                    <div class="mt-2 text-xs text-base-content/60 flex flex-wrap gap-2 items-center">
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

            {{-- 無限スクロール：ローディング表示 --}}
            <div class="p-4">
                @if($hasMore)
                    <div class="flex items-center justify-center gap-2 text-sm text-base-content/60">
                        <span class="loading loading-spinner loading-sm"></span>
                        <span>読み込み中…</span>
                    </div>
                @else
                    <div class="text-center text-xs text-base-content/50">
                        これ以上投稿はありません。
                    </div>
                @endif
            </div>

            {{-- Sentinel（ここが見えたら loadMore） --}}
            @if($hasMore)
                <div
                    x-data="{
                        observer: null,
                        init() {
                            const el = this.$el;
                            // 既存があれば破棄
                            if (this.observer) this.observer.disconnect();

                            this.observer = new IntersectionObserver((entries) => {
                                entries.forEach((entry) => {
                                    if (entry.isIntersecting) {
                                        // 連打防止：Livewire側でもガードしているが念のため
                                        this.$wire.loadMore();
                                    }
                                });
                            }, { root: null, threshold: 0.1 });

                            this.observer.observe(el);
                        }
                    }"
                    x-init="init()"
                    x-effect="init()"
                    class="h-8"
                ></div>
            @endif
        </div>
    </div>

</div>
