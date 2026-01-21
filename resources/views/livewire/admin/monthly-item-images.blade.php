<div>
  {{-- メッセージ表示 --}}
  @include('commons.messages')

  {{-- =========================
       カバー画像
  ========================== --}}
  <div class="mb-8">
    <label class="block font-semibold mb-2">カバー画像</label>

    <div class="relative mb-3">
      @if ($cover)
        @php
          $disk = config('filesystems.default', 'public');
          $url = is_array($cover)
              ? Storage::disk($disk)->url($cover['path'])
              : (method_exists($cover, 'temporaryUrl') ? $cover->temporaryUrl() : null);
        @endphp
        @if($url)
          <img src="{{ $url }}" class="rounded-lg w-full h-48 object-cover border shadow-sm">
        @endif

        <button wire:click="removeCover"
          class="absolute top-2 right-2 btn btn-xs btn-circle bg-red-500 text-white hover:bg-red-600 shadow">
          ✕
        </button>
      @else
        <div class="w-full h-48 border-2 border-dashed rounded-lg flex items-center justify-center text-gray-400 bg-gray-50">
          <span class="text-sm">現在カバー画像は設定されていません</span>
        </div>
      @endif
    </div>

    <input type="file" wire:model="cover" accept="image/*" class="hidden" id="monthly_item_cover_input">
    <label for="monthly_item_cover_input"
           class="flex items-center justify-center border border-dashed rounded-lg p-4 cursor-pointer hover:bg-gray-50 text-gray-600">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
      </svg>
      カバー画像を選択
    </label>

    <div wire:loading wire:target="cover" class="text-xs text-gray-500 mt-2">アップロード中...</div>
  </div>

  {{-- =========================
       ギャラリー画像
  ========================== --}}
  <div>
    <label class="block font-semibold mb-2">ギャラリー画像</label>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 mb-3">
      @foreach($gallery as $i => $file)
        <div class="relative group border rounded-lg overflow-hidden bg-gray-50 hover:shadow">
          @php
            $disk = config('filesystems.default', 'public');
            $url = is_array($file)
                ? Storage::disk($disk)->url($file['path'])
                : (method_exists($file, 'temporaryUrl') ? $file->temporaryUrl() : null);
          @endphp

          @if($url)
            <img src="{{ $url }}" class="object-cover w-full h-28 transition duration-150 group-hover:opacity-90">
          @endif

          <button wire:click="removeGallery({{ $i }})"
                  class="absolute top-1 right-1 btn btn-xs btn-circle bg-red-500 text-white hover:bg-red-600 shadow">
            ✕
          </button>

          <div class="absolute bottom-1 right-1 flex space-x-1">
            @if($i > 0)
              <button wire:click="moveUp({{ $i }})"
                      class="btn btn-xs btn-circle bg-gray-100 text-gray-700 hover:bg-gray-200">⬆</button>
            @endif
            @if($i < count($gallery) - 1)
              <button wire:click="moveDown({{ $i }})"
                      class="btn btn-xs btn-circle bg-gray-100 text-gray-700 hover:bg-gray-200">⬇</button>
            @endif
          </div>
        </div>
      @endforeach

      <label class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 w-full h-28 text-gray-400 hover:bg-gray-50 cursor-pointer transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span class="text-xs">追加</span>
        <input type="file" wire:model="newGallery" multiple accept="image/*" class="hidden" />
      </label>
    </div>

    <div wire:loading wire:target="newGallery" class="text-xs text-gray-500 mt-2">アップロード中...</div>
  </div>

  {{-- =========================
       保存ボタン
  ========================== --}}
  <div class="flex items-center justify-end gap-3 mt-6">
    @if($hasChanges)
      <span class="text-sm text-yellow-600 font-medium animate-pulse">⚠ 変更があります。保存してください。</span>
    @endif

    <button wire:click="save" class="btn btn-primary btn-sm" wire:loading.attr="disabled" wire:target="save,newGallery,cover">
      <span wire:loading wire:target="save">保存中...</span>
      <span wire:loading.remove>画像を保存</span>
    </button>
  </div>

  <div class="mt-3 text-right text-xs text-gray-400">
    <span>最大30枚まで登録可能</span>
  </div>
</div>
