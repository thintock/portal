<x-admin-layout>
  @section('admin-header')
    <div>
      <h1 class="text-lg font-bold text-gray-800">ルーム別ポイント上書き</h1>
      <p class="text-sm text-gray-500">各ルームごとに、全体ルールを上書きできます。</p>
    </div>
  @endsection

  <div class="bg-base-100 shadow-sm rounded-lg p-4 sm:p-6">
    <div class="overflow-x-auto">
      <table class="table table-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>ルーム名</th>
            <th class="text-right">上書き件数</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($rooms as $room)
            @php $cnt = (int)($overrideCountsByRoom[$room->id] ?? 0); @endphp
            <tr>
              <td class="text-xs opacity-70">{{ $room->id }}</td>
              <td class="font-medium">{{ $room->name ?? ('Room#'.$room->id) }}</td>
              <td class="text-right"><span class="badge badge-ghost">{{ number_format($cnt) }}</span></td>
              <td class="text-right">
                <a href="{{ route('admin.points.room', $room) }}" class="btn btn-outline btn-xs">編集</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-4 flex justify-end">
      <a href="{{ route('admin.points.index') }}" class="btn btn-ghost btn-sm">← 全体ルールへ</a>
    </div>
  </div>
</x-admin-layout>