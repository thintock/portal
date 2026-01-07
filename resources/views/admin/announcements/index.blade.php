<x-admin-layout>
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">お知らせ管理</h1>
            <p class="text-sm text-gray-500">運営からのお知らせを管理します。</p>
        </div>
    @endsection

    <div class="w-full">
        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-xl font-bold">お知らせ一覧</h1>
                <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary btn-sm">
                    ＋ 新規お知らせ作成
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="table table-zebra w-full text-sm">
                    <thead class="bg-base-200">
                        <tr>
                            <th>タイトル</th>
                            <th class="w-64">公開期間</th>
                            <th class="w-32">ステータス</th>
                            <th class="w-32">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcements as $announcement)
                            @php
                                $now = now();
                                $isPublished =
                                    (!$announcement->publish_start_at || $announcement->publish_start_at <= $now)
                                    && (!$announcement->publish_end_at || $announcement->publish_end_at >= $now);
                            @endphp

                            <tr>

                                <td class="font-semibold">
                                    <a href="{{ route('admin.announcements.edit', $announcement) }}"
                                       class="link link-primary">
                                        {{ $announcement->title }}
                                    </a>
                                </td>

                                <td>
                                    <div>
                                        {{ $announcement->publish_start_at
                                            ? $announcement->publish_start_at->format('Y/m/d H:i')
                                            : '未設定' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        〜 {{ $announcement->publish_end_at
                                            ? $announcement->publish_end_at->format('Y/m/d H:i')
                                            : '未設定' }}
                                    </div>
                                </td>

                                <td>
                                  
                                    @switch($announcement->visibility)
                                        @case('public')
                                            <span class="badge badge-success">一般公開</span>
                                            @break
                                        @case('membership')
                                            <span class="badge badge-info">会員限定</span>
                                            @break
                                        @case('admin')
                                            <span class="badge badge-ghost">運営のみ</span>
                                            @break
                                    @endswitch
                                    
                                    @if($isPublished)
                                        <span class="badge badge-primary mt-1">公開中</span>
                                    @else
                                        <span class="badge badge-outline mt-1">非公開</span>
                                    @endif
                                </td>

                                <td class="space-x-1">
                                    <a href="{{ route('admin.announcements.edit', $announcement) }}"
                                       class="btn btn-xs btn-outline">
                                        編集
                                    </a>

                                    <form method="POST"
                                          action="{{ route('admin.announcements.destroy', $announcement) }}"
                                          class="inline"
                                          onsubmit="return confirm('本当に削除しますか？');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-error">
                                            削除
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-6 text-gray-500">
                                    お知らせはまだ登録されていません。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $announcements->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
