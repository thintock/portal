{{-- ルーム一覧 --}}
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
  @foreach($rooms as $room)
    <a
      href="{{ route('rooms.show', $room) }}"
      class="block group"
    >
      <div class="card bg-base-100 shadow-md border overflow-hidden transition
                  group-hover:shadow-lg group-hover:-translate-y-0.5">

        {{-- カバー画像 --}}
        <div class="flex md:block">
          <div class="w-28 md:w-full md:h-32 shrink-0 bg-base-200 flex items-center justify-center">
            @if($room->cover_media)
              <img
                src="{{ Storage::url($room->cover_media->path) }}"
                alt="cover"
                class="
                  h-full w-auto
                  md:w-full md:h-full
                  object-contain
                  object-cover
                "
              >
            @else
              <div class="w-full h-full bg-gradient-to-r from-accent/30 to-primary/40"></div>
            @endif
          </div>

          {{-- 本文 --}}
          <div class="card-body p-3 md:p-4 flex-1">
            <div class="flex items-center space-x-2">
              {{-- アイコン --}}
              @if($room->icon_media)
                <img
                  src="{{ Storage::url($room->icon_media->path) }}"
                  alt="icon"
                  class="w-8 h-8 rounded-full object-cover"
                >
              @else
                <img
                  src="{{ asset('images/bakele_logo.png') }}"
                  alt="icon"
                  class="w-8 h-8 rounded-full bg-gray-300"
                >
              @endif

              {{-- ルーム名（リンク解除） --}}
              <h2 class="text-sm md:text-lg font-semibold text-primary group-hover:underline">
                {{ $room->name }}
              </h2>
            </div>

            <p class="text-xs text-gray-600 md:mt-2 line-clamp-3">
              {!! \Illuminate\Support\Str::limit(strip_tags($room->description), 120) !!}
            </p>

            <div class="flex justify-between items-center text-xs sm:text-sm md:mt-3 text-gray-600">
              @if($room->visibility === 'public')
                <span>👥 公開</span>
              @else
                <span>👥 {{ $room->membersCount() }} メンバー</span>
              @endif
              <span>📝 {{ $room->posts_count }} 投稿</span>
            </div>
          </div>
        </div>

      </div>
    </a>
  @endforeach
</div>


{{-- ページネーション --}}
<div class="mt-6"></div>
