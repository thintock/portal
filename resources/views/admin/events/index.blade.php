<x-admin-layout>
  
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">イベント管理</h1>
            <p class="text-sm text-gray-500">イベントを作成・編集できます。</p>
        </div>
    @endsection
    
    <div class="w-full">
        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-xl font-bold">イベント一覧</h1>
                <a href="{{ route('admin.events.create') }}" class="btn btn-primary btn-sm">
                    ＋ 新規イベント作成
                </a>
            </div>

            {{-- ✅ 成功メッセージ --}}
            @if (session('success'))
                <div class="alert alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ✅ テーブル --}}
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full text-sm">
                    <thead class="bg-base-200">
                        <tr>
                            <th>ID</th>
                            <th>タイトル</th>
                            <th>開催期間</th>
                            <th>定員</th>
                            <th>受付</th>
                            <th>ステータス</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                            <tr>
                                <td>{{ $event->id }}</td>
                                <td class="font-semibold">
                                    <a href="{{ route('admin.events.edit', $event) }}" class="link link-primary">
                                        {{ $event->title }}
                                    </a>
                                </td>
                                <td>
                                    {{ $event->start_at ? $event->start_at->format('Y/m/d H:i') : '未設定' }}<br>
                                    〜 {{ $event->end_at ? $event->end_at->format('Y/m/d H:i') : '未設定' }}
                                </td>
                                <td>{{ $event->capacity ?? '—' }}</td>
                                <td>
                                    @if($event->recept)
                                        <span class="badge badge-success">受付中</span>
                                    @else
                                        <span class="badge badge-ghost">停止</span>
                                    @endif
                                </td>
                                <td>
                                    @switch($event->status)
                                        @case('draft') <span class="badge badge-ghost">下書き</span> @break
                                        @case('published') <span class="badge badge-info">公開中</span> @break
                                        @case('ongoing') <span class="badge badge-warning">開催中</span> @break
                                        @case('finished') <span class="badge badge-success">終了</span> @break
                                        @default <span class="badge">その他</span>
                                    @endswitch
                                </td>
                                <td class="space-x-1">
                                    <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-xs btn-outline">編集</a>
                                    <form method="POST" action="{{ route('admin.events.destroy', $event) }}" class="inline"
                                          onsubmit="return confirm('本当に削除しますか？');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-error">削除</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-6 text-gray-500">
                                    イベントはまだ登録されていません。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $events->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
