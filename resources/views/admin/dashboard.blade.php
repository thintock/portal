<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
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

            {{-- ルーム管理 --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">ルーム管理</h3>
                    <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">＋ ルーム作成</a>
                </div>

                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ルーム名</th>
                            <th>公開範囲</th>
                            <th>投稿権限</th>
                            <th>状態</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rooms as $room)
                            <tr>
                                <td>{{ $room->id }}</td>
                                <td>{{ $room->name }}</td>
                                <td>{{ $room->visibility }}</td>
                                <td>{{ $room->post_policy }}</td>
                                <td>
                                    @if($room->is_active)
                                        <span class="badge badge-success">公開</span>
                                    @else
                                        <span class="badge badge-ghost">非公開</span>
                                    @endif
                                </td>
                                <td class="flex space-x-2">
                                    <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-sm btn-outline">編集</a>
                                    <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" onsubmit="return confirm('削除してよろしいですか？');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-error">削除</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-gray-500 py-4">ルームがまだ作成されていません</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
