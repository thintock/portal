<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Community') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- インフォメーションカード --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-4">🎉 ようこそ、ベーカリスタサークルへ！</h1>
            <p class="text-gray-600">
                このページは有料会員だけが閲覧できます。<br>
                参加できるルームを以下から選んでください。
            </p>
        </div>
        
        {{-- 新着投稿 --}}
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">📝 新着投稿</h2>
            <ul class="divide-y">
                @foreach($latestPosts as $post)
                    <li class="py-2">
                        <a href="{{ route('rooms.show', $post->room) }}" class="font-semibold">
                            [{{ $post->room->name }}]
                        </a>
                        {{ Str::limit($post->body, 50) }}
                        <span class="text-xs text-gray-500">by {{ $post->user->name }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
        
        {{-- ルーム一覧 --}}
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($rooms as $room)
                <div class="card bg-base-100 shadow-md border overflow-hidden">
                    {{-- カバー画像 --}}
                    @if($room->cover_image)
                        <img src="{{ Storage::url($room->cover_image) }}" alt="cover" class="w-full h-32 object-cover">
                    @else
                        <div class="w-full h-32 bg-gray-200 flex items-center justify-center text-gray-500">
                            カバー画像がありません。
                        </div>
                    @endif

                    <div class="card-body">
                        <div class="flex items-center space-x-2">
                            {{-- アイコン --}}
                            @if($room->icon)
                                <img src="{{ Storage::url($room->icon) }}" alt="icon" class="w-8 h-8 rounded-full object-cover">
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
            {{ $rooms->links() }}
        </div>

    </div>
</div>
