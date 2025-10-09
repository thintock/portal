<div class="mt-2 ml-8">

  {{-- フッター（返信／キャンセル切り替え） --}}
  <div class="flex justify-between items-center">
    <livewire:reactions.reaction-button :model="$parent" :key="'comment-like-'.$parent->id" />

    <button wire:click="toggleForm"
            class="btn btn-link btn-xs text-blue-500">
      {{ $isOpen ? 'キャンセル' : '返信' }}
    </button>
  </div>

  {{-- フォーム表示 --}}
  @if($isOpen)
    <form wire:submit.prevent="save" class="mt-2 space-y-3">

      {{-- プレビュー + 画像追加 --}}
      <div class="grid grid-cols-3 gap-3">
        {{-- 選択済みファイル --}}
        @foreach($media as $i => $file)
          @if(is_object($file) && method_exists($file, 'temporaryUrl'))
            <div class="relative">
              <img src="{{ $file->temporaryUrl() }}" class="rounded-lg border object-cover w-full h-24" />
              {{-- 削除 --}}
              <button type="button" wire:click="removeMedia({{ $i }})"
                      class="absolute top-1 right-1 btn btn-xs btn-circle btn-neutral text-base-100">✕</button>
              {{-- 並び替え --}}
              <div class="absolute bottom-1 right-1 flex space-x-1">
                @if($i > 0)
                  <button type="button" wire:click="moveUp({{ $i }})" class="btn btn-xs btn-circle">⬆</button>
                @endif
                @if($i < count($media) - 1)
                  <button type="button" wire:click="moveDown({{ $i }})" class="btn btn-xs btn-circle">⬇</button>
                @endif
              </div>
            </div>
          @endif
        @endforeach

        {{-- 追加ボタン（最大3個まで） --}}
        @if(count($media) < 3)
          <label class="flex items-center justify-center rounded-lg border border-dashed border-gray-400 w-full h-24 cursor-pointer hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <input type="file" wire:model="newMedia" accept="image/*,video/*" class="hidden" />
          </label>
        @endif
      </div>

      {{-- アップロード中インジケーター --}}
      <div wire:loading.flex wire:target="newMedia" class="items-center space-x-2 text-xs text-gray-500 mt-2">
        <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l3 3-3 3v-4a8 8 0 01-8-8z"></path>
        </svg>
        <span>アップロード中...</span>
      </div>

      {{-- テキスト入力 --}}
      <textarea wire:model.defer="body" rows="2"
        class="textarea textarea-bordered w-full"
        placeholder="返信を入力..."
        wire:key="reply-body-{{ $formKey }}"
        wire:loading.attr="disabled"
        wire:target="newMedia"></textarea>

      {{-- 送信ボタン --}}
      <button class="btn btn-primary btn-xs w-full"
              type="submit"
              wire:loading.attr="disabled"
              wire:target="newMedia,save">
        <span wire:loading wire:target="newMedia">アップロード中...</span>
        <span wire:loading wire:target="save">保存中...</span>
        <span wire:loading.remove wire:target="newMedia,save">返信送信</span>
      </button>
    </form>
  @endif
</div>
