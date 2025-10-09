{{-- 会員証モーダル --}}
<dialog id="membershipCard" class="modal" onclick="if(event.target === this) this.close();">
  <div class="modal-box relative max-w-xs p-0 rounded-3xl overflow-hidden border-4 border-white shadow-[0_0_60px_rgba(255,255,255,0.1)] animate-fadeInUp
              bg-gradient-to-b from-gray-900 via-gray-800 to-black text-white transition-transform duration-500 ease-out">
    
    {{-- 会員証内容 --}}
    <div class="flex flex-col items-center text-center px-6 pt-8 pb-10 relative">

      {{-- グロウ背景エフェクト --}}
      <div class="absolute inset-0 bg-gradient-to-br from-primary/10 via-transparent to-indigo-900/30 blur-3xl animate-pulse-slow"></div>

      {{-- ロゴタイトル --}}
      <h2 class="text-xl font-bold tracking-widest mb-1 relative z-10 text-transparent bg-clip-text bg-gradient-to-r from-white via-gray-200 to-gray-400 drop-shadow-lg">
        BAKERISTA CIRCLE
      </h2>
      <p class="text-[11px] uppercase tracking-widest text-gray-400 mb-5 relative z-10">membership card</p>

      {{-- プロフィール画像 --}}
      @php
        $avatar = $user->mediaFiles()
                      ->wherePivot('media_files.type', 'avatar')
                      ->orderBy('media_relations.sort_order', 'asc')
                      ->first();
      @endphp
      <div class="avatar mb-4 relative z-10 animate-scaleIn">
        <div class="w-24 h-24 rounded-full ring-2 ring-white/60 ring-offset-4 ring-offset-gray-900 shadow-lg shadow-primary/20">
          @if($avatar)
            <img src="{{ Storage::url($avatar->path) }}" alt="avatar" class="object-cover rounded-full">
          @else
            <div class="bg-gray-700 flex items-center justify-center w-full h-full text-4xl font-semibold">
              {{ mb_substr($user->display_name ?? '？', 0, 1) }}
            </div>
          @endif
        </div>
      </div>

      {{-- display_name --}}
      <h3 class="text-xl font-semibold tracking-wide mb-1 relative z-10">{{ $user->display_name ?? 'Bakerista Member' }}</h3>

      {{-- Instagram ID --}}
      @if($user->instagram_id)
        <p class="text-sm text-gray-400 mb-4 flex items-center justify-center gap-1 relative z-10">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-pink-400" fill="currentColor" viewBox="0 0 24 24">
            <path d="M7.5 2C4.462 2 2 4.462 2 7.5v9C2 19.538 4.462 22 7.5 22h9c3.038 0 5.5-2.462 5.5-5.5v-9C22 4.462 19.538 2 16.5 2h-9zM12 7a5 5 0 1 1 0 10a5 5 0 0 1 0-10zm6-1a1 1 0 1 1 0 2a1 1 0 0 1 0-2zM12 9a3 3 0 1 0 0 6a3 3 0 0 0 0-6z"/>
          </svg>
          <span>{{ $user->instagram_id }}</span>
        </p>
      @endif

      {{-- 会員番号 --}}
      <div class="px-4 py-1 rounded-full bg-gradient-to-r from-primary to-indigo-600 text-white text-sm font-semibold shadow-lg shadow-primary/30 relative z-10 mb-4">
        No. {{ $user->member_number ?? '未発行' }}
      </div>

      {{-- 登録日 --}}
      <p class="text-xs text-gray-400 relative z-10">Member since {{ $user->created_at->format('Y') }}</p>

      {{-- 装飾ライン --}}
      <div class="w-20 h-px bg-gradient-to-r from-transparent via-gray-500 to-transparent my-4 opacity-60 animate-pulse-slow"></div>

      {{-- フッターロゴ --}}
      <p class="text-[10px] tracking-[0.15em] text-gray-500 uppercase relative z-10">bakerista circle © {{ date('Y') }}</p>
    </div>
  </div>
</dialog>
