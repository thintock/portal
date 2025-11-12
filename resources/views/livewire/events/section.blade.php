<div class="space-y-6">
  {{-- セクションタイトル --}}
  <div class="text-center">
    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-base-content mb-2">
      イベント
    </h2>
    <p class="text-sm text-base-content/70">
      ベーカリスタの交流や学びの場 — 最新イベント情報
    </p>
  </div>
  {{-- 次回イベント（Next Up） --}}
  @if($next)
    @php
      $cover = $next->mediaFiles()->where('type', 'event_cover')->first();
      $gallery = $next->mediaFiles()->where('type', 'event_gallery')->orderBy('media_relations.sort_order')->get();

      // 参加者関連データ
      $participants = $next->activeParticipants()
          ->with('user.mediaFiles')
          ->get()
          ->shuffle()
          ->take(10);
      $total = $next->activeParticipants()->count();
    @endphp

    <div class="card lg:card-side bg-base-100 shadow-sm overflow-hidden">
      {{-- カバー画像 --}}
      <figure class="lg:w-1/3">
        <img
          src="{{ $cover->url ?? asset('images/default_event_cover.jpg') }}"
          alt="{{ $next->title }}"
          class="object-cover w-full h-full cursor-pointer"
          @click="$dispatch('open-modal', 'image-viewer'); $dispatch('set-image', { src: '{{ $cover->url ?? asset('images/default_event_cover.jpg') }}' });"
        />
      </figure>

      {{-- コンテンツ --}}
      <div class="card-body gap-3 lg:w-2/3">
        {{-- 開催日時・ステータス --}}
        <div class="flex flex-wrap items-center text-xs text-base-content/70 gap-x-2">
          <span>
            {{ $next->starts_at_tz?->isoFormat('M/D(ddd) HH:mm') }}
            @if($next->ends_at_tz)〜{{ $next->ends_at_tz->isoFormat('HH:mm') }}@endif
          </span>
          @if($next->is_joined)
            <span class="badge badge-outline">参加予定</span>
          @endif
          @if($next->is_full)
            <span class="badge badge-warning">満席</span>
          @endif
          @if($next->status === 'cancelled')
            <span class="badge badge-error">中止</span>
          @endif
        </div>

        {{-- タイトル --}}
        <h3 class="card-title text-lg lg:text-xl font-semibold mt-1">{{ $next->title }}</h3>

        {{-- 概要抜粋 --}}
        @php
          $excerpt = \Illuminate\Support\Str::limit(strip_tags($next->body1 ?? ''), 150);
        @endphp
        @if($excerpt)
          <p class="text-sm text-base-content/80 leading-relaxed">{{ $excerpt }}</p>
        @endif
        {{-- ギャラリー --}}
        @if($gallery->isNotEmpty())
          <div class="flex gap-2 mt-3 overflow-x-auto pb-2">
            @foreach($gallery as $img)
              @php
                $url = $img->url ?? Storage::url($img->path);
              @endphp
              <img
                src="{{ $url }}"
                alt="gallery image"
                class="w-20 h-20 object-cover rounded-md border border-base-300 flex-shrink-0 hover:opacity-80 transition cursor-pointer"
                @click="$dispatch('open-modal', 'image-viewer'); $dispatch('set-image', { src: '{{ $url }}' });"
              />
            @endforeach
          </div>
        @endif
        {{-- 参加情報（整理されたレイアウト） --}}
        <div class="bg-base-200 rounded-lg p-3 text-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
          <div class="flex flex-wrap gap-x-4 gap-y-1">
            <span><strong>定員：</strong>
              @if(empty($next->capacity) || $next->capacity == 0)
                なし
              @else
                {{ $next->capacity }}名
              @endif
            </span>
            <span><strong>参加登録：</strong>{{ $next->recept ? '必要' : '不要' }}</span>
            <span><strong>現在：</strong>{{ $total }}名
              @if(!empty($next->capacity) && $next->capacity > 0)
                ／ {{ $next->capacity }}名
              @endif
            </span>
            <span><strong>受付状況：</strong>
              @if($next->is_full)
                <span class="text-red-600 font-semibold">定員に達しています</span>
              @else
                <span class="text-green-700">受付中</span>
              @endif
            </span>
          </div>
        </div>

        {{-- ボタン + アバター一覧 --}}
        <div class="flex flex-wrap items-center justify-between gap-3 mt-2">
        
          {{-- 左：参加ボタン＋アバター --}}
          <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            {{-- ボタン群 --}}
            <div class="flex items-center gap-2">
              @if($next->is_joined)
                @if($next->join_url)
                  <a href="{{ $next->join_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-primary">
                    会場入口
                  </a>
                @endif
                <livewire:events.rsvp-button :event="$next" wire:key="rsvp-next-{{ $next->id }}" />
              @elseif(!$next->is_full)
                <livewire:events.rsvp-button :event="$next" wire:key="rsvp-next-{{ $next->id }}" />
              @else
                <button class="btn btn-sm btn-disabled" disabled>満席</button>
              @endif
            </div>
        
            {{-- アバター一覧（スマホでは次の行、PCでは横並びでボタンに重なる） --}}
            <div class="flex -space-x-3 mt-2 sm:mt-0">
              @foreach($participants as $p)
                @php
                  $avatar = $p->user->mediaFiles()->where('media_files.type', 'avatar')->first();
                @endphp
                <div class="w-9 h-9 rounded-full overflow-hidden bg-base-200 flex items-center justify-center border-2 border-base-100 shadow-sm"
                     title="{{ $p->user->name }}">
                  @if($avatar)
                    <img src="{{ Storage::url($avatar->path) }}" alt="avatar" class="w-full h-full object-cover">
                  @else
                    <span class="text-sm font-semibold text-gray-600">
                      {{ mb_substr($p->user->name ?? '？', 0, 1) }}
                    </span>
                  @endif
                </div>
              @endforeach
              @if($total > 10)
                <div class="w-9 h-9 rounded-full bg-base-200 flex items-center justify-center text-sm border-2 border-base-100">
                  +{{ $total - 10 }}
                </div>
              @endif
            </div>
          </div>
        
          {{-- 右：詳細ボタン --}}
          <div class="flex justify-end w-full sm:w-auto mt-2 sm:mt-0">
            @if (Route::has('events.show'))
              <a href="{{ route('events.show', $next->slug ?? $next->id) }}" class="btn btn-sm btn-outline">
                詳細
              </a>
            @endif
          </div>
        </div>
      </div>
    </div>
  @endif

  {{-- タブ --}}
  <div class="tabs tabs-boxed mt-6">
    <button class="tab {{ $tab==='upcoming' ? 'tab-active' : '' }}" wire:click="$set('tab','upcoming')">近日</button>
    <button class="tab {{ $tab==='past' ? 'tab-active' : '' }}" wire:click="$set('tab','past')">過去</button>
  </div>

  {{-- イベント一覧 --}}
  <livewire:events.list-grid :tab="$tab" :exclude-id="$next?->id" wire:key="list-{{ $tab }}" />
</div>
