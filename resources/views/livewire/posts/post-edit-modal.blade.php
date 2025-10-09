<div>
  {{--Livewireメッセージ--}}
  @include('commons.messages')
  @if($showModal)
    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold">投稿を編集</h2>
          <button wire:click="$set('showModal', false)" class="btn btn-sm btn-circle btn-ghost">✕</button>
        </div>
        {{-- メディア一覧 --}}
        <div class="grid grid-cols-3 gap-3 mb-3">
          @foreach($media as $i => $file)
            <div class="relative">
              @if(is_object($file) && method_exists($file, 'temporaryUrl'))
                {{-- 🔹 新規アップロード直後 --}}
                <img src="{{ $file->temporaryUrl() }}" class="rounded-lg border object-cover w-full h-24">
              @elseif(is_array($file) && isset($file['path']))
                {{-- 既存メディア（DB由来） --}}
                @php
                  $ext = strtolower(pathinfo($file['path'], PATHINFO_EXTENSION));
                  $url = Storage::url($file['path']);
                @endphp
                @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                  <img src="{{ $url }}" class="rounded-lg border object-cover w-full h-24">
                @elseif(in_array($ext, ['mp4','mov','webm','avi']))
                  <video class="rounded-lg border w-full h-24" controls>
                    <source src="{{ $url }}" type="video/{{ $ext === 'mov' ? 'quicktime' : $ext }}">
                  </video>
                @else
                  <a href="{{ $url }}" class="text-blue-500 text-xs">添付を開く</a>
                @endif
              @endif

              {{-- 削除 --}}
              <button
                type="button"
                wire:click="removeMedia({{ $i }})"
                class="absolute top-1 right-1 btn btn-xs btn-circle btn-neutral text-white">
                ✕
              </button>

              {{-- 並べ替え --}}
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

          {{-- 追加ボタン --}}
          <label class="flex items-center justify-center rounded-lg border border-dashed border-gray-400 w-full h-24 cursor-pointer hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <input type="file" wire:model="newMedia" multiple accept="image/*,video/*" class="hidden"/>
          </label>
        </div>

        {{-- アップロード中 --}}
        <div wire:loading wire:target="newMedia" class="text-xs text-gray-500 mb-2">アップロード中...</div>

        {{-- 本文 --}}
        <textarea wire:model="body" rows="5" class="textarea textarea-bordered w-full mb-3"></textarea>
        {{-- ボタン --}}
        <button wire:click="save" class="btn btn-primary btn-sm w-full" wire:loading.attr="disabled" wire:target="newMedia,save">
          <span wire:loading wire:target="newMedia">アップロード中...</span>
          <span wire:loading wire:target="save">保存中...</span>
          <span wire:loading.remove wire:target="newMedia,save">保存</span>
        </button>
      </div>
    </div>
  @endif
</div>
