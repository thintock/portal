<div class="card bg-base-100 shadow-md">
  <div class="card-body">
    
    {{--トグルボタン--}}
    <button type="button" class="btn btn-outline btn-sm mb-2" wire:click="toggleForm">
      {{ $showForm ? 'キャンセル' : '新しい投稿を作成' }}
    </button>
    
    {{--Livewireメッセージ--}}
    @include('commons.messages')
    @if($showForm)
      <form wire:submit.prevent="save" class="space-y-4">
        {{-- プレビュー + 画像追加ボタン --}}
        <div class="grid grid-cols-3 gap-3">
          {{-- 既存画像 --}}
          @foreach($media as $i => $file)
              <div class="relative">
                  <img src="{{ $file->temporaryUrl() }}" class="rounded-lg border object-cover w-full h-32" />
                  {{-- 削除ボタン --}}
                  <button type="button" wire:click="removeMedia({{ $i }})" 
                      class="absolute top-1 right-1 btn btn-xs btn-circle btn-neutral text-white">✕</button>
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
          @endforeach
      
          {{-- 画像追加ボタン（常に最後尾に出す） --}}
          <label class="flex items-center justify-center rounded-lg border border-dashed border-gray-400 w-full h-32 cursor-pointer hover:bg-gray-100">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 4v16m8-8H4" />
              </svg>
              <input type="file" wire:model="newMedia" accept="image/*,video/*" class="hidden" />
          </label>
        </div>
        {{-- アップロード中表示 --}}
        <div wire:loading wire:target="newMedia" class="text-xs text-gray-500 mt-2">アップロード中...</div>
        
        {{-- 自動リサイズ付きテキストエリア --}}
        <textarea 
          wire:model="body" 
          wire:key="post-body-{{ $formKey }}" 
          x-data 
          x-ref="textarea"
          x-init="$refs.textarea.style.height = $refs.textarea.scrollHeight + 'px'" 
          @input="$refs.textarea.style.height = 'auto'; $refs.textarea.style.height = $refs.textarea.scrollHeight + 'px'" 
          rows="3" 
          class="textarea textarea-bordered w-full mb-3" 
          placeholder="どんなことを共有したい？"></textarea>
          
        <button class="btn btn-primary btn-sm w-full" type="submit" wire:loading.attr="disabled" wire:target="newMedia,save">
            <span wire:loading wire:target="newMedia">アップロード中...</span>
            <span wire:loading wire:target="save">保存中...</span>
            <span wire:loading.remove wire:target="newMedia,save">シェア</span>
        </button>
        </div>
  
      </form>
    @endif
  </div>
</div>
