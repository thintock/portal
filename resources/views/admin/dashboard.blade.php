<x-admin-layout>
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">管理ダッシュボード</h1>
            <p class="text-sm text-gray-500"></p>
        </div>
    @endsection
    
    <div class="w-full space-y-6">

        {{-- ステータスカード --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat bg-base-100 shadow rounded-lg">
                <div class="stat-title">登録ユーザー数</div>
                <div class="stat-value text-primary">{{ number_format($stats['users_count']) }}</div>
                <div class="stat-desc">累計登録ユーザー</div>
            </div>

            <div class="stat bg-base-100 shadow rounded-lg">
                <div class="stat-title">アクティブサブスク</div>
                <div class="stat-value text-secondary">{{ number_format($stats['active_subscriptions']) }}</div>
                <div class="stat-desc">現在の有効契約</div>
            </div>

            <div class="stat bg-base-100 shadow rounded-lg">
                <div class="stat-title">総投稿数</div>
                <div class="stat-value text-accent">{{ number_format($stats['posts_count']) }}</div>
                <div class="stat-desc">全投稿合計</div>
            </div>

            <div class="stat bg-base-100 shadow rounded-lg">
                <div class="stat-title">総コメント数</div>
                <div class="stat-value text-info">{{ number_format($stats['comments_count']) }}</div>
                <div class="stat-desc">全コメント合計</div>
            </div>
        </div>

        {{-- 月次新規ユーザー推移 --}}
        <div class="bg-base-100 shadow-sm rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">月次新規ユーザー数</h3>
                <a href="{{ route('admin.users.index') }}" class="link link-primary text-sm">ユーザー管理へ</a>
            </div>
            <canvas id="usersChart" height="100"></canvas>
        </div>

        {{-- イベント情報 --}}
        <div class="bg-base-100 shadow-sm rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">直近イベント</h3>
                <a href="{{ route('admin.events.index') }}" class="link link-primary text-sm">イベント管理へ</a>
            </div>

            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>タイトル</th>
                            <th>定員</th>
                            <th>申込数</th>
                            <th>開催日</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentEvents as $event)
                            <tr>
                                <td class="font-medium">{{ $event->title }}</td>
                                <td>{{ $event->capacity ?? 'なし' }}</td>
                                <td>{{ $event->activeParticipants()->count() }}</td>
                                <td>{{ $event->starts_at?->format('Y/m/d') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 管理メニュー --}}
        <div class="bg-base-100 shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">管理メニュー</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm">👤 ユーザー</a>
                <a href="{{ route('admin.pages.index') }}" class="btn btn-outline btn-sm">📄 ページ</a>
                <a href="{{ route('admin.events.index') }}" class="btn btn-outline btn-sm">📅 イベント</a>
                <a href="" class="btn btn-outline btn-sm">📝 投稿</a>
            </div>
        </div>
    </div>

    {{-- ChartJS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('usersChart');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($userMonthlyCounts)) !!},
                datasets: [{
                    label: '新規ユーザー数',
                    data: {!! json_encode(array_values($userMonthlyCounts)) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.2)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
</x-admin-layout>
