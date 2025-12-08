<div class="card bg-base-100 shadow">
  <div class="card-body p-0">
    {{-- 1段目：ヘッダー --}}
    <div class="flex items-center justify-between px-4 py-3 border-b">
      <div class="flex items-center space-x-3">
        {{-- アバター表示 --}}
        @php
            $avatar = $post->user->mediaFiles()
                ->where('media_files.type', 'avatar')
                ->first();
            $isBirthday = $post->user->birthday_month == now()->month
              && $post->user->birthday_day == now()->day;
        @endphp
        
        {{-- アバター全体（← overflow-hidden なし） --}}
      <div class="relative w-8 h-8 cursor-pointer flex items-center justify-center"
           wire:click="$dispatch('show-membership-card', { userId: {{ $post->user->id }} })"
           title="{{ $post->user->name ?? 'ユーザー名未登録' }}">
          
          {{-- 丸いアバター枠（ここで overflow-hidden） --}}
          <div class="w-full h-full rounded-full overflow-hidden bg-base-200 border-2 flex items-center justify-center transition hover:scale-105 hover:border-primary">
              @if($avatar)
                  <img src="{{ Storage::url($avatar->path) }}"
                       alt="avatar"
                       class="w-full h-full object-cover">
              @else
                  <span class="text-sm font-semibold text-gray-600">
                      {{ mb_substr($post->user->name ?? '？', 0, 1) }}
                  </span>
              @endif
          </div>
      
          {{-- 誕生日アイコン（枠の外に飛び出す） --}}
          @if($isBirthday)
              <div class="absolute -top-2.5 -right-2.5 text-white text-[16px] rounded-full px-1.5 py-[1px] transform rotate-[40deg]">
                  👑
              </div>
          @endif
      </div>
      
        {{-- ニックネーム・日付表示 --}}
        <div>
          <span class="font-semibold">{{ $post->user->name ?? 'ユーザー名未登録' }}</span>
          <span class="text-xs text-gray-500">
            {{ $post->created_at->diffForHumans() }}
            @if($post->updated_at->ne($post->created_at))
              <span title="更新: {{ $post->updated_at->diffForHumans() }}">
                （{{ $post->updated_at->diffForHumans() }}:編集済み）
              </span>
            @endif
          </span>
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
    {{-- 2段目：メディア（カルーセル） --}}
    @if($post->mediaFiles->isNotEmpty())
      <div 
        x-data="{ active: 0, total: {{ $post->mediaFiles->count() }} }" 
        class="relative overflow-hidden rounded-lg"
      >
        {{-- スライド一覧 --}}
        <div class="flex transition-transform duration-500 ease-in-out"
             :style="'transform: translateX(-' + (active * 100) + '%)'">
          @foreach($post->mediaFiles as $i => $media)
            @php
              $ext = strtolower(pathinfo($media->path, PATHINFO_EXTENSION));
              $url = Storage::url($media->path);
            @endphp
    
            <div class="flex-shrink-0 w-full">
              {{-- 画像 --}}
              @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                <img 
                  src="{{ $url }}" 
                  class="w-full max-h-96 object-contain cursor-pointer"
                  @click="$dispatch('open-modal', 'image-viewer'); $dispatch('set-image', { src: '{{ $url }}' })"
                >
              {{-- 動画 --}}
              @elseif(in_array($ext, ['mp4','webm','mov','avi']))
                <video controls class="rounded border max-h-60 w-full">
                  <source src="{{ $url }}" type="video/{{ $ext === 'mov' ? 'quicktime' : $ext }}">
                </video>
              {{-- その他 --}}
              @else
                <a class="link link-primary" href="{{ $url }}" target="_blank">添付を開く</a>
              @endif
            </div>
          @endforeach
        </div>
    
        {{-- 左右ナビゲーション（画像2枚以上の時のみ表示） --}}
        @if($post->mediaFiles->count() > 1)
            <button 
              class="absolute left-2 top-1/2 transform -translate-y-1/2 btn btn-circle btn-sm bg-white/70 hover:bg-white"
              @click="active = (active === 0) ? total - 1 : active - 1"
            >❮</button>
        
            <button 
              class="absolute right-2 top-1/2 transform -translate-y-1/2 btn btn-circle btn-sm bg-white/70 hover:bg-white"
              @click="active = (active === total - 1) ? 0 : active + 1"
            >❯</button>
        @endif
    
        {{-- インジケーター（2枚以上の時だけ表示） --}}
        @if($post->mediaFiles->count() > 1)
            <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-1">
              <template x-for="i in total" :key="i">
                <button
                  class="w-2.5 h-2.5 rounded-full"
                  :class="i - 1 === active ? 'bg-gray-800' : 'bg-gray-400/60'"
                  @click="active = i - 1"
                ></button>
              </template>
            </div>
        @endif

      </div>
    @endif


    
    {{-- 3段目：本文（200文字で省略表示） --}}
    <div class="p-2 sm:p-4 md:p-6 text-sm text-gray-800 break-words" x-data="{open:false}">
      @if(mb_strlen($post->body) > 200)
        {{-- 短縮表示 --}}
        <span x-show="!open" class="break-words">{!! $post->short_body !!}</span>
    
        {{-- 全文表示 --}}
        <span x-show="open" class="break-words">{!! $post->formatted_body !!}</span>
    
        <button class="text-blue-600 text-xs ml-2"
                @click="open=!open"
                x-text="open ? '閉じる' : '…つづきを表示'"></button>
      @else
        {!! $post->formatted_body !!}
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