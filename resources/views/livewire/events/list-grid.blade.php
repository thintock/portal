<div>
  <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    @forelse($events as $event)
      @php
        $cover = $event->mediaFiles()->where('type', 'event_cover')->first();
      @endphp

      {{-- 状態に応じて背景色を切り替え --}}
      <div class="card shadow-sm overflow-hidden 
                  {{ $tab === 'past' ? 'bg-base-300 opacity-80 border' : 'bg-base-100' }}">

        {{-- カバー画像 --}}
        @if($cover)
          <figure class="h-40 overflow-hidden">
            <img 
              src="{{ $cover->url }}" 
              alt="{{ $event->title }}"
              class="w-full h-full object-cover transition hover:scale-105 duration-300"
            >
          </figure>
        @endif

        <div class="card-body gap-3">

          {{-- 開催日時 --}}
          <div class="text-xs text-base-content/70">
            {{ $event->starts_at_tz?->isoFormat('M/D(ddd) HH:mm') }}
            @if($event->ends_at_tz) 〜 {{ $event->ends_at_tz->isoFormat('HH:mm') }} @endif
            @if($event->recept)
              @if($event->is_joined) <span class="badge badge-outline ml-2">参加中</span> @endif
              @if($event->is_full)   <span class="badge badge-warning ml-2">満席</span> @endif
              @if($event->status === 'cancelled') <span class="badge badge-error ml-2">中止</span> @endif
            @endif
            @if($tab === 'past')
              <span class="badge badge-neutral ml-2">終了</span>
            @endif
          </div>

          {{-- タイトル --}}
          <h3 class="card-title leading-tight">
            {{ $event->title }}
          </h3>

          {{-- 本文（近日:body1 / 過去:body3） --}}
          @php
            $excerpt = $tab === 'past'
              ? \Illuminate\Support\Str::limit(strip_tags($event->body3 ?? ''), 100)
              : \Illuminate\Support\Str::limit(strip_tags($event->body1 ?? ''), 100);
          @endphp

          @if($excerpt)
            <p class="text-sm leading-relaxed">{{ $excerpt }}</p>
          @endif

          {{-- ボタン群 --}}
          <div class="card-actions justify-end">
            @if($tab === 'upcoming')
          
              {{-- ✅ 中止は最優先 --}}
              @if($event->status === 'cancelled')
                <button class="btn btn-sm btn-disabled" disabled>中止</button>
          
              {{-- ✅ 参加登録不要：RSVP非表示。join_urlがあれば会場入口のみ --}}
              @elseif(!$event->recept)
                @if($event->join_url)
                  <a href="{{ $event->join_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-primary">
                    会場入口
                  </a>
                @endif
          
              {{-- ✅ 参加登録が必要：満席/参加中に応じてRSVP --}}
              @else
                @if($event->is_joined)
                  @if($event->join_url)
                    <a href="{{ $event->join_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-primary">
                      会場入口
                    </a>
                  @endif
                  <livewire:events.rsvp-button :event="$event" wire:key="rsvp-{{ $event->id }}" />
          
                @elseif(!$event->is_full)
                  <livewire:events.rsvp-button :event="$event" wire:key="rsvp-{{ $event->id }}" />
          
                @else
                  <button class="btn btn-sm btn-disabled" disabled>満席</button>
                @endif
              @endif
          
              {{-- 詳細は記載仕様そのまま --}}
              <a href="{{ route('events.show', $event->slug ?? $event->id) }}" class="btn btn-sm">
                詳細
              </a>
          
            @else
              {{-- 過去イベントは詳細のみ --}}
              <a href="{{ route('events.show', $event->slug ?? $event->id) }}" class="btn btn-sm btn-ghost">
                詳細を見る
              </a>
            @endif
          </div>

        </div>
      </div>
    @empty
      <div class="col-span-full text-center text-sm text-base-content/60 py-8">
        イベントはありません
      </div>
    @endforelse
  </div>

  <div class="mt-4">
    {{ $events->links() }}
  </div>
</div>
