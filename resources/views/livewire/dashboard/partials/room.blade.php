{{-- 新着投稿 --}}
<div class="bg-white shadow-sm sm:rounded-lg">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold p-4">📝 新着投稿</h2>

        {{-- 通知ボタン --}}
        <button class="btn btn-ghost btn-circle relative" wire:click="$dispatch('open-notifications')">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 
                    6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 
                    6.165 6 8.388 6 11v3.159c0 .538-.214 
                    1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 
                    0v-1m6 0H9"/>
            </svg>

            {{-- 未読バッジ --}}
            @php
                $unread = \App\Models\Notification::where('user_id', auth()->id())
                    ->whereNull('read_at')
                    ->count();
            @endphp
            @if($unread > 0)
                <span class="badge badge-error badge-xs absolute top-1 right-1">{{ $unread }}</span>
            @endif
        </button>
    </div>

    <ul class="divide-y">
        @foreach($latestPosts as $post)
            <li class="hover:bg-base-100 transition">
                <a href="{{ route('posts.show', $post) }}" class="block p-3">
                    <div class="w-full break-words">
                        {{-- ルーム名 --}}
                        <div class=" mb-1">
                            <span class="text-sm font-bold text-primary">[{{ $post->room->name }}]</span> <span class="text-sm">{{ Str::limit(strip_tags($post->body), 100) }}</span> <span class="badge badge-sm badge-soft text-xs">by {{ $post->user->display_name }}</span>
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

