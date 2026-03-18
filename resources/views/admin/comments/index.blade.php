<x-admin-layout>
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">コメント管理</h1>
            <p class="text-sm text-gray-500">コメント・返信の確認と削除ができます。</p>
        </div>
    @endsection

    <div class="space-y-6">

        {{-- 検索 --}}
        <div class="card bg-white shadow-sm">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('admin.comments.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <input
                        type="text"
                        name="q"
                        value="{{ $q }}"
                        class="input input-bordered w-full"
                        placeholder="本文・投稿者名・親投稿本文で検索"
                    />
                    <button type="submit" class="btn btn-primary">検索</button>
                    <a href="{{ route('admin.comments.index') }}" class="btn btn-outline">リセット</a>
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
                                <th>投稿者</th>
                                <th>【ルーム】<br>
                                親投稿/親コメント</th>
                                <th>本文</th>
                                <th>投稿日</th>
                                <th class="text-right">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($comments as $comment)
                                <tr>
                                    <td class="whitespace-nowrap">{{ $comment->id }}</td>
                                    <td class="whitespace-nowrap">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($comment->user->name ?? '不明'), 16) }}
                                    </td>
                                    <td class="min-w-[240px] text-xs text-gray-500">
                                        【{{ $comment->post?->room?->name ?? '-' }}】<br>
                                        @if($comment->parent)
                                            親コメント: {{ \Illuminate\Support\Str::limit(strip_tags($comment->parent->body), 80) }}
                                        @elseif($comment->post)
                                            投稿: {{ \Illuminate\Support\Str::limit(strip_tags($comment->post->body), 80) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="min-w-[240px]">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($comment->body), 120) }}
                                    </td>
                                    <td class="whitespace-nowrap text-xs text-gray-500">
                                        {{ $comment->created_at?->format('Y/m/d H:i') }}
                                    </td>
                                    <td class="text-right whitespace-nowrap">
                                        <div class="flex justify-end gap-2">
                                            @if($comment->post)
                                                <a href="{{ route('posts.show', $comment->post) }}" class="btn btn-xs btn-outline" target="_blank">
                                                    表示
                                                </a>
                                            @endif

                                            <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}"
                                                  onsubmit="return confirm('このコメントを削除しますか？');">
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
                                    <td colspan="8" class="text-center text-gray-500 py-8">
                                        コメントがありません。
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $comments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>