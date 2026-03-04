@php
  /** @var \App\Models\User $user */
  /** @var int $pointBalance */
  /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $pointLedgers */
@endphp

<div class="card bg-white shadow p-4 mt-6">
  <div class="flex items-start justify-between gap-3">
    <div>
      <h3 class="font-bold mb-1">ポイント</h3>
      <p class="text-xs text-gray-500">現在のポイント残高と、付与/取消の履歴です。</p>
    </div>

    <div class="text-right">
      <div class="text-xs text-gray-500">現在のポイント</div>
      <div class="text-2xl font-extrabold">
        {{ number_format((int)$pointBalance) }}
        <span class="text-sm font-normal text-gray-600">pt</span>
      </div>
    </div>
  </div>

  <div class="divider my-3"></div>

  {{-- 履歴 --}}
  <div class="overflow-x-auto">
    <table class="table table-sm">
      <thead>
        <tr>
          <th class="whitespace-nowrap">日時</th>
          <th class="whitespace-nowrap">種別</th>
          <th class="whitespace-nowrap">操作の種類</th>
          <th class="text-right whitespace-nowrap">ポイント</th>
          <th class="whitespace-nowrap">ルーム</th>
          <th class="whitespace-nowrap">対象</th>
          <th class="whitespace-nowrap">期限</th>
          <th class="whitespace-nowrap">メモ</th>
        </tr>
      </thead>
      <tbody>
        @forelse($pointLedgers as $l)
          @php
            // ここはあなたの PointLedger のカラムに合わせて調整
            $points = (int)($l->delta ?? 0);

            // +/− の見た目
            $badgeClass = $points >= 0 ? 'badge-success' : 'badge-error';
            $sign = $points >= 0 ? '+' : '';

            // room 名を出したいなら、PointLedgerに room() リレーションを持たせるのが理想
            // ここでは room_id がある前提で、表示はIDのみ（必要なら eager load で name 出せます）
            $roomLabel = isset($l->room_id) && $l->room_id ? ('#'.$l->room_id) : '-';

            // subject表示（ポリモーフィック想定）
            $subjectLabel = '-';
            if (!empty($l->subject_type) && !empty($l->subject_id)) {
              $subjectLabel = class_basename($l->subject_type).'#'.$l->subject_id;
            }

            $expires = $l->expires_at ? \Carbon\Carbon::parse($l->expires_at) : null;
          @endphp

          <tr>
            <td class="whitespace-nowrap text-xs opacity-70">
              {{ optional($l->created_at)->format('Y/m/d H:i') }}
            </td>

            <td class="whitespace-nowrap">
              @if(isset($l->kind))
                <span class="badge badge-ghost badge-sm">{{ $l->kind }}</span>
              @else
                <span class="badge badge-ghost badge-sm">元帳</span>
              @endif
            </td>

            <td class="text-xs opacity-70 break-all">
              {{ $l->action_type ?? '-' }}
            </td>

            <td class="text-right whitespace-nowrap">
              <span class="badge {{ $badgeClass }} badge-sm">
                {{ $sign }}{{ number_format($points) }}pt
              </span>
            </td>

            <td class="whitespace-nowrap text-xs opacity-70">
              {{ $roomLabel }}
            </td>

            <td class="whitespace-nowrap text-xs opacity-70">
              {{ $subjectLabel }}
            </td>

            <td class="whitespace-nowrap text-xs opacity-70">
              @if($expires)
                {{ $expires->format('Y/m/d') }}
              @else
                -
              @endif
            </td>

            <td class="text-xs opacity-70 break-words">
              {{ $l->note ?? '-' }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-sm text-gray-500">履歴がありません。</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $pointLedgers->links() }}
  </div>
</div>