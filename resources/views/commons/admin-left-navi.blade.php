{{-- ===============================
        サイドナビゲーション（左側）
================================ --}}
<aside class="w-64 bg-slate-900 text-gray-200 flex flex-col min-h-screen">
    {{-- ロゴエリア --}}
    <div class="flex items-center justify-center h-20 border-b border-slate-700">
        <a href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('images/bakerista_logo_rv.png') }}" alt="Bakerista Logo" class="h-8 w-auto opacity-90">
        </a>
    </div>

    {{-- ナビゲーション --}}
    <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-1 text-sm">
        {{-- ユーザートップページ --}}
        <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 rounded-md transition 
                  hover:bg-primary/30 hover:text-white
                {{ request()->routeIs('dashboard') ? 'bg-primary/40 text-white font-semibold' : '' }}">
            ユーザートップ
        </a>
        {{-- 管理ダッシュボード --}}
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center px-3 py-2 rounded-md transition 
                  hover:bg-primary/30 hover:text-white
                  {{ request()->routeIs('admin.dashboard') ? 'bg-primary/40 text-white font-semibold' : '' }}">
            ダッシュボード
        </a>
        {{-- 区切り線 --}}
        <div class="border-t border-slate-700 my-3"></div>
        
        {{-- ユーザー管理 --}}
        <a href="{{ route('admin.users.index') }}"
           class="flex items-center px-3 py-2 rounded-md transition 
                  hover:bg-primary/30 hover:text-white
                  {{ request()->routeIs('admin.users.*') ? 'bg-primary/40 text-white font-semibold' : '' }}">
            ユーザー管理
        </a>

        {{-- お知らせページ --}}
        <a href="{{ route('admin.announcements.index') }}"
           class="flex items-center px-3 py-2 rounded-md transition 
                  hover:bg-primary/30 hover:text-white
                  {{ request()->routeIs('admin.announcements.*') ? 'bg-primary/40 text-white font-semibold' : '' }}">
            お知らせ管理
        </a>
        
        {{-- 固定ページ --}}
        <a href="{{ route('admin.pages.index') }}"
           class="flex items-center px-3 py-2 rounded-md transition 
                  hover:bg-primary/30 hover:text-white
                  {{ request()->routeIs('admin.pages.*') ? 'bg-primary/40 text-white font-semibold' : '' }}">
            ページ管理
        </a>

        {{-- ルーム管理 --}}
        <a href="{{ route('admin.rooms.index') }}"
           class="flex items-center px-3 py-2 rounded-md transition 
                  hover:bg-primary/30 hover:text-white
                  {{ request()->routeIs('admin.rooms.*') ? 'bg-primary/40 text-white font-semibold' : '' }}">
            ルーム管理
        </a>
        
        {{-- イベント管理 --}}
        <a href="{{ route('admin.events.index') }}"
           class="flex items-center px-3 py-2 rounded-md transition 
                  hover:bg-primary/30 hover:text-white
                  {{ request()->routeIs('admin.events.*') ? 'bg-primary/40 text-white font-semibold' : '' }}">
            イベント管理
        </a>
        
        {{-- 月次テーマ管理 --}}
        <a href="{{ route('admin.monthly-items.index') }}"
           class="flex items-center px-3 py-2 rounded-md transition 
                  hover:bg-primary/30 hover:text-white
                  {{ request()->routeIs('admin.monthly-items.*') ? 'bg-primary/40 text-white font-semibold' : '' }}">
            月次テーマ管理
        </a>

        {{-- 区切り線 --}}
        <div class="border-t border-slate-700 my-3"></div>

        {{-- 以下は開発中カテゴリ --}}
        <div class="px-3 text-xs uppercase tracking-wider text-gray-400 mb-1">開発中メニュー</div>

        @php
            $devMenus = [
                '受注管理', '出荷管理', '発注管理', '製粉管理',
                '在庫管理', '商品管理', '資材消耗品管理',
                '受注データ変換', '顧客管理', 'メール管理'
            ];
        @endphp

        @foreach($devMenus as $label)
            <div class="flex items-center justify-between px-3 py-2 rounded-md bg-slate-800 text-gray-400 cursor-not-allowed">
                <span class="flex items-center">
                    {{ $label }}
                </span>
                <span class="badge badge-outline badge-sm text-gray-400 border-gray-600">🛠 開発中</span>
            </div>
        @endforeach

        {{-- 区切り線 --}}
        <div class="border-t border-slate-700 my-3"></div>

        {{-- サイト設定 --}}
        <a href="#"
           class="flex items-center px-3 py-2 rounded-md transition 
                  hover:bg-primary/30 hover:text-white">
            サイト設定
        </a>
    </nav>

    {{-- フッター --}}
    <div class="border-t border-slate-700 p-4 text-[11px] text-gray-500">
        <p>© {{ date('Y') }} Bakerista Mills Corp.</p>
    </div>
</aside>
