{{-- resources/views/livewire/dashboard/partials/monthly.blade.php --}}

<div class="card bg-base-100 border border-base-200">
  <div class="card-body p-4 space-y-3">

    {{-- Header --}}
    <div class="flex items-center justify-between">
      <div class="text-lg font-bold">今月のテーマ</div>

      @if(!empty($monthlyItem))
        <a href="{{ route('monthly-items.index') }}" class="btn btn-xs btn-base-100">
          一覧へ
        </a>
      @endif
    </div>

    @if(empty($monthlyItem))
      <div class="text-sm text-gray-500">
        公開中の月次テーマがありません。
      </div>
    @else
      @php
        // month が "2026-01" 形式想定
        try {
          $monthLabel = \Carbon\Carbon::createFromFormat('Y-m', $monthlyItem->month)->format('Y年n月');
        } catch (\Throwable $e) {
          $monthLabel = (string) $monthlyItem->month;
        }

        $badgeClass = $monthlyBadge ?? 'badge-ghost';
        $badgeLabel = $monthlyLabel ?? '公開中';
      @endphp

      {{-- Content (single clickable area) --}}
      <a href="{{ route('monthly-items.show', $monthlyItem) }}"
         class="block rounded-xl border border-base-200 hover:border-primary transition bg-base-200">
        <div class="p-4">

          {{-- Mobile: stacked / Desktop: 2 columns --}}
          <div class="flex flex-col md:flex-row md:items-start gap-4">

            {{-- Left: text --}}
            <div class="min-w-0 flex-1 space-y-2">

              {{-- Row1: month + badge --}}
              <div class="flex items-center justify-between gap-3">
                <div class="text-sm text-gray-500">
                  {{ $monthLabel }}
                </div>
                <span class="badge {{ $badgeClass }}">
                  {{ $badgeLabel }}
                </span>
              </div>

              {{-- Row2: title --}}
              <div class="font-bold text-lg leading-snug break-words">
                {{ $monthlyItem->title }}
              </div>

              {{-- Row3: description --}}
              <div class="text-sm text-gray-700 break-words leading-relaxed">
                {{ \Illuminate\Support\Str::limit(strip_tags($monthlyItem->description), 180) }}
              </div>

              {{-- Row4: meta --}}
              <div class="text-xs text-gray-500">
                公開日：{{ optional($monthlyItem->published_at)->format('Y/m/d') }}
              </div>
            </div>

            {{-- Right: cover --}}
            @if(!empty($monthlyCover))
              <div class="w-full md:w-5/12 lg:w-4/12">
                <div class="aspect-[16/10] w-full overflow-hidden rounded-lg border border-base-200 bg-base-100">
                  <img
                    src="{{ $monthlyCover->url }}"
                    alt="cover"
                    class="w-full h-full object-cover"
                    loading="lazy"
                  >
                </div>
              </div>
            @endif

          </div>
        </div>
      </a>
    @endif

  </div>
</div>
