<x-admin-layout>
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">月次テーマ管理</h1>
            <p class="text-sm text-gray-500">
                毎月お届けする小麦・素材・テーマを管理します
            </p>
        </div>
    @endsection

    <div class="w-full">

        {{-- 新規作成 --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-lg font-bold">月次テーマ管理</h1>
                <a href="{{ route('admin.monthly-items.create') }}" class="btn btn-primary">＋ 新規テーマ作成</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full text-sm">
                    <thead>
                        <tr>
                            <th>対象月</th>
                            <th>商品名</th>
                            <th>フィードバック期間<br>件数</th>
                            <th>ステータス</th>
                            <th>登録日</th>
                            <th class="text-right">操作</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($monthlyItems as $item)
                            @php
                                $isActive =
                                    $item->status === 'published'
                                    && now()->between(
                                        $item->feedback_start_at,
                                        $item->feedback_end_at
                                    );
                            @endphp

                            <tr>
                                {{-- 対象月 --}}
                                <td class="font-medium">
                                    {{ $item->month }}
                                </td>

                                {{-- 商品名 --}}
                                <td class="min-w-[220px]">
                                    <div class="font-semibold">
                                        {{ $item->title ?? '—' }}
                                    </div>
                                </td>

                                {{-- フィードバック期間 --}}
                                <td class="min-w-[220px]">
                                    <div class="text-xs">
                                        {{ $item->feedback_start_at->format('Y/m/d H:i') }}
                                        〜
                                        {{ $item->feedback_end_at->format('Y/m/d H:i') }}
                                    </div>

                                    <div class="mt-1">
                                      <span class="badge badge-soft badge-secondary text-white">
                                        {{ $item->feedback_posts_count, '0' }}件
                                      </span>
                                      @if($isActive)
                                            <span class="badge badge-success text-white">
                                                受付中
                                            </span>
                                      @endif
                                    </div>
                                </td>

                                {{-- ステータス --}}
                                <td>
                                    @if($item->status === 'published')
                                        <span class="badge badge-primary text-white">
                                            公開
                                        </span>
                                    @else
                                        <span class="badge badge-neutral">
                                            下書き
                                        </span>
                                    @endif
                                </td>

                                {{-- 登録日 --}}
                                <td>
                                    {{ $item->created_at->format('Y/m/d') }}
                                </td>

                                {{-- 操作 --}}
                                <td class="text-right space-x-1">
                                    <a
                                        href="{{ route('admin.monthly-items.edit', $item) }}"
                                        class="btn btn-xs btn-info text-white"
                                    >
                                        編集
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.monthly-items.destroy', $item) }}"
                                        class="inline"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="btn btn-xs btn-error text-white"
                                            onclick="return confirm('削除してよろしいですか？')"
                                        >
                                            削除
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-sm text-gray-500 py-6">
                                    まだ月次アイテムは登録されていません。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
    
            {{-- ページネーション --}}
            <div class="mt-4">
                {{ $monthlyItems->links() }}
            </div>
    
        </div>
    </div>
</x-admin-layout>
