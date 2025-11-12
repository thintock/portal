@section('title', $room->name)
<div class="max-w-4xl mx-auto p-1 pt-4 pb-8 space-y-4">
    {{-- ===========================
         他のルームへのリンク一覧
    =========================== --}}
    @if($otherRooms->count() > 0)
    <div class="flex flex-wrap gap-2 mb-3 justify-start">
        @foreach($otherRooms as $r)
            <a href="{{ route('rooms.show', $r->id) }}" 
               class="btn 
                      btn-outline btn-primary 
                      normal-case
                      btn-xs sm:btn-sm  {{-- 👈 スマホ：小、PC：標準 --}}
                      {{ $r->id === $room->id ? 'btn-primary text-white' : '' }}">
               {{ $r->name }}
            </a>
        @endforeach
    </div>
    @endif
    {{-- ===========================
         カバー写真（スマホでコンパクト表示）
    =========================== --}}
    <div class="relative w-full h-24 sm:h-64 rounded-lg bg-gray-200 overflow-visible">
        @if($room->cover_media)
            <img src="{{ Storage::url($room->cover_media->path) }}" 
                 alt="cover"
                 class="w-full h-full object-cover rounded-lg">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-500 rounded-lg">
                No Cover Image
            </div>
        @endif
    
        {{-- ===========================
             スマホ専用：ルームアイコン
        ============================ --}}
        <div class="absolute left-4 bottom-[-36px] sm:hidden">
            @if($room->icon_media)
                <img src="{{ Storage::url($room->icon_media->path) }}" alt="icon" class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-md">
            @else
                <div class="w-20 h-20 rounded-full bg-gray-300 flex items-center justify-center text-xl text-gray-600 border-4 border-white shadow-md">
                    {{ mb_substr($room->name, 0, 1) }}
                </div>
            @endif
        </div>
    </div>

    {{-- ===========================
         スマホ専用：ルーム情報 + メンバー + 参加ボタン
    =========================== --}}
    <div class="sm:hidden mt-10 px-4 flex flex-col space-y-3">
    
        {{-- 上段：ルーム名・説明 --}}
        <div class="flex justify-between">
            <div class="ml-24 flex-1">
                <h1 class="text-base font-semibold text-gray-800 leading-tight">{{ $room->name }}</h1>
                @if($room->description)
                    <p class="text-sm text-gray-600 mt-0.5">{{ $room->description }}</p>
                @endif
            </div>
        </div>
    
        {{-- 下段：参加者リスト + 参加ボタン --}}
        <div class="flex justify-between items-center mt-1">
            {{-- メンバーアバター --}}
            <div class="flex -space-x-2">
                @foreach($room->memberUsers->shuffle()->take(5) as $user)
                    @php
                        $avatar = $user->mediaFiles()->where('media_files.type', 'avatar')->first();
                    @endphp
                    <div class="w-8 h-8 rounded-full overflow-hidden bg-base-200 flex items-center justify-center border border-white"
                         title="{{ $user->name }}">
                        @if($avatar)
                            <img src="{{ Storage::url($avatar->path) }}" 
                                 alt="avatar"
                                 class="w-full h-full object-cover">
                        @else
                            <span class="text-xs font-semibold text-gray-600">
                                {{ mb_substr($user->name ?? '？', 0, 1) }}
                            </span>
                        @endif
                    </div>
                @endforeach
                @if($room->memberUsers->count() > 5)
                    <div class="w-8 h-8 rounded-full bg-base-200 flex items-center justify-center text-xs border border-white">
                        +{{ $room->memberUsers->count() - 5 }}
                    </div>
                @endif
            </div>
    
            {{-- 参加 / 退出ボタン --}}
            @if($room->visibility !== 'public')
                @if($room->members->contains('user_id', auth()->id())) 
                    <form method="POST" action="{{ route('rooms.leave', $room) }}">
                        @csrf 
                        @method('DELETE') 
                        <button class="btn btn-xs btn-error whitespace-nowrap">退出</button>
                    </form>
                @else 
                    <form method="POST" action="{{ route('rooms.join', $room) }}">
                        @csrf 
                        <button class="btn btn-xs btn-primary whitespace-nowrap">参加</button>
                    </form>
                @endif
            @endif
        </div>
    </div>


    {{-- ===========================
         PC用カード（既存デザイン維持）
    ============================ --}}
    <div class="hidden sm:block card bg-base-100 shadow-lg">
        <div class="card-body">
            {{-- プロフ写真 + ルーム情報 --}}
            <div class="flex items-center space-x-4 mb-4">
                @if($room->icon_media)
                    <img src="{{ Storage::url($room->icon_media->path) }}" alt="icon" class="w-16 h-16 rounded-full object-cover border">
                @else
                    <div class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center text-lg text-gray-600">
                        {{ mb_substr($room->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <h1 class="card-title text-2xl">{{ $room->name }}</h1>
                    <p class="text-gray-600">{{ $room->description }}</p>
                </div>
            </div>

            {{-- 参加/退出ボタン + メンバーアバター（PC表示） --}}
            @if($room->visibility !== 'public') 
                <div class="flex items-center space-x-4 mb-4">
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
                            @php
                                $avatar = $user->mediaFiles()->where('media_files.type', 'avatar')->first();
                            @endphp
                            <div class="w-10 h-10 rounded-full overflow-hidden bg-base-200 flex items-center justify-center border-2 
                                        {{ $user->role === 'guest' ? 'border-secondary' : 'border-base-100' }}"
                                 title="{{ $user->name }}">
                                @if($avatar)
                                    <img src="{{ Storage::url($avatar->path) }}" 
                                         alt="avatar" 
                                         class="w-full h-full object-cover">
                                @else
                                    <span class="text-sm font-semibold text-gray-600">
                                        {{ mb_substr($user->name ?? '？', 0, 1) }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
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
