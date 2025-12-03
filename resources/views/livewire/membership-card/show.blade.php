<div>
  @if($showModal)
      {{-- ▼ まず、カードを表示してよいユーザーか判定 --}}
    @php
        $showFullCard = false;
    
        if ($user?->role === 'admin' || $user?->role === 'guest') {
            $showFullCard = true; // 常にOK
        } elseif ($user?->member_number) {
            $showFullCard = true; // サークル会員
        }
    @endphp
    
    @if(! $showFullCard)
        {{-- ▼ サークル非会員（カードは出さず、説明文のみ） --}}
      <div class="modal modal-open" wire:click.self="close">
          <div class="modal-box relative max-w-xs p-6 rounded-3xl bg-white text-gray-800 text-center">
              <h3 class="text-lg font-semibold mb-3">{{ $user?->name }}<span class="text-xs">さん</span></h3>
              <p class="text-sm leading-relaxed text-gray-600">
                  現在、「{{ $user?->name }}」さんは<br>ベイクルの会員ではありません。<br>
                  ベイクルに再加入されると、<br>ここにメンバーカードが表示されます。
              </p>
      
              <div class="mt-6">
                  <button wire:click="close"
                      class="btn btn-sm btn-outline w-full">閉じる</button>
              </div>
          </div>
      </div>
    @else
    {{-- DaisyUI modal --}}
    <div class="modal modal-open" wire:click.self="close">
      <div 
        class="modal-box relative max-w-xs p-0 rounded-3xl overflow-hidden border-4 border-white 
               shadow-[0_0_60px_rgba(255,255,255,0.1)] 
               bg-gradient-to-b from-gray-900 via-gray-800 to-black text-white 
               transition-all duration-500 ease-out animate-[zoomIn_0.4s_ease-out]"
      >
  
        {{-- 会員証本文 --}}
        <div class="flex flex-col items-center text-center px-6 pt-8 pb-10 relative">
  
          {{-- グロウ背景 --}}
          <div class="absolute inset-0 bg-gradient-to-br from-primary/10 via-transparent to-indigo-900/30 blur-3xl animate-pulse-slow"></div>
  
          {{-- ロゴタイトル --}}
          <h2 class="text-xl font-bold tracking-widest mb-1 relative z-10 
                     text-transparent bg-clip-text bg-gradient-to-r 
                     from-white via-gray-200 to-gray-400 drop-shadow-lg">
              BAKERISTA CIRCLE
          </h2>
          
          <p class="text-[11px] uppercase tracking-widest text-gray-400 mb-5 relative z-10">
              membership
          </p>
  
          {{-- アバター --}}
          @php
            $avatar = $user?->mediaFiles()->where('type', 'avatar')->first();
          @endphp
          <div class="avatar mb-4 relative z-10">
            <div class="w-24 h-24 rounded-full ring-2 ring-white/60 ring-offset-4 ring-offset-gray-900 shadow-lg shadow-primary/20 animate-[fadeIn_0.8s_ease-out]">
              @if($avatar)
                <img src="{{ Storage::url($avatar->path) }}" alt="avatar" class="object-cover rounded-full">
              @else
                <div class="bg-gray-700 flex items-center justify-center w-full h-full text-4xl font-semibold">
                  {{ mb_substr($user?->display_name ?? '？', 0, 1) }}
                </div>
              @endif
            </div>
          </div>
  
          {{-- display_name --}}
          <h3 class="text-xl font-semibold tracking-wide mb-1 relative z-10 animate-[fadeIn_1s_ease-out]">
            {{ $user?->name ?? 'Bakerista Member' }}
          </h3>
  
          {{-- Instagram --}}
          @if($user?->instagram_id)
            <div 
              x-data="{
                openInstagram() {
                  const username = '{{ $user->instagram_id }}';
                  const appUrl = `instagram://user?username=${username}`;
                  const webUrl = `https://www.instagram.com/${username}/`;
                  
                  const ua = navigator.userAgent.toLowerCase();
                  const isMobile = /iphone|ipad|android|ipod/.test(ua);
          
                  if (isMobile) {
                    // スマホならアプリ試行 → fallbackでWeb
                    const timeout = setTimeout(() => window.open(webUrl, '_blank'), 800);
                    window.location = appUrl;
                    // ユーザーがキャンセルした場合でもWebへ
                    window.addEventListener('pagehide', () => clearTimeout(timeout));
                  } else {
                    // PCならWeb版へ
                    window.open(webUrl, '_blank');
                  }
                }
              }"
              @click="openInstagram()"
              class="text-sm text-gray-400 mb-4 flex items-center justify-center gap-1 relative z-10 animate-[fadeIn_1s_ease-out] cursor-pointer hover:text-pink-400 transition-colors"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-pink-400" fill="currentColor" viewBox="0 0 24 24">
                <path d="M7.5 2C4.462 2 2 4.462 2 7.5v9C2 19.538 4.462 22 7.5 22h9c3.038 0 5.5-2.462 5.5-5.5v-9C22 4.462 19.538 2 16.5 2h-9zM12 7a5 5 0 1 1 0 10a5 5 0 0 1 0-10zm6-1a1 1 0 1 1 0 2a1 1 0 0 1 0-2zM12 9a3 3 0 1 0 0 6a3 3 0 0 0 0-6z"/>
              </svg>
              <span class="underline underline-offset-2">{{ '@' . $user->instagram_id }}</span>
            </div>
          @endif
  
          {{-- 会員番号 --}}
          <div class="px-4 py-1 rounded-full bg-gradient-to-r from-primary to-indigo-600 
                      text-white text-sm font-semibold shadow-lg shadow-primary/30 relative z-10 mb-4 animate-[fadeIn_1.2s_ease-out]">
          
              @if($user?->role === 'admin')
                  Official
              @elseif($user?->role === 'guest')
                  Guest
              @elseif($user?->member_number) 
                  No. {{ $user->member_number }}
              @else
                  ベイクル未登録
              @endif
          
          </div>
  
          {{-- 登録年 --}}
          @if($user)
            <p class="text-xs text-gray-400 relative z-10 animate-[fadeIn_1.2s_ease-out]">
              Member since {{ $user->created_at->format('Y') }}
            </p>
          @endif
  
          {{-- ライン --}}
          <div class="w-20 h-px bg-gradient-to-r from-transparent via-gray-500 to-transparent my-4 opacity-60 animate-pulse-slow"></div>
  
          {{-- フッターロゴ --}}
          <p class="text-[10px] tracking-[0.15em] text-gray-500 relative z-10 animate-[fadeIn_1.5s_ease-out]">
            © {{ date('Y') }} Bakerista Mills Corp.
          </p>
        </div>
      </div>
    </div>
    @endif
  @endif
</div>
