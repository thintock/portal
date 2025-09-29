<x-app-layout>
  <div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">投稿を編集</h1>

    <form method="POST" action="{{ route('rooms.posts.update', [$room->id, $post->id]) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
      @method('PUT')

      {{-- 本文 --}}
      <div>
        <label class="block font-semibold">本文</label>
        <textarea name="body" rows="5" class="textarea textarea-bordered w-full">{{ old('body', $post->body) }}</textarea>
        @error('body') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
      </div>

      {{-- 公開範囲 --}}
      <div>
        <label class="block font-semibold">公開範囲</label>
        <select name="visibility" class="select select-bordered w-full">
          <option value="public" {{ $post->visibility==='public'?'selected':'' }}>公開</option>
          <option value="members" {{ $post->visibility==='members'?'selected':'' }}>メンバーのみ</option>
          <option value="private" {{ $post->visibility==='private'?'selected':'' }}>非公開</option>
        </select>
      </div>

      {{-- URL --}}
      <div>
        <label class="block font-semibold">外部リンク</label>
        <input type="url" name="external_url" value="{{ old('external_url', $post->external_url) }}" class="input input-bordered w-full">
      </div>

      {{-- 添付ファイル --}}
      <div>
        <label class="block font-semibold">ファイル追加</label>
        <input type="file" name="media[]" class="file-input file-input-bordered w-full" multiple>
      </div>

      {{-- 既存の添付一覧 --}}
      @if($post->media_json)
        <div class="grid grid-cols-3 gap-2 mt-2">
          @foreach($post->media_json as $media)
            <div class="relative">
              <img src="{{ Storage::url($media) }}" class="rounded border max-h-32 object-cover">
            </div>
          @endforeach
        </div>
      @endif

      <div>
        <button class="btn btn-primary">更新する</button>
      </div>
    </form>
  </div>
</x-app-layout>
