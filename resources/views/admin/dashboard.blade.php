<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">サイト全体の状況</h3>
                <ul class="space-y-2">
                    <li>👤 ユーザー数: {{ $stats['users_count'] }}</li>
                    <li>💳 アクティブサブスク: </li>
                    <li>📝 投稿数: </li>
                    <li>📅 イベント数: </li>
                </ul>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">管理メニュー</h3>
                <div class="flex flex-col space-y-2">
                    <a href="{{ route('admin.users') }}" class="text-blue-600 hover:underline">👤 ユーザー管理</a>
                    <a href="" class="text-blue-600 hover:underline">📝 投稿管理</a>
                    <a href="" class="text-blue-600 hover:underline">📅 イベント管理</a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
