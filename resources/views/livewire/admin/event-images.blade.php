<div>
  @include('commons.messages')

  {{-- カバー画像 --}}
  <div class="mb-6">
    <label class="block font-semibold mb-1">カバー画像</label>

    <div class="relative mb-3">
      @if (is_array($cover))
        @php $url = Storage::url($cover['path']); @endphp
        <img src="{{ $url }}" class="rounded-lg w-full h-48 object-cover border">
      @elseif (is_object($cover) && method_exists($cover, 'temporaryUrl'))
        <img src="{{ $cover->temporaryUrl() }}" class="rounded-lg w-full h-48 object-cover border opacity-80">
      @endif

      @if($cover)
        <button wire:click="$set('cover', null)"
          class="absolute top-2 right-2 btn btn-xs btn-circle bg-red-500 text-white">✕</button>
      @endif
    </div>

    <input type="file" wire:model="cover" accept="image/*" class="hidden" id="cover_input">
    <label for="cover_input"
           class="flex items-center justify-center border border-dashed rounded-lg p-4 cursor-pointer hover:bg-gray-50 text-gray-500">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
      </svg>
      カバー画像を選択
    </label>
    <div wire:loading wire:target="cover" class="text-xs text-gray-500 mt-2">アップロード中...</div>
  </div>

  {{-- ギャラリー画像 --}}
  <div>
    <label class="block font-semibold mb-1">ギャラリー画像</label>

    <div class="grid grid-cols-3 gap-3 mb-3">
      @foreach($gallery as $i => $file)
        <div class="relative">
          @if(is_array($file))
            @php $url = Storage::url($file['path']); @endphp
            <img src="{{ $url }}" class="rounded-lg border object-cover w-full h-24">
          @elseif(is_object($file) && method_exists($file, 'temporaryUrl'))
            <img src="{{ $file->temporaryUrl() }}" class="rounded-lg border object-cover w-full h-24 opacity-80">
          @endif

          <button wire:click="removeGallery({{ $i }})"
            class="absolute top-1 right-1 btn btn-xs btn-circle bg-red-500 text-white">✕</button>

          <div class="absolute bottom-1 right-1 flex space-x-1">
            @if($i > 0)
              <button wire:click="moveUp({{ $i }})" class="btn btn-xs btn-circle bg-gray-200">⬆</button>
            @endif
            @if($i < count($gallery) - 1)
              <button wire:click="moveDown({{ $i }})" class="btn btn-xs btn-circle bg-gray-200">⬇</button>
            @endif
          </div>
        </div>
      @endforeach

      {{-- 追加ボタン --}}
      <label class="flex items-center justify-center rounded-lg border border-dashed border-gray-400 w-full h-24 cursor-pointer hover:bg-gray-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <input type="file" wire:model="newGallery" multiple accept="image/*" class="hidden" />
      </label>
    </div>

    <div wire:loading wire:target="newGallery" class="text-xs text-gray-500 mt-2">アップロード中...</div>
  </div>

  {{-- 保存ボタン --}}
  <div class="text-right mt-6">
    <button wire:click="save" class="btn btn-primary btn-sm"
            wire:loading.attr="disabled" wire:target="save,newGallery,cover">
      <span wire:loading wire:target="save">保存中...</span>
      <span wire:loading.remove>画像を保存</span>
    </button>
  </div>
</div>
