<div class="max-w-4xl mx-auto p-1 pt-4 pb-8 space-y-4">

  {{-- Livewireメッセージ --}}
  @include('commons.messages')

  <div class="flex items-start justify-between gap-3">
    <div class="min-w-0">
      <h1 class="text-xl sm:text-2xl font-bold">メッセージ編集</h1>
      <div class="text-sm text-gray-500 mt-1 break-words">
        対象：{{ \Carbon\Carbon::createFromFormat('Y-m', $monthlyItem->month)->format('Y年n月') }} / {{ $monthlyItem->title }}
      </div>
    </div>

    <a href="{{ route('monthly-items.show', $monthlyItem) }}" class="btn btn-sm btn-outline shrink-0">
      ← 戻る
    </a>
  </div>

  <div class="card bg-base-100 shadow border border-base-200">
    <div class="card-body space-y-5 p-2 sm:p-4 md:p-6 text-sm text-gray-800 break-words">

      <div>
        <label class="block font-semibold mb-1">タイトル</label>
        <input type="text" wire:model="title" class="input input-bordered w-full">
        @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block font-semibold mb-1">本文</label>
        <textarea wire:model="body" class="textarea textarea-bordered w-full" rows="8" maxlength="1000"></textarea>
        <div class="text-xs text-gray-500 mt-1">{{ mb_strlen((string)$body) }}/1000</div>
        @error('body') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- 画像（プレビュー、削除、並び替え、追加） --}}
      <div>
        <div class="flex items-center justify-between mb-2">
          <label class="block font-semibold">画像（最大10枚）</label>
          @error('images') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 sm:gap-3">
          @foreach($images as $i => $file)
            <div class="relative border rounded-lg overflow-hidden bg-gray-50">
              @php
                $url = is_array($file)
                    ? Storage::disk(config('filesystems.default'))->url($file['path'])
                    : (method_exists($file, 'temporaryUrl') ? $file->temporaryUrl() : null);
              @endphp

              @if($url)
                <img src="{{ $url }}" class="w-full h-24 sm:h-28 object-cover">
              @endif

              {{-- 削除 --}}
              <button
                type="button"
                wire:click="removeImageAt({{ $i }})"
                class="absolute top-1 right-1 btn btn-xs btn-circle bg-red-500 text-white hover:bg-red-600"
              >
                ✕
              </button>

              {{-- 並び替え --}}
              <div class="absolute bottom-1 right-1 flex gap-1">
                @if($i > 0)
                  <button type="button" wire:click="moveUp({{ $i }})"
                          class="btn btn-xs btn-circle bg-gray-100 text-gray-700 hover:bg-gray-200">⬆</button>
                @endif
                @if($i < count($images) - 1)
                  <button type="button" wire:click="moveDown({{ $i }})"
                          class="btn btn-xs btn-circle bg-gray-100 text-gray-700 hover:bg-gray-200">⬇</button>
                @endif
              </div>
            </div>
          @endforeach

          {{-- 追加ボタン --}}
          <label class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 w-full h-24 sm:h-28 text-gray-400 hover:bg-gray-50 cursor-pointer transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span class="text-xs">追加</span>
            <input type="file" wire:model="newImages" multiple accept="image/*" class="hidden" />
          </label>
        </div>

        <div wire:loading wire:target="newImages" class="text-xs text-gray-500 mt-2">アップロード中...</div>
        @error('newImages.*') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror

        @if($hasChanges)
          <div class="mt-2 text-xs text-yellow-700">変更があります。更新してください。</div>
        @endif
      </div>

      {{-- 操作 --}}
      <div class="flex justify-end gap-2 flex-wrap pt-2">
        <button
          type="button"
          wire:click="save"
          class="btn btn-primary"
          wire:loading.attr="disabled"
          wire:target="save,newImages"
        >
          更新する
        </button>

        <button
          type="button"
          class="btn btn-error text-white"
          onclick="if(confirm('投稿を削除します。画像も含めて元に戻せません。よろしいですか？')) { @this.call('deletePost') }"
        >
          メッセージを削除
        </button>
      </div>

    </div>
  </div>
</div>
