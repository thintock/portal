<x-admin-layout>
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">投稿管理</h1>
            <p class="text-sm text-gray-500">投稿の確認と削除ができます。</p>
        </div>
    @endsection

    <div class="space-y-6">

        {{-- 検索 --}}
        <div class="card bg-white shadow-sm">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('admin.posts.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <input
                        type="text"
                        name="q"
                        value="{{ $q }}"
                        class="input input-bordered w-full"
                        placeholder="本文・投稿者名・ルーム名で検索"
                    />
                    <button type="submit" class="btn btn-primary">検索</button>
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline">リセット</a>
                </form>
            </div>
        </div>

        {{-- 一覧 --}}
        <div class="card bg-white shadow-sm">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ルーム</th>
                                <th>投稿者</th>
                                <th>本文</th>
                                <th>投稿日</th>
                                <th class="text-right">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($posts as $post)
                                <tr>
                                    <td class="whitespace-nowrap">{{ $post->id }}</td>
                                    <td class="whitespace-nowrap">
                                        {{ $post->room->name ?? '-' }}
                                    </td>
                                    <td class="whitespace-nowrap">
                                        {{ $post->user->name ?? '不明' }}
                                    </td>
                                    <td class="min-w-[280px]">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 140) }}
                                    </td>
                                    <td class="whitespace-nowrap text-xs text-gray-500">
                                        {{ $post->created_at?->format('Y/m/d H:i') }}
                                    </td>
                                    <td class="text-right whitespace-nowrap">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('posts.show', $post) }}" class="btn btn-xs btn-outline" target="_blank">
                                                表示
                                            </a>

                                            <form method="POST" action="{{ route('admin.posts.destroy', $post) }}"
                                                  onsubmit="return confirm('この投稿を削除しますか？');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-error">
                                                    削除
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-gray-500 py-8">
                                        投稿がありません。
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>