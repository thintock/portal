@props([
  'pointBalance' => 0,
  'pointActionRows' => collect(),
])

<div class="bg-base-100 border border-base-200 rounded-2xl p-4 sm:p-5 shadow-sm">
  <div class="flex items-start justify-between gap-3">
    <div>
      <h3 class="text-base sm:text-lg font-bold">ポイント</h3>
      <p class="text-xs sm:text-sm text-base-content/60">
        有効期限内のポイント残高です。
      </p>
    </div>

    <div class="text-right">
      <div class="text-xs text-base-content/60">現在のポイント</div>
      <div class="text-2xl sm:text-3xl font-extrabold">
        {{ number_format((int)$pointBalance) }}
        <span class="text-sm font-semibold">pt</span>
      </div>
    </div>
  </div>

  <div class="divider my-1 sm:my-3"></div>

  <div class="overflow-x-auto">
    <table class="table table-sm">
      <tbody>
        @foreach($pointActionRows as $row)
          <tr class="{{ $row['is_active'] ? '' : 'opacity-50' }}">
            <td class="font-medium">{{ $row['label'] }}</td>
            <td class="text-right">
              <span class="badge badge-ghost">
                + {{ number_format((int)$row['points']) }} pt
              </span>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>