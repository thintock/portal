{{-- 📝 新着投稿 --}}
<div class="bg-white shadow-sm rounded-lg max-w-3xl mx-auto w-full sm:px-6 pb-4 sm:pb-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold p-4">📝 新着投稿</h2>
    </div>

    <ul class="divide-y">
        @forelse($latestPosts as $post)
            @php
                // 1枚目の「画像」だけ拾う（動画等は除外）
                $firstImage = optional($post->mediaFiles)
                    ->first(function ($m) {
                        $path = $m->path ?? '';
                        return preg_match('/\.(jpe?g|png|webp|gif)$/i', $path);
                    });

                $thumbUrl = $firstImage
                    ? Storage::url($firstImage->path)
                    : asset('images/bakele_logo.png'); // ダミー画像（好きなものに変更OK）
            @endphp

            <li class="hover:bg-base-100 transition">
                <a href="{{ route('posts.show', $post) }}" class="block p-2">
                    <div class="grid grid-cols-12 gap-3 items-start">
                        {{-- 左：サムネ（1枚目 or ダミー） --}}
                        <div class="col-span-3 sm:col-span-1">
                            <div class="w-full aspect-square rounded-lg overflow-hidden border bg-gray-50">
                                <img src="{{ $thumbUrl }}" class="w-full h-full object-cover" alt="thumb">
                            </div>
                        </div>

                        {{-- 右：テキスト --}}
                        <div class="col-span-9 sm:col-span-11 min-w-0">
                            <span class="text-sm font-bold text-primary">[{{ $post->room->name }}]</span>

                            {{-- モバイル：50文字 --}}
                            <span class="text-sm sm:hidden">
                                {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 60) }}
                            </span>

                            {{-- PC：100文字 --}}
                            <span class="text-sm hidden sm:inline">
                                {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 130) }}
                            </span>
                            <span class="badge badge-sm text-xs bg-gray-100 text-gray-600 border-none">
                                by {{ $post->user->name }}・{{ $post->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </a>
            </li>
        @empty
            <li class="p-4 text-gray-500 text-center">まだ投稿がありません。</li>
        @endforelse
    </ul>

    <div class="px-4 pt-3 text-right">
        <a href="{{ route('posts.index') }}" class="link link-primary text-sm">もっと見る →</a>
    </div>
</div>