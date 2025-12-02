<div class="max-w-5xl mx-auto px-4 py-10 space-y-8 bg-base-100">

  {{-- カバー画像 --}}
  @if($cover)
    <img 
      src="{{ $cover->url }}" 
      alt="{{ $event->title }}"
      class="w-full h-72 object-cover rounded-lg shadow cursor-pointer"
      @click="$dispatch('open-modal', 'image-viewer'); 
              $dispatch('set-image', { src: '{{ $cover->url }}' });"
    />
  @endif

  {{-- 開催情報計算 --}}
  @php
    $participants = $event->activeParticipants()
        ->with('user.mediaFiles')
        ->get()
        ->shuffle()
        ->take(10);
    $total = $event->activeParticipants()->count();
  @endphp

  {{-- タイトル・日時 --}}
  <div class="space-y-3">
    <h1 class="text-3xl font-bold">{{ $event->title }}</h1>

    <div class="flex flex-wrap items-center gap-x-2 text-sm text-base-content/70">
      <span>
        {{ $event->starts_at_tz?->isoFormat('YYYY年M月D日(ddd) HH:mm') }}
        @if($event->ends_at_tz)
          〜 {{ $event->ends_at_tz->isoFormat('HH:mm') }}
        @endif
      </span>

      {{-- ステータス --}}
      @if($event->is_joined)
        <span class="badge badge-outline">参加予定</span>
      @endif

      @if($event->is_full)
        <span class="badge badge-warning">満席</span>
      @endif

      @if($event->status === 'cancelled')
        <span class="badge badge-error">中止</span>
      @endif
    </div>
  </div>

  {{-- 概要 --}}
  @if($event->body1)
    <div class="prose max-w-none">
      <h2>概要</h2>
      <p>{{ $event->body1 }}</p>
    </div>
  @endif

  {{-- 詳細 --}}
  @if($event->body2)
    <div class="prose max-w-none">
      <h2>詳細</h2>
      {!! $event->body2 !!}
    </div>
  @endif

  {{-- 完了報告 --}}
  @if($event->body3)
    <div class="prose max-w-none text-gray-600">
      <h2>完了報告</h2>
      <p>{{ $event->body3 }}</p>
    </div>
  @endif

  {{-- ギャラリー --}}
  @if($gallery->isNotEmpty())
    <div class="mt-6">
      <h3 class="font-semibold mb-2">ギャラリー</h3>
      <div class="flex gap-2 overflow-x-auto pb-2">
        @foreach($gallery as $img)
          <img 
            src="{{ $img->url }}"
            class="w-24 h-24 object-cover rounded border border-base-300 flex-shrink-0 cursor-pointer"
            @click="$dispatch('open-modal', 'image-viewer'); 
                    $dispatch('set-image', { src: '{{ $img->url }}' })"
          />
        @endforeach
      </div>
    </div>
  @endif

  {{-- === 参加情報（section と統一） === --}}
  <div class="bg-base-200 rounded-lg p-3 text-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
    <div class="flex flex-wrap gap-x-4 gap-y-1">
      
      <span><strong>定員：</strong>
        @if(empty($event->capacity) || $event->capacity == 0)
          なし
        @else
          {{ $event->capacity }}名
        @endif
      </span>

      <span><strong>参加登録：</strong>{{ $event->recept ? '必要' : '不要' }}</span>

      <span><strong>現在：</strong>{{ $total }}名
        @if(!empty($event->capacity) && $event->capacity > 0)
          ／ {{ $event->capacity }}名
        @endif
      </span>

      <span><strong>受付状況：</strong>
        @if($event->is_full)
          <span class="text-red-600 font-semibold">定員に達しています</span>
        @else
          <span class="text-green-700">受付中</span>
        @endif
      </span>
    </div>
  </div>

  {{-- === 参加ボタン + アバター（section と完全同期） === --}}
  <div class="flex flex-wrap items-center justify-between gap-3 mt-2">

    {{-- 左エリア --}}
    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">

      {{-- ボタン群 --}}
      <div class="flex items-center gap-2">
        @if($event->is_joined)
          @if($event->join_url)
            <a href="{{ $event->join_url }}" 
               target="_blank" rel="noopener" 
               class="btn btn-sm btn-primary">
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

      {{-- アバター一覧 --}}
      <div class="flex -space-x-3 mt-2 sm:mt-0">
        @foreach($participants as $p)
          @php
            $avatar = $p->user->mediaFiles()->where('media_files.type', 'avatar')->first();
          @endphp

          <div class="w-9 h-9 rounded-full overflow-hidden bg-base-200 flex items-center justify-center border-2 border-base-100 shadow-sm"
               title="{{ $p->user->name }}">
            @if($avatar)
              <img src="{{ Storage::url($avatar->path) }}" class="w-full h-full object-cover" />
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

    {{-- 右エリア：戻る --}}
    <div class="flex justify-end w-full sm:w-auto mt-2 sm:mt-0">
      <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline">← 一覧に戻る</a>
    </div>
  </div>

</div>
