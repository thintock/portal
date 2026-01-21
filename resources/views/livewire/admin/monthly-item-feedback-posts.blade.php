<div class="space-y-4">

  {{-- 検索・表示件数・並び --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

    <label class="input input-bordered flex items-center gap-2 w-full md:max-w-md">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1 0 6.3 6.3a7.5 7.5 0 0 0 10.35 10.35z"/>
      </svg>
      <input type="text" class="grow focus:outline-none focus:ring-0 border-0" placeholder="タイトル・本文・ユーザー名で検索" wire:model.live.debounce.300ms="q">
    </label>

    <div class="flex items-center gap-2 justify-end">
      <select class="select select-bordered select-sm text-xs" wire:model.live="sort">
        <option value="newest">新しい順</option>
        <option value="oldest">古い順</option>
      </select>

      <select class="select select-bordered select-sm text-xs" wire:model.live="perPage">
        <option value="10">10件</option>
        <option value="20">20件</option>
        <option value="50">50件</option>
      </select>
    </div>
  </div>

  {{-- ローディング --}}
  <div wire:loading class="text-sm text-gray-500">読み込み中...</div>

  {{-- テーブル --}}
  <div class="overflow-x-auto">
    <table class="table table-zebra w-full">
      <thead>
        <tr>
          <th>ID</th>
          <th>投稿日時<br>投稿者</th>
          <th>タイトル</th>
        </tr>
      </thead>
      <tbody>
        @forelse($posts as $p)
          <tr class="align-top">
            <td class="font-mono">{{ $p->id }}</td>
            <td class="break-words">
              {{ optional($p->created_at)->format('Y/m/d H:i') }}<br>
              {{ $p->user?->name ?? '（不明）' }}
            </td>
            <td class="break-words">
              <div class="font-semibold">{{ $p->title }}</div>

              <div class="mt-2">
                <div class="text-sm text-gray-800">
                  {{ \Illuminate\Support\Str::limit(strip_tags($p->body), 200) }}
                </div>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center text-sm text-gray-500 py-8">
              メッセージはまだ投稿されていません。
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- ページネーション --}}
  <div>
    {{ $posts->links() }}
  </div>

</div>
