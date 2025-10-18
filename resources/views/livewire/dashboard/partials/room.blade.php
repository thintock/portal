{{-- 新着投稿 --}}
<div class="bg-white shadow-sm sm:rounded-lg">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold p-4">📝 新着投稿</h2>
    </div>

    <ul class="divide-y">
        @foreach($latestPosts as $post)
            <li class="hover:bg-base-100 transition">
                <a href="{{ route('posts.show', $post) }}" class="block p-3">
                    <div class="w-full break-words">
                        {{-- ルーム名 --}}
                        <div class=" mb-1">
                            <span class="text-sm font-bold text-primary">[{{ $post->room->name }}]</span> <span class="text-sm">{{ Str::limit(strip_tags($post->body), 100) }}</span> <span class="badge badge-sm badge-soft text-xs">by {{ $post->user->name }}</span>
                        </div>
                    </div>
                </a>
            </li>
        @endforeach
    </ul>



</div>

{{-- ルーム一覧 --}}
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($rooms as $room)
        <div class="card bg-base-100 shadow-md border overflow-hidden">
            {{-- カバー画像 --}}
            @if($room->cover_media)
                <img src="{{ Storage::url($room->cover_media->path) }}" alt="cover" class="w-full h-32 object-cover">
            @else
                <div class="w-full h-32 bg-gradient-to-r from-accent/30 to-primary/40 flex items-center">
                </div>
            @endif

            <div class="card-body">
                <div class="flex items-center space-x-2">
                    {{-- アイコン --}}
                    @if($room->icon_media)
                        <img src="{{ Storage::url($room->icon_media->path) }}" alt="icon" class="w-8 h-8 rounded-full object-cover">
                    @else
                        <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-xs text-gray-600">
                            {{ mb_substr($room->name, 0, 1) }}
                        </div>
                    @endif

                    <h2 class="card-title text-lg m-0">
                        <a href="{{ route('rooms.show', $room) }}" class="link link-primary">
                            {{ $room->name }}
                        </a>
                    </h2>
                </div>

                <p class="text-sm text-gray-600 mt-2">{{ $room->description }}</p>

                <div class="flex justify-between items-center text-sm mt-3">
                    @if($room->visibility === 'public')
                        <span>👥 公開</span>
                    @else
                        <span>👥 {{ $room->membersCount() }} メンバー</span>
                    @endif
                    <span>📝 {{ $room->posts_count }} 投稿</span>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- ページネーション --}}
<div class="mt-6">
</div>

