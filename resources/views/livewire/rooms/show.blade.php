<div class="max-w-4xl mx-auto py-8 space-y-6">

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
                    <h1 class="card-title text-2xl">{{ $room->name }}</h1>
                    <p class="text-gray-600">{{ $room->description }}</p>
                </div>
            </div>

            {{-- 参加/退出ボタン（public は非表示） --}} 
            @if($room->visibility !== 'public') 
                <div class="mb-4"> 
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
                </div> 
            @endif

            {{-- メンバー一覧（public 以外のみ） --}}
            @if($room->visibility !== 'public')
                <h2 class="text-xl font-semibold mt-6 mb-2">メンバー</h2>
                <ul class="divide-y divide-gray-200">
                    @foreach($room->members as $member)
                        <li class="py-2 flex justify-between items-center">
                            <span>{{ $member->user->name }} <span class="badge">{{ $member->role }}</span></span>
                        </li>
                    @endforeach
                </ul>
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
