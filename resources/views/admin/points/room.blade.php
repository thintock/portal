<x-admin-layout>
  @section('admin-header')
    <div class="flex items-start justify-between gap-3">
      <div>
        <h1 class="text-lg font-bold text-gray-800">ルーム別ポイント上書き</h1>
        <p class="text-sm text-gray-500">
          ルーム：<span class="font-semibold">{{ $room->name ?? ('Room#'.$room->id) }}</span>
        </p>
      </div>
      <div class="flex gap-2">
        <a href="{{ route('admin.points.rooms') }}" class="btn btn-ghost btn-sm">← ルーム一覧</a>
        <a href="{{ route('admin.points.index') }}" class="btn btn-outline btn-sm">全体ルール</a>
      </div>
    </div>
  @endsection

  @if(session('success'))
    <div class="alert alert-success">
      <span>{{ session('success') }}</span>
    </div>
  @endif

  <div class="bg-base-100 shadow-sm rounded-lg p-4 sm:p-6">
    <form method="POST" action="{{ route('admin.points.roomBulkUpdate', $room) }}">
      @csrf

      <div class="overflow-x-auto">
        <table class="table table-sm">
          <thead>
            <tr>
              <th>アクション</th>
              <th>action_type</th>
              <th class="text-right">上書きポイント</th>
              <th>有効</th>
              <th>未設定に戻す</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rows as $row)
              @php
                $actionType = $row['action_type'];
                $hasOverride = !is_null($row['points']) || !is_null($row['is_active']);
              @endphp

              <tr>
                <td class="font-medium">{{ $row['label'] }}</td>
                <td class="text-xs opacity-70">{{ $actionType }}</td>

                <td class="text-right">
                  <input
                    type="number"
                    name="rules[{{ $actionType }}][points]"
                    value="{{ $hasOverride ? (int)($row['points'] ?? 0) : '' }}"
                    class="input input-bordered input-sm w-28 text-right"
                    placeholder="(未設定)"
                    step="1"
                  />
                </td>

                <td>
                  <label class="label cursor-pointer justify-start gap-2">
                    <input type="hidden" name="rules[{{ $actionType }}][is_active]" value="0">
                    <input
                      type="checkbox"
                      name="rules[{{ $actionType }}][is_active]"
                      value="1"
                      class="checkbox checkbox-sm"
                      {{ ($row['is_active'] ?? true) ? 'checked' : '' }}
                    />
                    <span class="text-xs text-base-content/70">有効</span>
                  </label>
                </td>

                <td>
                  <label class="label cursor-pointer justify-start gap-2">
                    <input
                      type="checkbox"
                      name="rules[{{ $actionType }}][reset]"
                      value="1"
                      class="checkbox checkbox-sm"
                    />
                    <span class="text-xs text-base-content/70">削除して全体に戻す</span>
                  </label>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-4 flex justify-end gap-2">
        <button type="submit" class="btn btn-primary btn-sm">保存</button>
      </div>

      <div class="mt-3 text-xs text-base-content/60">
        ※ 未設定の場合は「全体ルール（PointRule）」にフォールバックする想定です。<br>
        ※ 「未設定に戻す」をチェックした行は、RoomPointRule レコードを削除します。
      </div>
    </form>
  </div>
</x-admin-layout>