{{-- ===============================
        管理画面トップナビゲーション
================================ --}}
<header x-data="{ open: false }" class="bg-base-100 border-b border-base-300 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between h-16">
            
            {{-- 左側：タイトル／ページヘッダー --}}
            <div class="flex items-center">
                @hasSection('admin-header')
                    @yield('admin-header')
                @else
                    <h1 class="text-lg font-semibold text-gray-700">
                        未設定
                    </h1>
                @endif
            </div>

            {{-- 右側：ユーザードロップダウン --}}
            <div class="hidden sm:flex sm:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 bg-base-100 hover:bg-base-200 focus:outline-none transition">
                            
                            {{-- 名前 --}}
                            <div>
                                {{ Auth::user()->last_name ?? '' }}{{ Auth::user()->first_name ?? '' }}
                                <span class="text-xs text-gray-400">さん</span>
                            </div>

                            {{-- ▼アイコン --}}
                            <svg class="ml-1 h-4 w-4 fill-current text-gray-500"
                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z"
                                      clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        {{-- プロフィール設定 --}}
                        <x-dropdown-link :href="route('profile.edit')">
                            プロフィール設定
                        </x-dropdown-link>

                        {{-- ログアウト --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                ログアウト
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>

                {{-- プロフィール画像 --}}
                <div class="ml-3">
                    @php
                        $avatar = Auth::user()?->mediaFiles()
                            ->where('type', 'avatar')
                            ->orderBy('media_relations.sort_order', 'asc')
                            ->first();
                    @endphp

                    @if($avatar && $avatar->path)
                        <img src="{{ Storage::url($avatar->path) }}"
                             alt="プロフィール画像"
                             class="w-8 h-8 rounded-full object-cover cursor-pointer"
                             @click="$dispatch('open-modal', { id: 'image-viewer' }); $dispatch('set-image', { src: '{{ Storage::url($avatar->path) }}' });">
                    @else
                        <div class="avatar placeholder cursor-pointer"
                             @click="$dispatch('open-modal', { id: 'image-viewer' });">
                            <div class="bg-neutral text-neutral-content rounded-full w-8">
                                <span class="text-sm">{{ mb_substr(Auth::user()->last_name ?? '？', 0, 1) }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- モバイルハンバーガー --}}
            <div class="flex items-center sm:hidden">
                <button @click="open = !open"
                        class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-base-200 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }"
                              class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }"
                              class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- モバイルメニュー --}}
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden bg-base-100 border-t border-base-200">
        <div class="pt-4 pb-1 border-t border-base-300">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">
                    {{ Auth::user()->last_name ?? '' }}{{ Auth::user()->first_name ?? '' }}
                </div>
                <div class="font-medium text-sm text-gray-500">
                    {{ Auth::user()->email }}
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    プロフィール設定
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        ログアウト
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</header>
