<div class="max-w-4xl mx-auto px-1 py-8 sm:py-10 space-y-6">

  <div class="card bg-base-100 shadow-sm overflow-hidden">

    {{-- カバー --}}
    @if($cover)
      @php
        $coverUrl = $cover->url ?? Storage::url($cover->path);
      @endphp

      <figure class="w-full">
        <button
          type="button"
          class="block w-full"
          onclick="
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'image-viewer' }));
            window.dispatchEvent(new CustomEvent('set-image', { detail: { src: '{{ $coverUrl }}' } }));
          "
        >
          <img
            src="{{ $coverUrl }}"
            alt="{{ $announcement->title }}"
            class="w-full h-56 sm:h-72 object-cover"
          />
        </button>
      </figure>
    @endif

    <div class="card-body p-4 sm:p-8 space-y-4">

      {{-- 見出し --}}
      <div class="space-y-2">
        <div class="flex flex-wrap items-center gap-2 text-xs sm:text-sm text-base-content/60">
          <span>更新：{{ $announcement->updated_at?->format('Y/m/d H:i') }}</span>

          @if($announcement->visibility === 'public')
            <span class="badge badge-ghost badge-sm sm:badge-md">一般会員</span>
          @elseif($announcement->visibility === 'membership')
            <span class="badge badge-primary badge-sm sm:badge-md">ベイクル会員</span>
          @elseif($announcement->visibility === 'admin')
            <span class="badge badge-secondary badge-sm sm:badge-md">運営</span>
          @endif

          @if($announcement->publish_start_at)
            <span>公開開始：{{ $announcement->publish_start_at->format('Y/m/d') }}</span>
          @endif
          @if($announcement->publish_end_at)
            <span>公開終了：{{ $announcement->publish_end_at->format('Y/m/d') }}</span>
          @endif
        </div>

        <h1 class="text-xl sm:text-2xl font-bold leading-snug pb-2 border-b border-base-200">
          {{ $announcement->title }}
        </h1>
      </div>

      {{-- 本文（HTML可） --}}
      <div class="text-sm sm:text-base prose max-w-none">
        {!! $announcement->body !!}
      </div>

      {{-- ギャラリー --}}
      @if($gallery && $gallery->isNotEmpty())
        <div class="pt-2">
          <h2 class="font-semibold text-base-content mb-2">画像</h2>

          <div class="flex gap-2 overflow-x-auto pb-2">
            @foreach($gallery as $img)
              @php
                $url = $img->url ?? Storage::url($img->path);
              @endphp

              <button
                type="button"
                class="block flex-shrink-0"
                onclick="
                  window.dispatchEvent(new CustomEvent('open-modal', { detail: 'image-viewer' }));
                  window.dispatchEvent(new CustomEvent('set-image', { detail: { src: '{{ $url }}' } }));
                "
              >
                <img
                  src="{{ $url }}"
                  alt="announcement gallery image"
                  class="w-24 h-24 object-cover rounded-md border border-base-300 hover:opacity-90 transition"
                />
              </button>
            @endforeach
          </div>
        </div>
      @endif

      {{-- 戻る --}}
      <div class="pt-2 flex justify-end">
        <a href="{{ route('dashboard') }}" class="btn btn-xs sm:btn-sm btn-outline">
          ← ダッシュボードに戻る
        </a>
      </div>

    </div>
  </div>

</div>
