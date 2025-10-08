<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
    <!-- メインナビ -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            {{-- 左側：ロゴ＋メニュー --}}
            <div class="flex items-center">
                {{-- ロゴ --}}
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/bakerista_circle_gray.png') }}" alt="Bakerista Logo" class="h-8 w-auto">
                    </a>
                </div>

                {{-- メインメニュー（PC） --}}
                <div class="hidden sm:flex sm:space-x-8 sm:ml-10 text-gray-700 font-medium">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        ホーム
                    </x-nav-link>
                </div>
            </div>

            {{-- 右側：ユーザードロップダウン --}}
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->display_name }} <span class="text-xs">さん</span></div>
                            <svg class="ml-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
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
            </div>

            {{-- ハンバーガーメニュー（モバイル） --}}
            <div class="flex items-center sm:hidden">
                <button @click="open = ! open" 
                        class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition">
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
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden bg-white border-t border-gray-100">
        {{-- メニューリンク --}}
        <div class="pt-2 pb-3 space-y-1 text-gray-700 font-medium">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                ホーム
            </x-responsive-nav-link>
        </div>

        {{-- ユーザー情報 --}}
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            {{-- メニュー下部 --}}
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    プロフィール設定
                </x-responsive-nav-link>

                {{-- ログアウト --}}
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
</nav>
