@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl',
    'full' => false,  {{-- true なら画面100% --}}
])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div
    x-data="{ show: @js($show) }"
    x-init="$watch('show', value => {
        document.body.classList.toggle('overflow-y-hidden', value)
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70"
    style="display: none;"
    @click.self="show = false"
>
    {{-- モーダル本体 --}}
    <div
        x-show="show"
        x-transition
        class="relative bg-white rounded-lg shadow-xl overflow-hidden
               {{ $full ? 'w-screen h-screen' : 'sm:w-full ' . $maxWidth }}"
    >
        {{-- 閉じるボタン --}}
        <button type="button"
            @click="show = false"
            class="absolute top-3 right-3 bg-black bg-opacity-50 text-white rounded-full p-2 hover:bg-opacity-80 z-50">
            ✕
        </button>

        {{-- スロット部分 --}}
        <div class="p-4 overflow-y-auto h-full">
            {{ $slot }}
        </div>
    </div>
</div>
