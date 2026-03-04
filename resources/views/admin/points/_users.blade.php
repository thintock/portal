{{-- resources/views/admin/points/_users.blade.php --}}

<div class="mt-6 bg-base-100 shadow-sm rounded-lg p-4 sm:p-6 space-y-4">
  <div class="flex items-start justify-between gap-3">
    <div>
      <h2 class="text-base font-bold text-gray-800">ポイント保有状況</h2>
      <p class="text-xs text-gray-500">
        有効期限内（expires_at が null または未来）の point_ledgers.delta 合計で算出しています。
      </p>
    </div>

    <div class="text-right">
      <div class="text-xs text-gray-500">全ユーザーの保有ポイント合計</div>
      <div class="text-2xl font-extrabold">
        {{ number_format((int)($totalOutstandingPoints ?? 0)) }}
      </div>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="table table-sm">
      <thead>
        <tr>
          <th class="w-16">ID</th>
          <th>ユーザー</th>
          <th class="hidden sm:table-cell">メール</th>
          <th class="text-right w-40">保有ポイント</th>
          <th class="text-right w-28">操作</th>
        </tr>
      </thead>
      <tbody>
        @forelse($usersWithPoints as $u)
          @php
            $balance = (int)($u->point_balance ?? 0);
          @endphp
          <tr>
            <td class="text-xs opacity-70">{{ $u->id }}</td>

            <td class="min-w-0">
              <div class="font-medium break-words">{{ $u->name ?? '（未設定）' }}</div>
              <div class="text-xs text-gray-500 break-words">
                {{ trim(($u->last_name ?? '').' '.($u->first_name ?? '')) }}
              </div>
            </td>

            <td class="hidden sm:table-cell text-xs opacity-70 break-words">
              {{ $u->email ?? '-' }}
            </td>

            <td class="text-right font-bold">
              {{ number_format($balance) }}
            </td>

            <td class="text-right">
              <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-xs btn-outline">
                詳細
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center text-gray-500 py-6">
              ユーザーが見つかりません。
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="flex justify-end">
    {{ $usersWithPoints->links() }}
  </div>
</div>