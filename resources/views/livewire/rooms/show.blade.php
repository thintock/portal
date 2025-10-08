<div class="max-w-4xl mx-auto p-1 py-8 space-y-6">

    {{-- カバー写真 --}}
    <div class="w-full h-48 rounded-lg overflow-hidden bg-gray-200">
        @if($room->cover_image)
            <img src="{{ Storage::url($room->cover_image) }}" alt="cover" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-500">
                No Cover Image
            </div>
        @endif
    </div>

    <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
            {{-- プロフ写真 + ルーム情報 --}}
            <div class="flex items-center space-x-4 mb-4">
                @if($room->icon)
                    <img src="{{ Storage::url($room->icon) }}" alt="icon" class="w-16 h-16 rounded-full object-cover">
                @else
                    <div class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center text-lg text-gray-600">
                        {{ mb_substr($room->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <h1 class="card-title text-2xl">{{ $room->display_name }}</h1>
                    <p class="text-gray-600">{{ $room->description }}</p>
                </div>
            </div>
            {{-- 参加/退出ボタン + メンバーアバター --}}
            @if($room->visibility !== 'public') 
                <div class="flex items-center space-x-4 mb-4">
                    {{-- ボタン --}}
                    @if($room->members->contains('user_id', auth()->id())) 
                        <form method="POST" action="{{ route('rooms.leave', $room) }}"> 
                            @csrf 
                            @method('DELETE') 
                            <button class="btn btn-error">退出する</button> 
                        </form> 
                    @else 
                        <form method="POST" action="{{ route('rooms.join', $room) }}"> 
                            @csrf 
                            <button class="btn btn-primary">参加する</button>
                        </form> 
                    @endif 
            
                    {{-- メンバーアバター表示（ランダム順） --}}
                    <div class="flex -space-x-3">
                        @foreach($room->memberUsers->shuffle()->take(10) as $user)
                            <div class="w-10 h-10 rounded-full overflow-hidden bg-base-200 flex items-center justify-center border-2 {{ $user->role === 'guest' ? 'border-secondary' : 'border-base-100' }}"
                                 title="{{ $user->display_name }}">
                                @if($user->avatar_media_id)
                                    <img src="{{ Storage::url($user->avatar->path ?? '') }}" 
                                         alt="avatar" 
                                         class="w-full h-full object-cover">
                                @else
                                    <span class="text-sm font-semibold text-gray-600">
                                        {{ mb_substr($user->display_name ?? '？', 0, 1) }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
            
                        {{-- 残り人数表示 --}}
                        @if($room->memberUsers->count() > 10)
                            <div class="w-10 h-10 rounded-full bg-base-200 flex items-center justify-center text-sm border-2 border-base-100">
                                +{{ $room->memberUsers->count() - 10 }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
    {{-- 投稿フォーム + 投稿一覧 --}}
    @if($room->visibility === 'public')
        @livewire('posts.post-feed', ['room' => $room])
    @elseif(in_array($room->visibility, ['members', 'private']))
        @if($room->members->contains('user_id', auth()->id()))
            @livewire('posts.post-feed', ['room' => $room])
        @endif
    @endif
</div>
