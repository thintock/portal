<x-admin-layout>
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">ユーザー管理</h1>
            <p class="text-sm text-gray-500">ユーザーを確認・編集できます。</p>
        </div>
    @endsection
    
    <div class="w-full">
        {{-- 成功メッセージ --}}
        @if(session('success'))
            <div class="alert alert-success shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="card bg-white shadow-xl">
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
                                <td>{{ $user->id }}：<div class="badge badge-primary">{{ $user->member_number ?? 'なし' }}</div></td>
                                <td>
                                    @if($user->avatar_url)
                                      <div class="avatar">
                                        <div class="w-10 rounded-full">
                                          <img src="{{ $user->avatar_url }}" alt="プロフィール画像" class="w-10 h-10 rounded-full object-cover">
                                        </div>
                                      </div>
                                    @else
                                      <div class="avatar placeholder">
                                        <div class="bg-neutral text-neutral-content rounded-full w-10">
                                          <span class="text-sm">{{ mb_substr($user->name ?? '？', 0, 1) }}</span>
                                        </div>
                                      </div>
                                    @endif
                                </td>
                                <td>{{ $user->name }}（{{ $user->last_name }} {{ $user->first_name }}）</td>
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
</x-admin-layout>
