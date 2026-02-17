<div class="flex items-center p-2 select-none">
  @auth
    {{-- 保存ボタン --}}
    <button
      type="button"
      wire:click="openModal"
      wire:loading.attr="disabled"
      class="flex items-center space-x-1 text-sm focus:outline-none transition"
      title="{{ $saved ? '保存済み' : '投稿を保存' }}"
    >
      @if($saved)
        {{-- 保存済み（塗り） --}}
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
             fill="currentColor" class="w-5 h-5 text-warning">
          <path fill-rule="evenodd"
                d="M6.32 2.577A49.255 49.255 0 0112 2.25c1.807 0 3.584.098 5.32.327A2.25 2.25 0 0119.25 4.8v16.372a.75.75 0 01-1.172.62L12 18.115l-6.078 3.677A.75.75 0 014.75 21.17V4.8a2.25 2.25 0 011.57-2.223z"
                clip-rule="evenodd" />
        </svg>
      @else
        {{-- 未保存（枠） --}}
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.8" stroke="currentColor"
             class="w-5 h-5 text-neutral hover:text-warning transition">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M17.25 6.75V21l-5.25-3.5L6.75 21V6.75A2.25 2.25 0 019 4.5h6A2.25 2.25 0 0117.25 6.75z" />
        </svg>
      @endif

      <span class="text-xs text-neutral">
        {{ $saved ? '保存済み' : '保存' }}
      </span>
    </button>

    {{-- モーダル --}}
    @if($showModal)
      <div class="modal modal-open">
        <div class="modal-box max-w-md">
          <h3 class="font-semibold text-base">
            {{ $saved ? '保存済み' : '投稿を保存' }}
          </h3>

          <div class="mt-4 space-y-4">
            {{-- 保存箱選択 --}}
            <div>
              <label class="label">
                <span class="label-text font-semibold">保存箱</span>
              </label>

              <select class="select select-bordered w-full"
                      wire:model="selectedCategoryId">
                <option value="">未分類</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                @endforeach
              </select>
              @if(!empty($selectedCategoryId))
                <div class="flex justify-end mt-2">
                  <button type="button"
                          class="btn btn-xs sm:btn-sm md:btn-md btn-outline btn-error"
                          x-on:click.prevent="if(confirm('この保存箱を削除しますか？（この保存箱に入っている保存投稿は未分類になります）')) { $wire.deleteCategory() }"
                          wire:loading.attr="disabled">
                    保存箱を削除
                  </button>
                </div>
              @endif

              @error('selectedCategoryId')
                <p class="text-xs text-error mt-1">{{ $message }}</p>
              @enderror
            </div>

            {{-- 新規保存箱作成 --}}
            <div>
              <label class="label">
                <span class="label-text font-semibold">新しい保存箱を作る</span>
              </label>

              <input type="text"
                     class="input input-bordered w-full"
                     placeholder="例：レシピ / お知らせ"
                     wire:model.defer="newCategoryName"
                     maxlength="50" />

              @error('newCategoryName')
                <p class="text-xs text-error mt-1">{{ $message }}</p>
              @enderror

            </div>
          </div>

          <div class="modal-action flex items-center justify-between">
            <div>
              @if($saved)
                <button type="button"
                        class="btn btn-xs sm:btn-sm md:btn-md btn-outline btn-error"
                        wire:click="remove"
                        wire:loading.attr="disabled">
                  保存を解除
                </button>
              @endif
            </div>

            <div class="space-x-2">
              <button type="button"
                      class="btn btn-xs sm:btn-sm md:btn-md btn-outline"
                      wire:click="closeModal">
                キャンセル
              </button>

              <button type="button"
                      class="btn btn-xs sm:btn-sm md:btn-md btn-primary"
                      wire:click="save"
                      wire:loading.attr="disabled">
                {{ $saved ? '更新する' : '保存する' }}
              </button>
            </div>
          </div>
        </div>

        {{-- 背景クリックで閉じる --}}
        <div class="modal-backdrop" wire:click="closeModal"></div>
      </div>
    @endif
  @endauth
</div>
