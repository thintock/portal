<div class="max-w-4xl mx-auto p-1 py-8 space-y-6">
  {{-- 戻るボタン --}}
  <div class="mb-4">
    <a href="{{ route('rooms.show', $post->room_id) }}" class="btn btn-outline btn-sm">
      ← ルーム
    </a>
  </div>

  {{-- 投稿カード（既存のPostCardコンポーネントを使う） --}}
  @livewire('posts.post-card', ['post' => $post, 'autoOpen' => true], key('post-card-' . $post->id))

</div>
