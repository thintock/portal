<x-admin-layout>
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">管理ダッシュボード</h1>
            <p class="text-sm text-gray-500"></p>
        </div>
    @endsection
    {{-- 管理者向けお知らせ --}}
    <div class="w-full space-y-6">
    
        @if(($adminAnnouncements ?? collect())->isEmpty())
            <div class="p-4 bg-base-200 rounded-lg text-sm text-base-content/60">
                現在、管理者向けのお知らせはありません。
            </div>
        @else
            <ul class="list bg-base-100 rounded-box shadow-sm">
                <li class="p-2 text-xs opacity-60 tracking-wide">お知らせ</li>
                @foreach($adminAnnouncements as $a)
                    <li class="list-row py-2 hover:bg-white transition tracking-wide border-t border-base-200">
                        <a href="{{ route('admin.announcements.show', $a) }}" class="flex items-center justify-between gap-4 w-full px-2 py-1" >
                            {{-- タイトル --}}
                            <div class="min-w-0">
                                <p class="text-xs font-medium leading-snug line-clamp-2">
                                    {{ $a->title }}
                                </p>
                            </div>
    
                            {{-- 日付 --}}
                            <div class="text-xs text-base-content/60 whitespace-nowrap">
                                {{ $a->updated_at?->format('Y/m/d') }}
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif

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

        {{-- 月次グラフエリア --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        
            {{-- 新規ユーザー登録数 --}}
            <div class="bg-base-100 shadow-sm rounded-lg p-4">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-base font-semibold">新規ユーザー登録数</h3>
                    <a href="{{ route('admin.users.index') }}" class="link link-primary text-xs">
                        ユーザー管理へ
                    </a>
                </div>
                <canvas id="usersChart" height="90"></canvas>
            </div>
        
            {{-- ベイクル会員数 --}}
            <div class="bg-base-100 shadow-sm rounded-lg p-4">
                <h3 class="text-base font-semibold mb-3">ベイクル会員数</h3>
                <canvas id="subsChart" height="90"></canvas>
            </div>
        
            {{-- 月次 投稿数 --}}
            <div class="bg-base-100 shadow-sm rounded-lg p-4">
                <h3 class="text-base font-semibold mb-3">投稿数</h3>
                <canvas id="postsChart" height="90"></canvas>
            </div>
        
            {{-- 月次 コメント数 --}}
            <div class="bg-base-100 shadow-sm rounded-lg p-4">
                <h3 class="text-base font-semibold mb-3">コメント数</h3>
                <canvas id="commentsChart" height="90"></canvas>
            </div>
        
            {{-- 月次 いいね数 --}}
            <div class="bg-base-100 shadow-sm rounded-lg p-4">
                <h3 class="text-base font-semibold mb-3">いいね数</h3>
                <canvas id="likesChart" height="90"></canvas>
            </div>
        
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
                                <td>{{ $event->start_at?->format('Y/m/d') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
    <script>
      const labels = {!! json_encode(array_keys($userMonthlyCounts)) !!};
    
      const makeLine = (id, label, values) => {
        const ctx = document.getElementById(id);
        if (!ctx) return;
    
        new Chart(ctx, {
          type: 'line',
          data: {
            labels,
            datasets: [{
              label,
              data: values,
              borderWidth: 2,
              tension: 0.3,
              fill: true,
            }]
          },
          options: {
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } }
          }
        });
      };
    
      makeLine('subsChart', '契約中ユーザー数', {!! json_encode(array_values($subscriptionMonthlyCounts)) !!});
      makeLine('postsChart', '投稿数',         {!! json_encode(array_values($postMonthlyCounts)) !!});
      makeLine('commentsChart', 'コメント数',   {!! json_encode(array_values($commentMonthlyCounts)) !!});
      makeLine('likesChart', 'いいね数',       {!! json_encode(array_values($likeMonthlyCounts)) !!});
    </script>
</x-admin-layout>
