<div>
    @if ($showModal)
        <div class="modal modal-open" wire:click.self="close">

            {{-- ▼ 全カード共通レイアウト：背景のみ条件分岐 --}}
            <div
                class="modal-box relative max-w-xs p-0 rounded-3xl overflow-hidden border transition-all duration-500 ease-out animate-[zoomIn_0.4s_ease-out]
                @if($isMember)
                    bg-gradient-to-b from-gray-900 via-gray-800 to-black border-white shadow-[0_0_60px_rgba(255,255,255,0.1)] text-white
                @else
                    bg-base-100 border-gray-200 shadow-lg text-gray-800
                @endif">

                {{-- ▼ カード内部 --}}
                <div class="flex flex-col items-center text-center px-6 pt-8 pb-10 relative">

                    {{-- グロウ背景（会員のみ） --}}
                    @if($isMember)
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/10 via-transparent to-indigo-900/30 blur-3xl animate-pulse-slow pointer-events-none"></div>
                    @endif

                    {{-- タイトル --}}
                    <h2 class="text-xl font-bold tracking-widest mb-1 relative z-10
                        @if($isMember)
                            text-transparent bg-clip-text bg-gradient-to-r from-white via-gray-200 to-gray-400 drop-shadow-lg
                        @else
                            text-gray-700
                        @endif">
                        BAKERISTA CIRCLE
                    </h2>

                    <p class="text-[11px] uppercase tracking-widest mb-5 relative z-10
                        @if($isMember) text-gray-400 @else text-gray-500 @endif">
                        membership
                    </p>

                    {{-- ▼ アバター --}}
                    @php
                        $avatar = $user?->mediaFiles->first();
                    @endphp
                    <div class="avatar mb-4 relative z-10">
                        <div
                            class="w-24 h-24 rounded-full
                            @if($isMember)
                                ring-2 ring-white/60 ring-offset-4 ring-offset-gray-900 shadow-lg shadow-primary/20
                            @else
                                bg-gray-100 ring-1 ring-gray-300
                            @endif">
                            @if($avatar)
                                <img src="{{ Storage::url($avatar->path) }}" class="object-cover rounded-full">
                            @else
                                <div class="flex items-center justify-center w-full h-full text-4xl font-semibold
                                    @if($isMember) text-gray-200 @else text-gray-500 @endif">
                                    {{ mb_substr($user?->display_name ?? $user?->name ?? '？', 0, 1) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 名前 --}}
                    <h3 class="text-xl font-semibold tracking-wide mb-1 relative z-10
                        @if($isMember) text-white @else text-gray-800 @endif">
                        {{ $user?->name }}
                    </h3>

                    {{-- ▼ Instagram（共通） --}}
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
                                        const timeout = setTimeout(() => window.open(webUrl, '_blank'), 800);
                                        window.location = appUrl;
                                        window.addEventListener('pagehide', () => clearTimeout(timeout));
                                    } else {
                                        window.open(webUrl, '_blank');
                                    }
                                }
                            }"
                            @click="openInstagram()"
                            class="text-sm mb-4 flex items-center justify-center gap-1 relative z-10 cursor-pointer hover:text-pink-400 transition-colors
                                @if($isMember) text-gray-400 @else text-gray-500 @endif"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-pink-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7.5 2C4.462 2 2 4.462 2 7.5v9C2 19.538 4.462 22 7.5 22h9c3.038 0 5.5-2.462 5.5-5.5v-9C22 4.462 19.538 2 16.5 2h-9zM12 7a5 5 0 1 1 0 10a5 5 0 0 1 0-10zm6-1a1 1 0 1 1 0 2a1 1 0 0 1 0-2zM12 9a3 3 0 1 0 0 6a3 3 0 0 0 0-6z"/>
                            </svg>
                            <span class="underline underline-offset-2">{{ '@' . $user->instagram_id }}</span>
                        </div>
                    @endif

                    {{-- ▼ ステータス（会員番号 or Free Member） --}}
                    <div
                        class="px-4 py-1 rounded-full text-sm font-semibold relative z-10 mb-4 shadow
                        @if($isMember)
                            bg-gradient-to-r from-primary to-indigo-600 text-white shadow-primary/30
                        @else
                            bg-gray-100 text-gray-600 border
                        @endif">
                        {{ $memberStatus }}
                    </div>

                    {{-- ▼ “Member since” または 非会員メッセージ --}}
                    @if($isMember)
                        <p class="text-xs text-gray-400 relative z-10">
                            Member since {{ $user->created_at->format('Y') }}
                        </p>
                    @else
                        <p class="text-sm text-gray-500 relative z-10 leading-relaxed">
                            ベイクルにご加入いただくと、<br>
                            正式なメンバーカードが表示されます。
                        </p>
                    @endif

                    {{-- 区切り線 --}}
                    <div
                        class="w-20 h-px mt-5 bg-gradient-to-r
                        @if($isMember) from-transparent via-gray-500 to-transparent opacity-60
                        @else from-transparent via-gray-300 to-transparent
                        @endif">
                    </div>

                    {{-- フッターロゴ --}}
                    <p
                        class="text-[10px] tracking-[0.15em] mt-4 relative z-10
                        @if($isMember) text-gray-500 @else text-gray-400 @endif">
                        © {{ date('Y') }} Bakerista Mills Corp.
                    </p>

                    {{-- 閉じるボタン（共通） --}}
                    <button wire:click="close"
                        class="btn btn-sm mt-6 w-full
                        @if($isMember) btn-outline text-white border-gray-500 hover:border-white
                        @else btn-outline text-gray-700
                        @endif">
                        閉じる
                    </button>

                </div>
            </div>
        </div>
    @endif
</div>
