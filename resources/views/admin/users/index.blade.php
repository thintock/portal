<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">👥 ユーザー一覧</h2>
    </x-slot>

    <div class="p-6 space-y-4">
        {{-- 成功メッセージ --}}
        @if(session('success'))
            <div class="alert alert-success shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4">
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full text-sm">
                        <thead>
                            <tr>
                                <th>ID＆会員番号</th>
                                <th>プロフィール</th>
                                <th>お名前</th>
                                <th>お住まい</th>
                                <th>会員登録日時</th>
                                <th>サブスク状態</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}：<div class="badge badge-primary">{{ $user->member_number }}</div></td>
                                <td>
                                    @if($user->avatar)
                                        <div class="avatar">
                                            <div class="w-10 rounded-full">
                                                <img src="{{ $user->avatar->url }}" alt="avatar" class="w-12 h-12 rounded-full object-cover">
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge badge-ghost">なし</span>
                                    @endif
                                </td>
                                <td>{{ $user->display_name }}（{{ $user->last_name }} {{ $user->name }}）</td>
                                <td>{{ $user->prefecture }}{{ $user->address1 }}{{ $user->address2 }}</td>
                                <td>{{ $user->created_at }}</td>
                                <td>
                                    @if($user->subscribed('default'))
                                        <span class="badge badge-success">有効</span>
                                    @else
                                        <span class="badge badge-error">無効</span>
                                    @endif
                                </td>
                                <td class="space-x-1">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-xs btn-info">編集</a>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('削除してよろしいですか？')"
                                            class="btn btn-xs btn-error">削除</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ページネーション --}}
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
