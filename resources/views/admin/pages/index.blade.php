<x-app-layout>
    <div class="max-w-6xl mx-auto py-10 px-6">

        {{-- ✅ ページタイトルと新規作成 --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-lg font-bold">固定ページ管理</h1>
                <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">＋ 新規ページ作成</a>
            </div>

            {{-- ✅ 成功メッセージ --}}
            @if (session('success'))
                <div class="alert alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ✅ 一覧テーブル --}}
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>タイトル</th>
                        <th>スラッグ</th>
                        <th>作成者</th>
                        <th>更新者</th>
                        <th>状態</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $page)
                        <tr>
                            <td>{{ $page->id }}</td>
                            <td>
                                <a href="{{ route('admin.pages.edit', $page) }}" class="text-blue-600 hover:underline font-semibold">
                                    {{ $page->title ?? '（タイトル未設定）' }}
                                </a>
                            </td>
                            <td>{{ $page->slug }}</td>
                            <td>{{ $page->creator->name ?? '―' }}</td>
                            <td>{{ $page->updater->name ?? '―' }}</td>
                            <td>
                                @if($page->status === 'published')
                                    <span class="badge badge-success">公開</span>
                                @else
                                    <span class="badge badge-ghost">下書き</span>
                                @endif
                            </td>
                            <td class="flex space-x-2">
                                <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-outline">編集</a>
                                <a href="{{ route('admin.pages.show', $page) }}" class="btn btn-sm btn-info">表示</a>

                                <form method="POST"
                                      action="{{ route('admin.pages.destroy', $page) }}"
                                      onsubmit="return confirm('本当に削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-error">削除</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-500 py-4">
                                ページがまだ作成されていません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- ✅ ページネーション --}}
            @if(method_exists($pages, 'links'))
                <div class="mt-6">
                    {{ $pages->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
