<x-admin-layout>
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">ユーザー管理</h1>
            <p class="text-sm text-gray-500">ユーザーを確認・編集できます。</p>
        </div>
    @endsection

    <div class="w-full">
        {{-- 今月〜来月5日までの誕生日（最大20） --}}
        <div class="m-4">
            <div class="card bg-white shadow">
                <div class="card-body p-4 sm:p-6">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-gray-800">
                                誕生日（{{ $now->format('n/j') }} 〜 {{ $end->format('n/j') }}）
                            </h2>
                            <p class="text-xs sm:text-sm text-gray-500">
                                今月と来月5日までの誕生日ユーザー（最大20件）
                            </p>
                        </div>
                        <div class="badge badge-soft badge-secondary text-white">
                            {{ ($birthdayUsers ?? collect())->count() }}件
                        </div>
                    </div>
        
                    @if(($birthdayUsers ?? collect())->isEmpty())
                        <div class="mt-3 p-3 bg-base-200 rounded text-sm text-gray-600">
                            対象期間の誕生日ユーザーはいません。
                        </div>
                    @else
                        <div class="mt-4 overflow-x-auto">
                            <table class="table table-zebra w-full text-sm">
                                <thead>
                                    <tr>
                                        <th>ユーザー</th>
                                        <th>誕生日</th>
                                        <th>都道府県</th>
                                        <th class="text-right">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($birthdayUsers as $bu)
                                        @php
                                            $bday = ($bu->birthday_month && $bu->birthday_day)
                                                ? sprintf('%d/%d', $bu->birthday_month, $bu->birthday_day)
                                                : '—';
                                        @endphp
                                        <tr>
                                            <td class="min-w-[220px]">
                                                <div class="flex items-center gap-3">
                                                    @if($bu->avatar_url)
                                                        <div class="avatar">
                                                            <div class="w-9 rounded-full">
                                                                <img src="{{ $bu->avatar_url }}" alt="avatar" class="w-9 h-9 object-cover">
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="avatar placeholder">
                                                            <div class="bg-neutral text-neutral-content rounded-full w-9">
                                                                <span class="text-xs">{{ mb_substr($bu->name ?? '？', 0, 1) }}</span>
                                                            </div>
                                                        </div>
                                                    @endif
        
                                                    <div class="min-w-0">
                                                        <div class="font-medium">
                                                            {{ \Illuminate\Support\Str::limit($bu->name ?? '', 20, '…') }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            ID:{{ $bu->id }} / 会員番号: {{ $bu->member_number ?? '-' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
        
                                            <td>
                                                <span class="badge badge-soft badge-secondary text-white">
                                                    {{ $bday }}
                                                </span>
                                            </td>
        
                                            <td>{{ $bu->prefecture ?? '—' }}</td>
        
                                            <td class="text-right">
                                                <a href="{{ route('admin.users.edit', $bu) }}" class="btn btn-xs btn-info text-white">
                                                    編集
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <a href="{{ route('admin.csv.users.download') }}" class="btn btn-sm btn-outline m-4">
            ユーザーマスターCSVダウンロード
        </a>

        <div class="card bg-white shadow-xl">
            <div class="card-body p-4">
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full text-sm">
                        <thead>
                            <tr>
                                <th>
                                    <div class="badge badge-soft badge-secondary text-white">ID</div>
                                    <div class="badge badge-soft badge-primary text-white">
                                        会員番号
                                    </div>
                                </th>
                                <th>プロフィール</th>
                                <th>お名前</th>
                                <th>お住まい</th>
                                <th>会員登録日</th>
                                <th>ステータス</th>
                                <th>操作</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($users as $user)
                                @php
                                    // 必須プロフ判定（null/空文字両対応）
                                    $profileCompleted =
                                        filled($user->name) &&
                                        filled($user->first_name) &&
                                        filled($user->last_name) &&
                                        filled($user->postal_code) &&
                                        filled($user->prefecture) &&
                                        filled($user->address1) &&
                                        filled($user->address2);

                                    // 誕生日表示（両方ある時だけ）
                                    $birthday = ($user->birthday_month && $user->birthday_day)
                                        ? sprintf('%d/%d', $user->birthday_month, $user->birthday_day)
                                        : null;

                                    // 退会予定（ends_at が未来）
                                    $cancelScheduledAt = $user->subscription_cancel_scheduled_at;
                                    $hasCancelSchedule = $cancelScheduledAt && $cancelScheduledAt->isFuture();
                                @endphp

                                <tr>
                                    <td>
                                        <div class="flex flex-col gap-1">
                                            <div class="badge badge-soft badge-secondary text-white">ID:{{ $user->id }}</div>
                                            <div class="badge badge-soft badge-primary text-white">
                                                {{ $user->member_number ?? '-' }}
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        @if($user->avatar_url)
                                            <div class="avatar">
                                                <div class="w-10 rounded-full">
                                                    <img src="{{ $user->avatar_url }}" alt="プロフィール画像" class="w-10 h-10 rounded-full object-cover">
                                                </div>
                                            </div>
                                        @else
                                            <div class="avatar placeholder">
                                                <div class="bg-neutral text-neutral-content rounded-full w-10">
                                                    <span class="text-sm">{{ mb_substr($user->name ?? '？', 0, 1) }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                    <td class="min-w-[220px]">
                                        <div class="flex flex-col gap-1">
                                            <div class="font-medium">
                                                {{ \Illuminate\Support\Str::limit($user->name ?? '', 20, '…') }}
                                                （{{ trim(($user->last_name ?? '') . ' ' . ($user->first_name ?? '')) }}）
                                            </div>

                                            <div class="flex flex-wrap gap-2">
                                                @if($profileCompleted)
                                                    <span class="badge badge-primary badge-soft badge-sm text-white">プロフ完了</span>
                                                @else
                                                    <span class="badge badge-neutral badge-sm text-white">プロフ未完</span>
                                                @endif

                                                @if($birthday)
                                                    <span class="badge badge-secondary badge-sm text-white">誕生日：{{ $birthday }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="text-sm">
                                            {{ $user->prefecture ?? '—' }}
                                        </div>
                                    </td>

                                    <td>
                                        <div class="flex flex-col gap-1">
                                            <div>{{ $user->created_at?->format('Y/m/d') ?? '—' }}</div>

                                            <div class="text-xs">
                                                @if($user->email_verified_at)
                                                    <span class="badge badge-soft badge-primary badge-sm text-white">
                                                        認証：{{ $user->email_verified_at->format('Y/m/d') }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-neutral badge-sm text-white">未認証</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td class="min-w-[180px]">
                                        <div class="flex flex-col gap-1">
                                            <div>
                                                @if($user->subscribed('default'))
                                                    <span class="badge badge-soft badge-primary text-white">有効</span>
                                                @else
                                                    <span class="badge badge-error text-white">無効</span>
                                                @endif
                                            </div>

                                            <div class="text-xs text-gray-600">
                                                開始：{{ $user->subscription_started_at?->format('Y/m/d') ?? '—' }}
                                            </div>

                                            @if($hasCancelSchedule)
                                                <div class="text-xs">
                                                    <span class="badge badge-warning badge-sm">
                                                        退会予定：{{ $cancelScheduledAt->format('Y/m/d') }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="space-x-1">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-xs btn-info text-white">編集</a>

                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('削除してよろしいですか？')"
                                                    class="btn btn-xs btn-error text-white">
                                                削除
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
