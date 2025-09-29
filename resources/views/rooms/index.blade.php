<x-app-layout>
  <div class="max-w-5xl mx-auto py-8">
    <h1 class="text-3xl font-bold mb-6">ルーム一覧</h1>

    <div class="grid md:grid-cols-2 gap-6">
      @foreach($rooms as $room)
        <div class="card bg-base-100 shadow-md border">
          <div class="card-body">
            <h2 class="card-title text-lg">
              <a href="{{ route('rooms.show', $room) }}" class="link link-primary">
                {{ $room->name }}
              </a>
            </h2>
            <p class="text-sm text-gray-600">{{ $room->description }}</p>
            <div class="flex justify-between items-center text-sm mt-3">
              @if($room->visibility === 'public')
                <span>👥 公開</span>
              @else
                <span>👥 {{ $room->membersCount() }} メンバー</span>
              @endif
              <span>📝 {{ $room->posts_count }} 投稿</span>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    <div class="mt-6">
      {{ $rooms->links() }}
    </div>
  </div>
</x-app-layout>
