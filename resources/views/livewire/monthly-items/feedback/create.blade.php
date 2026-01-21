<div class="max-w-4xl mx-auto p-1 pt-4 pb-8 space-y-4">

  {{-- Livewireメッセージ --}}
  @include('commons.messages')

  @if($errors->any())
    <div class="alert alert-error">
      <div class="space-y-1">
        <div class="font-bold">入力内容を確認してください</div>
        <ul class="list-disc list-inside text-sm">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif

  <div class="flex items-start justify-between gap-3">
    <div class="min-w-0">
      <h1 class="text-xl sm:text-2xl font-bold">メッセージ作成</h1>
      <div class="text-sm text-gray-500 mt-1 break-words">
        対象：{{ \Carbon\Carbon::createFromFormat('Y-m', $monthlyItem->month)->format('Y年n月') }} / {{ $monthlyItem->title }}
      </div>
    </div>

    <a href="{{ route('monthly-items.show', $monthlyItem) }}" class="btn btn-sm btn-outline shrink-0">
      ← 戻る
    </a>
  </div>

  <div class="card bg-base-100 shadow border border-base-200">
    <div class="card-body p-2 sm:p-4 md:p-6 space-y-5">

      {{-- タイトル --}}
      <div>
        <label class="block font-semibold mb-1">タイトル</label>
        <input
          type="text"
          wire:model="title"
          class="input input-bordered w-full"
          placeholder="例：香りが最高でした / 焼き上がりがふわふわ 等"
        >
        @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- 本文 --}}
      <div>
        <label class="block font-semibold mb-1">本文</label>
        <textarea
          wire:model="body"
          class="textarea textarea-bordered w-full"
          rows="8"
          maxlength="1000"
          placeholder="生産者に伝えたいメッセージ、焼いたパンの内容や、香り、食感、食べた方の反応などを自由に書いてください。この投稿は実際に生産者にお届けします。（1000文字まで）"
        ></textarea>
        <div class="text-xs text-gray-500 mt-1">{{ mb_strlen((string)$body) }}/1000</div>
        @error('body') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- 画像（追加/削除/並べ替え/プレビュー） --}}
      <div>
        <div class="flex items-center justify-between mb-2">
          <label class="block font-semibold">画像（最大10枚）</label>
          @error('media') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 sm:gap-3">
          @foreach($media as $i => $file)
            <div class="relative border rounded-lg overflow-hidden bg-gray-50">
              <img
                src="{{ method_exists($file, 'temporaryUrl') ? $file->temporaryUrl() : '' }}"
                class="w-full h-24 sm:h-28 object-cover"
              />

              {{-- 削除 --}}
              <button
                type="button"
                wire:click="removeMedia({{ $i }})"
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
                @if($i < count($media) - 1)
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
            <input type="file" wire:model="newMedia" multiple accept="image/*" class="hidden" />
          </label>
        </div>

        <div wire:loading wire:target="newMedia" class="text-xs text-gray-500 mt-2">アップロード中...</div>
        @error('newMedia.*') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
      </div>

      <div class="flex justify-end">
        <button
          type="button"
          wire:click="save"
          class="btn btn-primary btn-sm md:btn-md w-full"
          wire:loading.attr="disabled"
          wire:target="save,newMedia"
        >
          送信する
        </button>
      </div>

    </div>
  </div>

</div>
