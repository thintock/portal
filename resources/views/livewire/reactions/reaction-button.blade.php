<div class="flex items-center p-2 select-none">
  {{-- LIKEボタン --}}
  <button wire:click="toggleLike" wire:loading.attr="disabled" class="flex items-center space-x-1 text-sm focus:outline-none transition">
    @if($liked)
      {{-- 押下状態 --}}
      <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" stroke="none" class="w-5 h-5 text-error">
        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09 C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5 c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
      </svg>
    @else
      {{-- 未押下状態 --}}
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 text-neutral hover:text-error transition">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.5c0 3.86-3.4 6.94-8.55 11.63L12 21.35l-0.45-0.42C5.4 15.44 2 12.36 2 8.5A4.5 4.5 0 017.5 4 5.5 5.5 0 0112 7a5.5 5.5 0 014.5-3A4.5 4.5 0 0121 8.5z"/>
      </svg>
    @endif
    <span class="text-xs text-neutral">
        {{ $likeCount > 0 ? $likeCount : '' }}
    </span>
  </button>
</div>
