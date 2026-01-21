<div class="max-w-5xl mx-auto p-2 pt-4 pb-10 space-y-4">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold">月次テーマ一覧</h1>
    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-ghost">← ダッシュボード</a>
  </div>

  <div class="space-y-4">
    @forelse($items as $item)
      @php
        $cover = $item->mediaFiles->first();

        $badge = 'badge-ghost';
        $label = '公開中';
        if($item->isFeedbackOpen()) { $badge = 'badge-primary animate-pulse'; $label = 'メッセージ受付中'; }
        elseif($item->status === 'published' && $item->feedback_start_at && now()->lt($item->feedback_start_at)) { $badge = 'badge-warning'; $label = 'メッセージ受付開始前'; }
        elseif($item->isFeedbackClosed()) { $badge = 'badge-neutral'; $label = 'メッセージ受付終了'; }
      @endphp

      <a href="{{ route('monthly-items.show', $item) }}"
         class="card bg-base-100 border border-base-200 hover:border-primary transition">
        <div class="card-body p-4">

          {{-- 1レコード：スマホ=縦 / PC=左右2カラム --}}
          <div class="flex flex-col md:flex-row gap-4">

            {{-- 左：情報 --}}
            <div class="min-w-0 flex-1 space-y-2">

              {{-- 1行目：左=対象月 / 右=バッジ --}}
              <div class="flex items-center justify-between gap-3">
                <div class="text-sm text-gray-500">
                  {{ \Carbon\Carbon::createFromFormat('Y-m', $item->month)->format('Y年n月') }}
                </div>
                <span class="badge {{ $badge }}">{{ $label }}</span>
              </div>

              {{-- 2行目：タイトル --}}
              <div class="font-bold text-lg break-words">
                {{ $item->title }}
              </div>

              {{-- 3行目：本文 --}}
              <div class="text-sm text-gray-700 break-words">
                {{ \Illuminate\Support\Str::limit(strip_tags($item->description), 180) }}
              </div>

              <div class="text-xs text-gray-500">
                公開日：{{ optional($item->published_at)->format('Y/m/d') }}
              </div>
            </div>

            {{-- 右：カバー（スマホは下に回る） --}}
            @if($cover)
              <div class="w-full md:w-5/12 lg:w-4/12">
                <img
                  src="{{ $cover->url }}"
                  alt="cover"
                  class="w-full h-44 sm:h-52 md:h-44 lg:h-44 object-cover rounded-lg border border-base-200"
                  loading="lazy"
                >
              </div>
            @endif

          </div>
        </div>
      </a>

    @empty
      <div class="text-gray-500">公開中の月次テーマがありません。</div>
    @endforelse
  </div>
</div>
