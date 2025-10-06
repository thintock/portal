<div class="space-y-6">
  {{-- 新規投稿フォーム --}}
  @livewire('posts.post-create', ['room' => $room], key('post-create-'.$room->id))

  {{-- 投稿一覧 --}}
  @foreach ($posts as $post)
    @livewire('posts.post-card', ['post' => $post], key('post-card-'.$post->id))
  @endforeach

  {{-- 無限スクロール用トリガー --}}
  @if($posts->hasMorePages())
    <div x-data x-init="
        let observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              Livewire.dispatch('load-more-posts');
            }
          });
        });
        observer.observe($el);
      "class="h-10"></div>
  @endif
</div>
