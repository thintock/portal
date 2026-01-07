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
                            <th>申込数（定員）</th>
                            <th>開催状況</th>
                            <th>ステータス</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                            <tr>
                                <td>{{ $event->id }}</td>
                                <td class="font-semibold">
                                    <a href="{{ route('events.show', $event->slug) }}"
                                       class="link link-primary"
                                       target="_blank" rel="noopener">
                                        {{ $event->title }}
                                    </a>
                                </td>
                                <td>
                                    {{ $event->start_at ? $event->start_at->format('Y/m/d H:i') : '未設定' }}<br>
                                    〜 {{ $event->end_at ? $event->end_at->format('Y/m/d H:i') : '未設定' }}
                                </td>
                                <td>
                                    @if(is_null($event->capacity) || (int)$event->capacity === 0)
                                      {{ $event->applications_count ?? 0 }}名（なし）
                                    @else
                                    　{{ $event->applications_count ?? 0 }}名（{{ $event->capacity }}名）
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $event->timing_class ?? 'badge-ghost' }}">
                                    {{ $event->timing_label ?? '未設定' }}
                                  </span>
                                <td>
                                    @switch($event->status)
                                        @case('draft') <span class="badge badge-ghost">下書き</span> @break
                                        @case('published') <span class="badge badge-info">公開中</span> @break
                                        @case('cancelled') <span class="badge badge-warning">中止</span> @break
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
