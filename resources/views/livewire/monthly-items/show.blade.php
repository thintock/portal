<div class="max-w-4xl mx-auto p-1 pt-4 pb-8 space-y-4">
  @php
    $cover = $monthlyItem->mediaFiles->firstWhere('type', 'monthly_item_cover');
    $gallery = $monthlyItem->mediaFiles->where('type', 'monthly_item_gallery')->values();
  @endphp

  {{-- 上部：月次概要（スマホは縦、PCは横） --}}
  <div class="flex flex-col md:flex-row items-start justify-between gap-4">
    {{-- 左：テキスト --}}
    <div class="min-w-0 flex-1">
      @php
        $badge = 'badge-ghost';
        $label = '公開中';
        if($monthlyItem->isFeedbackOpen()) { $badge = 'badge-primary animate-pulse'; $label = 'メッセージ受付中'; }
        elseif($monthlyItem->status === 'published' && $monthlyItem->feedback_start_at && now()->lt($monthlyItem->feedback_start_at)) { $badge = 'badge-warning'; $label = 'メッセージ受付開始前'; }
        elseif($monthlyItem->isFeedbackClosed()) { $badge = 'badge-neutral'; $label = 'メッセージ受付終了'; }
      @endphp
      <div class="flex items-center justify-between gap-3">
        <div class="">
          {{ \Carbon\Carbon::createFromFormat('Y-m', $monthlyItem->month)->format('Y年n月') }}
        </div>
        <span class="badge {{ $badge }}">{{ $label }}</span>
      </div>
      <div class="mt-1 text-lg font-bold break-words">
        {{ $monthlyItem->title }}
      </div>

      <div class="text-sm mt-2 break-words">
        @php
          $fullText = trim(strip_tags($monthlyItem->description ?? ''));
          $isLong = mb_strlen($fullText) > 300;
        @endphp
        
        <div class="text-sm mt-2 break-words" x-data="{ open: false }">
          @if($isLong)
        
            {{-- 省略表示 --}}
            <div x-show="!open">
              {!! nl2br(e(\Illuminate\Support\Str::limit($fullText, 300))) !!}
              <button type="button"
                      class="text-blue-600 text-xs ml-2"
                      @click="open = true">
                つづきを表示
              </button>
            </div>
        
            {{-- 全文表示 --}}
            <div x-show="open" x-cloak>
              {!! nl2br(e($fullText)) !!}
              <button type="button"
                      class="text-blue-600 text-xs ml-2"
                      @click="open = false">
                閉じる
              </button>
            </div>
        
          @else
            {!! nl2br(e($fullText)) !!}
          @endif
        </div>
      </div>

      {{-- 受付情報 + ステータス --}}
      <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-600">
        @if($monthlyItem->feedback_start_at && $monthlyItem->feedback_end_at)
          <span class="badge badge-ghost">
            メッセージ受付期間：{{ $monthlyItem->feedback_start_at->format('Y/m/d H:i') }} 〜 {{ $monthlyItem->feedback_end_at->format('Y/m/d H:i') }}
          </span>
        @endif
      </div>
      <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-600">  
        {{-- 成分（あるものだけ） --}}
        @if($monthlyItem->proteinLabel()) <span class="badge badge-ghost">タンパク値 {{ $monthlyItem->proteinLabel() }}</span> @endif
        @if($monthlyItem->ashLabel()) <span class="badge badge-ghost">灰分 {{ $monthlyItem->ashLabel() }}</span> @endif
        @if($monthlyItem->absorptionLabel()) <span class="badge badge-ghost">吸水率 {{ $monthlyItem->absorptionLabel() }}</span> @endif
      </div>
    </div>

    {{-- 右：カバー画像（スマホは下に回る） --}}
    @if($cover)
      <div class="w-full md:w-5/12 lg:w-4/12">
        <img
          src="{{ $cover->url }}"
          alt="monthly cover"
          class="w-full h-44 sm:h-52 md:h-56 object-cover rounded-lg border border-base-200 cursor-pointer hover:opacity-90 transition"
          loading="lazy"
          @click="$dispatch('open-modal', 'image-viewer'); $dispatch('set-image', { src: '{{ $cover->url }}' })"
        >
      </div>
    @endif
  </div>

  @if($gallery->isNotEmpty())
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
      @foreach($gallery as $m)
        <img
          src="{{ $m->url }}"
          class="w-full h-28 sm:h-32 md:h-36 object-cover rounded border cursor-pointer hover:opacity-90 transition"
          loading="lazy"
          alt="monthly gallery"
          @click="$dispatch('open-modal', 'image-viewer'); $dispatch('set-image', { src: '{{ $m->url }}' })"
        >
      @endforeach
    </div>
  @endif
  
  <div class="divider"></div>
  
  {{-- CTA：未投稿のみ create / 投稿済みなら edit（受付中のみ） --}}
  @auth
    <div class="space-y-2">
      @if($canCreate)
        <div class="flex justify-end">
          <a href="{{ route('monthly-items.feedback.create', $monthlyItem) }}" class="btn btn-primary btn-sm md:btn-md w-full">
            メッセージを書く
          </a>
        </div>
        <div class="divider my-0"></div>
      @elseif($canEdit)
        <div class="flex justify-end">
          <a href="{{ route('monthly-items.feedback.edit', $monthlyItem) }}" class="btn btn-outline btn-sm md:btn-md w-full">
            自分のメッセージを編集する
          </a>
        </div>
        <div class="divider my-0"></div>
      @endif
    </div>
  @endauth

  {{-- 投稿一覧 --}}
  @forelse($monthlyItem->feedbackPosts as $post)
    <div class="card bg-base-100 border border-base-200">
      <div class="card-body space-y-3 p-2 sm:p-4 md:p-6 text-sm text-gray-800 break-words">

        <div class="flex items-start justify-between gap-3">
          <div class="flex items-center space-x-3 min-w-0">

            @php
              // eager load済み：$post->user->mediaFiles は avatar のみ入っている想定
              $avatar = $post->user?->mediaFiles?->first();
              $isBirthday = $post->user
                && $post->user->birthday_month == now()->month
                && $post->user->birthday_day == now()->day;
            @endphp

            {{-- アバター（クリックで会員証モーダル） --}}
            <div class="relative w-8 h-8 cursor-pointer flex items-center justify-center" @click="$dispatch('show-membership-card', { userId: {{ $post->user_id }} })" title="{{ $post->user?->name ?? 'ユーザー名未登録' }}">

              <div class="w-full h-full rounded-full overflow-hidden bg-base-200 border-2 flex items-center justify-center transition hover:scale-105 hover:border-primary">
                @if($avatar)
                  <img src="{{ $avatar->url }}" alt="avatar" class="w-full h-full object-cover" loading="lazy">
                @else
                  <span class="text-sm font-semibold text-gray-600">
                    {{ mb_substr($post->user?->name ?? '？', 0, 1) }}
                  </span>
                @endif
              </div>

              @if($isBirthday)
                <div class="absolute -top-2.5 -right-2.5 text-white text-[16px] rounded-full px-1.5 py-[1px] transform rotate-[40deg]">
                  👑
                </div>
              @endif
            </div>

            {{-- 投稿タイトル＋投稿者名＋日時 --}}
            <div class="min-w-0">
              <div class="text-lg font-bold break-words">{{ $post->title }}</div>

              <div class="text-xs text-gray-500 mt-1">
                <span class="font-semibold">{{ $post->user?->name ?? 'ユーザー名未登録' }}</span>
                <span class="ml-2">
                  {{ $post->created_at?->diffForHumans() }}
                  @if($post->updated_at && $post->updated_at->ne($post->created_at))
                    <span title="更新: {{ $post->updated_at->diffForHumans() }}">
                      （{{ $post->updated_at->diffForHumans() }}:編集済み）
                    </span>
                  @endif
                </span>
              </div>
            </div>
          </div>

          {{-- 自分の投稿なら編集（受付中のみ） --}}
          @if(auth()->id() === $post->user_id && $monthlyItem->isFeedbackOpen())
            <a href="{{ route('monthly-items.feedback.edit', $monthlyItem) }}" class="btn btn-xs btn-outline">
              編集
            </a>
          @endif
        </div>

        {{-- 添付画像（モーダル拡大） --}}
        @if($post->mediaFiles->isNotEmpty())
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
            @foreach($post->mediaFiles as $m)
              <img src="{{ $m->url }}" class="w-full h-36 sm:h-40 object-cover rounded border cursor-pointer hover:opacity-90 transition" loading="lazy" alt="feedback image" @click="$dispatch('open-modal', 'image-viewer'); $dispatch('set-image', { src: '{{ $m->url }}' });">
            @endforeach
          </div>
        @endif

        <div class="prose max-w-none text-sm">
          {!! nl2br(e($post->body)) !!}
        </div>

      </div>
    </div>
  @empty
    <div class="text-gray-500">まだ投稿がありません。</div>
  @endforelse

  <div class="pt-2 flex items-center justify-between gap-2">
    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-ghost">← ダッシュボードへ</a>
    <a href="{{ route('monthly-items.index') }}" class="btn btn-sm btn-ghost">月次テーマ一覧</a>
  </div>

</div>
