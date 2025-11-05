<x-admin-layout>
    <div class="w-full">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- サイト状況 --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">サイト全体の状況</h3>
                <ul class="space-y-2">
                    <li>👤 ユーザー数: {{ $stats['users_count'] }}</li>
                    <li>💳 アクティブサブスク: </li>
                    <li>📝 投稿数: </li>
                    <li>📅 イベント数: </li>
                </ul>
            </div>

            {{-- 管理メニュー --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">管理メニュー</h3>
                <div class="flex flex-col space-y-2">
                    <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:underline">👤 ユーザー管理</a>
                    <a href="{{ route('admin.pages.index') }}" class="text-blue-600 hover:underline">📝 ページ管理</a>
                    <a href="" class="text-blue-600 hover:underline">📅 イベント管理</a>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
