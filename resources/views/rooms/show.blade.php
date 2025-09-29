<x-app-layout>
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

        {{-- 参加/退出ボタン（publicの場合は非表示） --}}
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

        {{-- メンバー一覧（publicの場合は非表示） --}}
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

    {{-- 投稿フォーム --}}
    <div class="card bg-base-100 shadow-md">
      <div class="card-body">
        <h2 class="text-lg font-semibold mb-3">新しい投稿</h2>
        <form method="POST" action="{{ route('rooms.posts.store', $room) }}" enctype="multipart/form-data">
          @csrf
          <textarea name="body" class="textarea textarea-bordered w-full mb-3" rows="3" placeholder="今なにしてる？"></textarea>
          <div class="flex justify-between items-center">
            <div>
              <input type="file" name="media[]" class="file-input file-input-bordered file-input-sm" multiple />
            </div>
            <button type="submit" class="btn btn-primary btn-sm">投稿する</button>
          </div>
        </form>
      </div>
    </div>

    {{-- 投稿一覧 --}}
    <div class="space-y-6">
      @forelse($room->posts()->latest()->get() as $post)
        <div class="card bg-base-100 shadow">
          <div class="card-body p-0">
    
            {{-- 1段目：ヘッダー --}}
            <div class="flex items-center justify-between px-4 py-3 border-b">
              <div class="flex items-center space-x-3">
                @if($post->user->profile_photo_path ?? false)
                  <img src="{{ Storage::url($post->user->profile_photo_path) }}"
                       alt="user-icon"
                       class="w-8 h-8 rounded-full object-cover">
                @else
                  <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-xs text-gray-600">
                    {{ mb_substr($post->user->name, 0, 1) }}
                  </div>
                @endif
                <div>
                  <span class="font-semibold">{{ $post->user->name }}</span><br>
                  <span class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
                </div>
              </div>
              {{-- ハンバーガーメニュー --}}
              <div class="dropdown dropdown-end">
                <label tabindex="0" class="btn btn-ghost btn-xs">⋮</label>
                <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-32">
                  <li>
                    <a href="{{ route('rooms.posts.edit', [$room->id, $post->id]) }}">編集</a>
                  </li>
                  <li>
                    <form method="POST" action="{{ route('rooms.posts.destroy', [$room->id, $post->id]) }}"
                          onsubmit="return confirm('本当に削除しますか？')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="w-full text-left">削除</button>
                    </form>
                  </li>
                </ul>
              </div>
            </div>


    
            {{-- 2段目：メディア（カルーセル風） --}}
            @if($post->media_json)
              <div class="relative">
                <div class="carousel w-full">
                  @foreach($post->media_json as $index => $media)
                    @php $ext = strtolower(pathinfo($media, PATHINFO_EXTENSION)); @endphp
                    <div id="slide-{{ $post->id }}-{{ $index }}" class="carousel-item relative w-full">
                      @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                        <img src="{{ Storage::url($media) }}" class="w-full max-h-96 object-contain" />
                      @elseif(in_array($ext, ['mp4','mov','avi','webm']))
                        <video controls class="w-full max-h-96">
                          <source src="{{ Storage::url($media) }}" type="video/{{ $ext }}">
                        </video>
                      @endif
                      {{-- 矢印 --}}
                      @if($index > 0)
                        <a href="#slide-{{ $post->id }}-{{ $index-1 }}" class="absolute left-2 top-1/2 btn btn-circle btn-sm">❮</a>
                      @endif
                      @if($index < count($post->media_json)-1)
                        <a href="#slide-{{ $post->id }}-{{ $index+1 }}" class="absolute right-2 top-1/2 btn btn-circle btn-sm">❯</a>
                      @endif
                    </div>
                  @endforeach
                </div>
              </div>
            @endif
    
            {{-- 3段目：アクション --}}
            <div class="px-4 py-2 flex items-center space-x-4">
              <button class="btn btn-ghost btn-sm">❤️</button>
              <span class="text-sm text-gray-500">{{ $post->reaction_count }} いいね</span>
            </div>
    
            {{-- 4段目：本文 --}}
            <div class="px-4 pb-4 text-sm text-gray-800">
              @if(mb_strlen($post->body) > 200)
                <span class="preview">{!! nl2br(e(mb_substr($post->body, 0, 200))) !!}</span>
                <span class="full hidden">{!! nl2br(e($post->body)) !!}</span>
                <button class="text-blue-600 text-xs ml-2 toggle-full">...つづきを表示</button>
              @else
                {!! nl2br(e($post->body)) !!}
              @endif
            </div>
            
            {{-- 5段目：コメント --}}
            <div class="px-4 pb-4 border-t">
              {{-- コメント投稿フォーム --}}
              <form method="POST" action="{{ route('rooms.posts.comments.store', [$room->id, $post->id]) }}" enctype="multipart/form-data" class="mb-3">
                @csrf
                <textarea name="body" rows="2" class="textarea textarea-bordered w-full mb-2" placeholder="コメントを追加..."></textarea>
                <div class="flex justify-between items-center">
                  <input type="file" name="media[]" class="file-input file-input-bordered file-input-xs" multiple>
                  <button type="submit" class="btn btn-primary btn-xs">送信</button>
                </div>
              </form>
    
              {{-- コメント一覧 --}}
              @forelse($post->comments()->latest()->take(5)->get() as $comment)
                <div class="flex items-start space-x-2 mb-3">
                  {{-- アイコン --}}
                  <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center text-xs text-gray-600">
                    {{ mb_substr($comment->user->name, 0, 1) }}
                  </div>
              
                  {{-- コメント本体 --}}
                  <div class="bg-gray-100 px-3 py-2 rounded-lg w-full relative">
                    {{-- ヘッダー --}}
                    <div class="flex justify-between items-center">
                      <div class="text-sm font-semibold">{{ $comment->user->name }}</div>
                      @if($comment->user_id === Auth::id())
                        {{-- 編集用ハンバーガーメニュー --}}
                        <div class="dropdown dropdown-end">
                          <label tabindex="0" class="btn btn-ghost btn-xs">⋮</label>
                          <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-28">
                            <li>
                              <a href="{{ route('rooms.posts.comments.edit', [$room->id, $post->id, $comment->id]) }}">
                                編集
                              </a>
                            </li>
                            <li>
                              <form method="POST" action="{{ route('rooms.posts.comments.destroy', [$room->id, $post->id, $comment->id]) }}"
                                    onsubmit="return confirm('コメントを削除しますか？')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full text-left">削除</button>
                              </form>
                            </li>
                          </ul>
                        </div>
                      @endif
                    </div>
              
                    {{-- 本文 --}}
                    <div class="text-sm mt-1">{!! nl2br(e($comment->body)) !!}</div>
              
                    {{-- 添付メディア --}}
                    @if($comment->media_json)
                      <div class="grid grid-cols-2 gap-2 mt-2">
                        @foreach($comment->media_json as $media)
                          @php $ext = strtolower(pathinfo($media, PATHINFO_EXTENSION)); @endphp
              
                          @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                            <img src="{{ Storage::url($media) }}"
                                 alt="comment-attachment"
                                 class="rounded border object-cover max-h-40">
                          @elseif(in_array($ext, ['mp4','mov','avi','webm']))
                            <video controls class="rounded border max-h-40 w-full">
                              <source src="{{ Storage::url($media) }}" type="video/{{ $ext }}">
                            </video>
                          @else
                            <a href="{{ Storage::url($media) }}" class="text-blue-600 underline text-xs" target="_blank">
                              添付ファイルを開く
                            </a>
                          @endif
                        @endforeach
                      </div>
                    @endif
              
                    {{-- 投稿日時 --}}
                    <div class="text-xs text-gray-500 mt-1">
                      {{ $comment->created_at->diffForHumans() }}
                    </div>
                  </div>
                </div>
              @empty
                <p class="text-gray-500 text-sm">コメントはまだありません。</p>
              @endforelse
            </div>
            
          </div>
        </div>
      @empty
        <p class="text-gray-500">まだ投稿はありません。</p>
      @endforelse
    </div>
    
    {{-- JS: つづきを表示 --}}
    <script>
    document.addEventListener("DOMContentLoaded", () => {
      document.querySelectorAll(".toggle-full").forEach(btn => {
        btn.addEventListener("click", () => {
          const card = btn.closest("div");
          card.querySelector(".preview").classList.add("hidden");
          card.querySelector(".full").classList.remove("hidden");
          btn.remove();
        });
      });
    });
    </script>


    </div>

  </div>
</x-app-layout>
