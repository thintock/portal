<x-admin-layout>
  @section('admin-header')
    <div class="flex items-start justify-between gap-3">
      <div>
        <h1 class="text-lg font-bold text-gray-800">ポイント設定</h1>
        <p class="text-sm text-gray-500">全体ルール（PointRule）を設定します。ルーム別は別画面で上書きできます。</p>
      </div>
      <a href="{{ route('admin.points.rooms') }}" class="btn btn-outline btn-sm">ルーム別上書きへ →</a>
    </div>
  @endsection

  @if(session('success'))
    <div class="alert alert-success">
      <span>{{ session('success') }}</span>
    </div>
  @endif

  <div class="bg-base-100 shadow-sm rounded-lg p-4 sm:p-6">
    <form method="POST" action="{{ route('admin.points.bulkUpdate') }}">
      @csrf

      <div class="overflow-x-auto">
        <table class="table table-sm">
          <thead>
            <tr>
              <th>アクション</th>
              <th>action_type</th>
              <th class="text-right">ポイント</th>
              <th>有効</th>
              <th class="text-right">ルーム別上書き数</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rows as $row)
              @php
                $actionType = $row['action_type'];
                $overrideCount = (int)($roomOverrideCounts[$actionType] ?? 0);
              @endphp
              <tr>
                <td class="font-medium">{{ $row['label'] }}</td>
                <td class="text-xs opacity-70">{{ $actionType }}</td>

                <td class="text-right">
                  <input
                    type="number"
                    name="rules[{{ $actionType }}][points]"
                    value="{{ (int)$row['points'] }}"
                    class="input input-bordered input-sm w-24 text-right"
                    step="1"
                  />
                </td>

                <td>
                  <label class="label cursor-pointer justify-start gap-2">
                    <input
                      type="hidden"
                      name="rules[{{ $actionType }}][is_active]"
                      value="0"
                    />
                    <input
                      type="checkbox"
                      name="rules[{{ $actionType }}][is_active]"
                      value="1"
                      class="checkbox checkbox-sm"
                      {{ $row['is_active'] ? 'checked' : '' }}
                    />
                    <span class="text-xs text-base-content/70">有効</span>
                  </label>
                </td>

                <td class="text-right">
                  <span class="badge badge-ghost">{{ number_format($overrideCount) }}</span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-4 flex justify-end">
        <button type="submit" class="btn btn-primary btn-sm">保存</button>
      </div>
    </form>

    <div class="mt-4 text-xs text-base-content/60">
      ※ ルーム別上書きが存在する場合、PointService はそちらを優先します（想定）。
    </div>
    @include('admin.points._users', [
      'totalOutstandingPoints' => $totalOutstandingPoints ?? 0,
      'usersWithPoints' => $usersWithPoints ?? collect(),
    ])
  </div>
</x-admin-layout>