<div class="space-y-3">
  {{-- Livewireメッセージ --}}
  @include('commons.messages')
  {{-- トグルボタン --}}
<div class="px-6">
  <button type="button" class="btn btn-outline btn-xs w-full"
          wire:click="$toggle('showForm')">
    {{ $showForm ? 'キャンセル' : '新しいコメントを作成' }}
  </button>
</div>

{{-- コメント投稿フォーム --}}
@if($showForm)
  <form wire:submit.prevent="save" class="mb-2 space-y-3">
    
    {{-- プレビュー + 画像追加 --}}
    <div class="grid grid-cols-3 gap-3">
      {{-- 既存ファイル --}}
      @foreach($media as $i => $file)
        <div class="relative">
          <img src="{{ $file->temporaryUrl() }}" class="rounded-lg border object-cover w-full h-24" />
          {{-- 削除 --}}
          <button type="button" wire:click="removeMedia({{ $i }})"
                  class="absolute top-1 right-1 btn btn-xs btn-circle btn-neutral text-base-100">✕</button>
          {{-- 並び替え --}}
          <div class="absolute bottom-1 right-1 flex space-x-1">
            @if($i > 0)
              <button type="button" wire:click="moveUp({{ $i }})" class="btn btn-xs btn-circle">⬆</button>
            @endif
            @if($i < count($media) - 1)
              <button type="button" wire:click="moveDown({{ $i }})" class="btn btn-xs btn-circle">⬇</button>
            @endif
          </div>
        </div>
      @endforeach

      @if(count($media) < 3)
        <label class="flex items-center justify-center rounded-lg border border-dashed w-full h-24 cursor-pointer hover:bg-base-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-base-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <input type="file" wire:model="newMedia" accept="image/*,video/*" class="hidden"/>
        </label>
      @endif
    </div>

    <div wire:loading wire:target="newMedia" class="text-xs mt-2">アップロード中...</div>

    {{-- テキスト入力 --}}
    <textarea wire:model.defer="body" rows="2"
      class="textarea textarea-bordered w-full leading-tight text-base"
      placeholder="コメントを追加..."
      wire:key="comment-body-{{ $formKey }}"></textarea>

    <button class="btn btn-primary btn-xs w-full" type="submit" wire:loading.attr="disabled" wire:target="newMedia,save">
      <span wire:loading wire:target="newMedia">アップロード中...</span>
      <span wire:loading wire:target="save">保存中...</span>
      <span wire:loading.remove wire:target="newMedia,save">送信</span>
    </button>
  </form>
@endif

  {{-- コメント一覧 --}}
  @forelse($parents as $comment)
    <div class="flex items-start space-x-2 mb-4" wire:key="comment-{{ $comment->id }}">
      

      <div class="bg-base-200 px-3 py-2 rounded-lg w-full relative">
        {{-- ヘッダー --}}
        <div class="flex justify-between items-center">
          <div class="flex items-center space-x-3">
            {{-- アイコン --}}
            <div class="w-8 h-8 rounded-full overflow-hidden bg-base-200 flex items-center justify-center border-2 {{ $comment->user->role === 'guest' ? 'border-secondary' : 'border-base-100' }}">
              @php
                  $avatar = $comment->user->mediaFiles()
                      ->where('media_files.type', 'avatar')
                      ->orderBy('media_relations.sort_order', 'asc')
                      ->first();
              @endphp
              
              @if($avatar)
                  <img src="{{ Storage::url($avatar->path) }}"
                       alt="avatar"
                       class="w-full h-full object-cover rounded-full">
              @else
                  <span class="text-sm font-semibold text-gray-600">
                      {{ mb_substr($comment->user->display_name ?? '？', 0, 1) }}
                  </span>
              @endif
            </div>
            <div>
              <span class="font-semibold">{{ $comment->user->display_name ?? 'ユーザー名未登録' }}</span>
              <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
              @if($comment->updated_at->ne($comment->created_at))
              <span class="text-xs text-gray-500" title="更新: ">
                ({{ $comment->updated_at->diffForHumans() }}:編集済み)
              </span>
              @endif
            </div>
          
          </div>
          @if($comment->user_id === auth()->id())
            {{-- ハンバーガーメニュー --}}
            <div class="dropdown dropdown-end z-50">
              <button tabindex="0" class="btn btn-ghost btn-xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h.01M12 12h.01M18 12h.01"/>
                </svg>
              </button>
              <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-28">
                <li><button type="button" wire:click="$dispatch('open-comment-edit', { commentId: {{ $comment->id }} })" class="w-full text-left">編集</button></li>
                <li><a wire:click="delete({{ $comment->id }})" onclick="return confirm('削除しますか？')">削除</a></li>
              </ul>
            </div>
          @endif
        </div>

        {{-- 本文 --}}
        <div class="text-sm mt-1 break-words" x-data="{ open: false }">
          @php
            $body = $comment->body ?? '';
            $short = mb_substr($body, 0, 100);
          @endphp
        
          @if(mb_strlen($body) > 100)
            {{-- 短縮表示 --}}
            <span x-show="!open" class="break-words">{!! \App\Helpers\TextHelper::linkify($short) !!}…</span>
        
            {{-- 全文表示 --}}
            <span x-show="open" class="break-words">{!! \App\Helpers\TextHelper::linkify($body) !!}</span>
        
            <button class="text-blue-600 text-xs ml-2"
                    @click="open=!open"
                    x-text="open?'閉じる':'…つづきを表示'"></button>
          @else
            <span class="break-words">{!! \App\Helpers\TextHelper::linkify($body) !!}</span>
          @endif
        </div>

        {{-- 添付メディア --}}
        @if($comment->mediaFiles && $comment->mediaFiles->isNotEmpty())
          <div class="grid grid-cols-3 gap-2 mt-2">
            @foreach($comment->mediaFiles->sortBy('pivot.sort_order') as $media)
              @php
                $ext = strtolower(pathinfo($media->path, PATHINFO_EXTENSION));
                $url = Storage::url($media->path);
              @endphp
        
              @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                {{-- 画像サムネイル（クリックでモーダル拡大） --}}
                <img src="{{ $url }}"
                     alt="{{ $media->alt ?? 'image' }}"
                     class="rounded border object-cover w-full h-24 cursor-pointer"
                     loading="lazy"
                     @click="
                       $dispatch('open-modal', 'image-viewer');
                       $dispatch('set-image', { src: '{{ $url }}' })">
              
              @elseif(in_array($ext, ['mp4','webm','mov','avi']))
                {{-- 動画プレビュー --}}
                <video controls class="rounded border max-h-40 w-full">
                  <source src="{{ $url }}" type="video/{{ $ext === 'mov' ? 'quicktime' : $ext }}">
                </video>
        
              @else
                {{-- その他ファイル --}}
                <a href="{{ $url }}" target="_blank" class="link link-primary text-xs">添付を開く</a>
              @endif
            @endforeach
          </div>
        @endif

        {{-- 返信フォーム --}}
        <livewire:comments.reply-form :parent="$comment" :key="'reply-form-'.$comment->id" />

        {{-- 子コメント一覧 --}}
        @php
          $visibleReplies = $comment->replies->take($repliesPerParent[$comment->id] ?? 3);
        @endphp
        
        {{--子コメント--}}
        @foreach($visibleReplies as $reply)
            
          <div class="mt-3 flex items-start space-x-2" wire:key="reply-{{ $reply->id }}">
            <div class="bg-base-100 px-3 py-2 rounded-lg w-full relative">
              {{-- ヘッダー --}}
              <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                  {{-- アイコン --}}
                  @php
                      $avatar = $reply->user->mediaFiles()
                          ->where('media_files.type', 'avatar')
                          ->first();
                  @endphp
                  
                  <div class="w-8 h-8 rounded-full overflow-hidden bg-base-100 flex items-center justify-center border-2 
                              {{ $reply->user->role === 'guest' ? 'border-secondary' : 'border-base-100' }}">
                    @if($avatar)
                      <img src="{{ Storage::url($avatar->path) }}" 
                           alt="avatar" 
                           class="w-full h-full object-cover">
                    @else
                      <span class="text-sm font-semibold text-gray-600">
                        {{ mb_substr($reply->user->display_name ?? '？', 0, 1) }}
                      </span>
                    @endif
                  </div>

                  <div class="flex items-center space-x-2">
                    <span class="text-sm font-semibold">{{ $reply->user->display_name ?? 'ユーザー名未登録' }}</span>
                    <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                    @if($reply->updated_at->ne($reply->created_at))
                      <span class="text-gray-400 text-xs">
                        ({{ $reply->updated_at->diffForHumans() }}:編集済み)
                      </span>
                    @endif
                  </div>
                </div>
                
                @if($reply->user_id === auth()->id())
                  {{-- ハンバーガーメニュー --}}
                  <div class="dropdown dropdown-end z-50">
                    <button tabindex="0" class="btn btn-ghost btn-xs">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h.01M12 12h.01M18 12h.01"/>
                      </svg>
                    </button>
                    <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-28">
                      <li>
                        <button type="button"
                                wire:click="$dispatch('open-comment-edit', { commentId: {{ $reply->id }} })"
                                class="w-full text-left">
                          編集
                        </button>
                      </li>
                      <li>
                        <a wire:click="delete({{ $reply->id }})" onclick="return confirm('削除しますか？')">削除</a>
                      </li>
                    </ul>
                  </div>
                @endif
              </div>
        
              {{-- 本文 --}}
              <div class="text-sm mt-1 break-words">
                {!! nl2br(e($reply->body)) !!}
              </div>
        
              {{-- 添付 --}}
              @if($reply->mediaFiles && $reply->mediaFiles->isNotEmpty())
                <div class="grid grid-cols-3 gap-2 mt-2">
                  @foreach($reply->mediaFiles as $media)
                    @php
                      $path = $media->path ?? $media->file_path ?? null;
                      $url  = $path ? Storage::url($path) : null;
                      $ext  = $path ? strtolower(pathinfo($path, PATHINFO_EXTENSION)) : null;
                    @endphp
              
                    @if($url)
                      @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                        {{-- サムネイル --}}
                        <img src="{{ $url }}"
                             class="rounded border object-cover w-full h-20 cursor-pointer"
                             loading="lazy"
                             @click="
                               $dispatch('open-modal', 'image-viewer');
                               $dispatch('set-image', { src: '{{ $url }}' })">
                      @elseif(in_array($ext, ['mp4','webm','mov','avi']))
                        {{-- 動画 --}}
                        <video controls class="rounded border max-h-32 w-full">
                          <source src="{{ $url }}" type="video/{{ $ext === 'mov' ? 'quicktime' : $ext }}">
                        </video>
                      @else
                        {{-- その他 --}}
                        <a href="{{ $url }}" target="_blank" class="link link-primary text-xs">
                          添付を開く
                        </a>
                      @endif
                    @endif
                  @endforeach
                </div>
              @endif

              <livewire:reactions.reaction-button :model="$reply" :key="'reply-like-'.$reply->id" />
            </div>
          </div>
        @endforeach

        
        {{-- 「さらに読み込む」ボタン --}}
        @if(($repliesPerParent[$comment->id] ?? 3) < $comment->replies->count())
          <div class="text-center mt-2 ml-8">
            <button wire:click="loadMoreReplies({{ $comment->id }})" class="btn btn-outline btn-xs">
              さらに返信を表示（{{ $comment->replies->count() - ($repliesPerParent[$comment->id] ?? 3) }}件）
            </button>
          </div>
        @endif
      </div>
    </div>
  @empty
    <p class="text-gray-500 text-sm">コメントはまだありません。</p>
  @endforelse

  @if($parents->hasMorePages())
    <div class="text-center mt-4">
      <button wire:click="loadMore" class="btn btn-outline btn-xs">さらに読み込む</button>
    </div>
  @endif
  
  

</div>