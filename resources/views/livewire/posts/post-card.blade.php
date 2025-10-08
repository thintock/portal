<div class="card bg-base-100 shadow">
  <div class="card-body p-0">
    {{-- 1段目：ヘッダー --}}
    <div class="flex items-center justify-between px-4 py-3 border-b">
      <div class="flex items-center space-x-3">
        {{--アバター表示--}}
        <div class="w-8 h-8 rounded-full overflow-hidden bg-base-200 flex items-center justify-center border-2 {{ $post->user->role === 'guest' ? 'border-secondary' : 'border-base-100' }}">
          @if($post->user->avatar_media_id)
            <img src="{{ Storage::url($post->user->avatar->path ?? '') }}" 
                 alt="avatar" 
                 class="w-full h-full object-cover">
          @else
            <span class="text-sm font-semibold text-gray-600">
                {{ mb_substr($post->user->display_name ?? '？', 0, 1) }}
            </span>
          @endif
        </div>
        {{---ニックネーム表示--}}
        <div>
          <span class="font-semibold">{{ $post->user->display_name }}</span>
          <span class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}
          @if($post->updated_at->ne($post->created_at))
          <span title="更新: {{ $post->updated_at->diffForHumans() }}">({{ $post->updated_at->diffForHumans() }}:編集済み)</span>
          @endif</span>
        </div>
      </div>

      {{-- ハンバーガーメニュー（投稿者のみ） --}}
      @if($post->user_id === auth()->id())
      <div class="dropdown dropdown-end z-50">
        <label tabindex="0" class="btn btn-ghost btn-xs">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h.01M12 12h.01M18 12h.01"/>
          </svg>
        </label>
        <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-32">
          <li><button class="w-full text-left" wire:click="$dispatch('open-post-edit', { postId: {{ $post->id }} })">編集</button></li>
          <li><button class="w-full text-left" x-on:click.prevent="if (confirm('削除しますか？')) { $wire.delete({{ $post->id }}) }">削除</button></li>
        </ul>
      </div>
      @endif
      
      
    </div>
    {{-- 2段目：メディア（最小カルーセル） --}}
    @if($post->media_json)
      <div class="relative">
        <div class="carousel w-full">
          @foreach($post->media_json as $i => $media)
            @php $ext = strtolower(pathinfo($media, PATHINFO_EXTENSION)); @endphp
            <div id="slide-{{ $post->id }}-{{ $i }}" class="carousel-item relative w-full">
              @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                <img src="{{ Storage::url($media) }}" class="w-full max-h-96 object-contain" @click="$dispatch('open-modal', 'image-viewer'); $dispatch('set-image', { src: '{{ Storage::url($media) }}' })">
              @elseif(in_array($ext, ['mp4','webm','mov','avi']))
                <video controls class="rounded border max-h-40 w-full">
                  {{-- mov は quicktime --}}
                  <source src="{{ Storage::url($media) }}" type="video/{{ $ext === 'mov' ? 'quicktime' : $ext }}">
                </video>
              @else
                <a class="link link-primary" href="{{ Storage::url($media) }}" target="_blank">添付を開く</a>
              @endif

              {{-- 矢印 --}}
              @if($i > 0)
                <a href="#slide-{{ $post->id }}-{{ $i-1 }}" class="absolute left-2 top-1/2 btn btn-circle btn-sm">❮</a>
              @endif
              @if($i < count($post->media_json)-1)
                <a href="#slide-{{ $post->id }}-{{ $i+1 }}" class="absolute right-2 top-1/2 btn btn-circle btn-sm">❯</a>
              @endif
            </div>
          @endforeach
        </div>
      </div>
    @endif
    
    {{-- 3段目：本文（200文字で省略表示） --}}
    <div class="px-2 md:px-6 text-sm text-gray-800 break-words" x-data="{open:false}">
      @php
        $body = $post->body ?? '';
        $short = mb_substr($body, 0, 200);
      @endphp
    
      @if(mb_strlen($body) > 200)
        {{-- 短縮表示（リンク化済み） --}}
        <span x-show="!open" class="break-words">{!! \App\Helpers\TextHelper::linkify($short) !!}…</span>
    
        {{-- 全文表示（リンク化済み） --}}
        <span x-show="open" class="break-words">{!! \App\Helpers\TextHelper::linkify($body) !!}</span>
    
        <button class="text-blue-600 text-xs ml-2"
                @click="open=!open"
                x-text="open?'閉じる':'…つづきを表示'"></button>
      @else
        {!! \App\Helpers\TextHelper::linkify($body) !!}
      @endif
    </div>

    
    {{-- 4段目：リアクション--}}
    <div class="px-2 flex items-center space-x-4">
      <livewire:reactions.reaction-button :model="$post" :key="'post-like-'.$post->id" />
    </div>

    {{-- 5段目：コメント --}}
    <div class="px-2 pb-4 pt-2 border-t">
      @livewire('comments.comment-section', ['post' => $post], key('comments-'.$post->id))
    </div>
  </div>
</div>