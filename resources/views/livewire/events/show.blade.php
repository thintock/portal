<div class="max-w-5xl mx-auto px-4 py-10 space-y-6">

  {{-- ====== 事前計算 ====== --}}
  @php
    $coverUrl = $cover?->url ?? null;

    $participants = $event->activeParticipants()
        ->with('user.mediaFiles')
        ->get()
        ->shuffle()
        ->take(10);

    $total = $event->activeParticipants()->count();

    $isOngoing = $event->start_at && $event->end_at
        ? now()->between($event->start_at, $event->end_at)
        : false;

    // 投稿と同じ感覚で本文を整形（改行を活かす）
    $body1 = $event->body1 ? nl2br(e($event->body1)) : null;
    // body2 は HTML が入る前提でそのまま
    $body2 = $event->body2 ?? null;
    $body3 = $event->body3 ? nl2br(e($event->body3)) : null;
  @endphp

  {{-- ====== ページヘッダー（控えめ） ====== --}}
  <div class="flex items-center justify-between gap-3">
    <div>
      <h1 class="text-2xl sm:text-3xl font-bold text-base-content">
        {{ $event->title }}
      </h1>

      <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-base-content/70">
        <span>
          {{ $event->starts_at_tz?->isoFormat('YYYY年M月D日(ddd) HH:mm') }}
          @if($event->ends_at_tz)〜{{ $event->ends_at_tz->isoFormat('HH:mm') }}@endif
        </span>

        {{-- ステータスバッジ --}}
        @if($event->is_joined)
          <span class="badge badge-outline">参加予定</span>
        @endif

        @if($event->is_full)
          <span class="badge badge-warning">満席</span>
        @endif

        @if($event->status === 'cancelled')
          <span class="badge badge-error">中止</span>
        @endif

        {{-- 開催中（アニメーション） --}}
        @if($isOngoing && $event->status !== 'cancelled')
          <span class="badge badge-error animate-pulse">開催中</span>
        @endif
      </div>
    </div>

    {{-- 右上：戻る --}}
    <div class="hidden sm:flex">
      <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline">← 一覧に戻る</a>
    </div>
  </div>

  {{-- ====== メインカード ====== --}}
  <div class="card bg-base-100 shadow-sm overflow-hidden border border-base-200">

    {{-- カバー --}}
    @if($coverUrl)
      <figure class="relative">
        <img
          src="{{ $coverUrl }}"
          alt="{{ $event->title }}"
          class="w-full h-72 sm:h-80 object-cover cursor-pointer"
          @click="$dispatch('open-modal', 'image-viewer'); $dispatch('set-image', { src: '{{ $coverUrl }}' });"
        />
      </figure>
    @endif

    <div class="card-body gap-5">

      {{-- ====== 参加情報（カード内の情報ブロック） ====== --}}
      <div class="bg-base-200 rounded-xl p-4 text-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

          <div class="flex flex-wrap gap-x-6 gap-y-2">
            <span>
              <strong>定員：</strong>
              @if(empty($event->capacity) || (int)$event->capacity === 0)
                なし
              @else
                {{ $event->capacity }}名
              @endif
            </span>

            <span>
              <strong>参加登録：</strong>{{ $event->recept ? '必要' : '不要' }}
            </span>

            <span>
              <strong>現在：</strong>{{ $total }}名
              @if(!empty($event->capacity) && (int)$event->capacity > 0)
                ／ {{ $event->capacity }}名
              @endif
            </span>

            <span>
              <strong>受付状況：</strong>
              @if($event->is_full)
                <span class="text-red-600 font-semibold">定員に達しています</span>
              @else
                <span class="text-green-700">受付中</span>
              @endif
            </span>
          </div>

          {{-- ボタン群（右寄せ） --}}
          <div class="flex items-center gap-2 justify-end">
            @if($event->is_joined)
              @if($event->join_url)
                <a href="{{ $event->join_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-primary">
                  会場入口
                </a>
              @endif
              <livewire:events.rsvp-button :event="$event" wire:key="rsvp-show-{{ $event->id }}" />
            @elseif(!$event->is_full)
              <livewire:events.rsvp-button :event="$event" wire:key="rsvp-show-{{ $event->id }}" />
            @else
              <button class="btn btn-sm btn-disabled" disabled>満席</button>
            @endif
          </div>

        </div>

        {{-- 参加者アバター（情報ブロック下段） --}}
        <div class="mt-4 flex items-center justify-between gap-3">
          <div class="flex -space-x-3">
            @foreach($participants as $p)
              @php
                $avatar = $p->user->mediaFiles()->where('media_files.type', 'avatar')->first();
              @endphp
              <div class="w-9 h-9 rounded-full overflow-hidden bg-base-100 flex items-center justify-center border-2 border-base-200 shadow-sm"
                   title="{{ $p->user->name }}">
                @if($avatar)
                  <img src="{{ Storage::url($avatar->path) }}" alt="avatar" class="w-full h-full object-cover" />
                @else
                  <span class="text-sm font-semibold text-gray-600">
                    {{ mb_substr($p->user->name ?? '？', 0, 1) }}
                  </span>
                @endif
              </div>
            @endforeach

            @if($total > 10)
              <div class="w-9 h-9 rounded-full bg-base-100 flex items-center justify-center text-sm border-2 border-base-200">
                +{{ $total - 10 }}
              </div>
            @endif
          </div>

          <div class="text-xs text-base-content/60">
            参加者：{{ $total }}名
          </div>
        </div>
      </div>

      {{-- ====== 本文（カード式で section を分ける） ====== --}}
      @if($body1)
        <div class="space-y-2">
          <h2 class="text-base font-bold text-base-content">概要</h2>
          <div class="prose max-w-none text-base-content/80 leading-relaxed">
            {!! $body1 !!}
          </div>
        </div>
      @endif

      @if($body2)
        <div class="space-y-2">
          <h2 class="text-base font-bold text-base-content">詳細</h2>
          <div class="prose max-w-none text-base-content/80 leading-relaxed">
            {!! $body2 !!}
          </div>
        </div>
      @endif

      @if($body3)
        <div class="space-y-2">
          <h2 class="text-base font-bold text-base-content">完了報告</h2>
          <div class="prose max-w-none text-base-content/70 leading-relaxed">
            {!! $body3 !!}
          </div>
        </div>
      @endif

      {{-- ====== ギャラリー（カード内のサブカード感） ====== --}}
      @if($gallery->isNotEmpty())
        <div class="space-y-2">
          <h3 class="text-base font-bold text-base-content">ギャラリー</h3>

          <div class="flex gap-2 overflow-x-auto pb-2">
            @foreach($gallery as $img)
              @php
                $url = $img->url ?? Storage::url($img->path);
              @endphp
              <img
                src="{{ $url }}"
                alt="gallery image"
                class="w-24 h-24 object-cover rounded-lg border border-base-300 flex-shrink-0 cursor-pointer hover:opacity-90 transition"
                @click="$dispatch('open-modal', 'image-viewer'); $dispatch('set-image', { src: '{{ $url }}' });"
              />
            @endforeach
          </div>
        </div>
      @endif

      {{-- ====== フッター（モバイル用戻る） ====== --}}
      <div class="sm:hidden pt-2">
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline w-full">← 一覧に戻る</a>
      </div>

    </div>
  </div>

</div>
