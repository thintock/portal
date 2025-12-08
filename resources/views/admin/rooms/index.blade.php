<x-admin-layout>
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">ルーム管理</h1>
            <p class="text-sm text-gray-500">ルームを作成・編集できます。</p>
        </div>
    @endsection
    
    <div class="w-full">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">ルーム管理（並び替え可能）</h3>
                <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">＋ ルーム作成</a>
            </div>

            {{-- ▼ ルーム一覧テーブル --}}
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>並び順</th>
                        <th>ルーム名</th>
                        <th>公開範囲</th>
                        <th>投稿権限</th>
                        <th>状態</th>
                        <th>操作</th>
                    </tr>
                </thead>

                <tbody id="sortable-body">
                    @forelse($rooms as $room)
                        <tr data-id="{{ $room->id }}" class="cursor-move hover:bg-base-200">

                            <!-- 並び順 -->
                            <td class="font-bold text-gray-500">
                                {{ $room->sort_order }}
                            </td>
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
                                <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}"
                                      onsubmit="return confirm('削除してよろしいですか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-error">削除</button>
                                </form>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-500 py-4">
                                ルームがまだ作成されていません
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- 並び順保存ボタン --}}
            <div class="mt-4 text-right">
                <button id="save-order" class="btn btn-primary">
                    並び順を保存
                </button>
            </div>

        </div>
    </div>

    {{-- ▼ Sortable.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

    <script>
        // ドラッグ&ドロップ有効化
        const sortable = new Sortable(document.getElementById('sortable-body'), {
            animation: 150,
            handle: 'tr',
        });

        // 保存ボタン処理
        document.getElementById('save-order').addEventListener('click', () => {

            const order = [];
            document.querySelectorAll('#sortable-body tr').forEach((row) => {
                order.push(row.dataset.id);
            });

            fetch("{{ route('admin.rooms.sort') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                body: JSON.stringify({ orders: order }),
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // 再読み込みで反映
                }
            });
        });
    </script>

</x-admin-layout>
