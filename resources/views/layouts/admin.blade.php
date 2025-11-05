<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="cupcake" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        管理画面 | {{ config('app.name', 'ベーカリスタポータル') }}
        @hasSection('title')
            | @yield('title')
        @endif
    </title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Icons & Favicons -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <meta name="theme-color" content="#ffffff">
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased bg-base-200 text-base-content overflow-x-hidden">

    <div class="flex min-h-screen">
        
        @include('commons.admin-left-navi')
        

        {{-- ===============================
            メインコンテンツエリア
        ================================ --}}
        <div class="flex-1 flex flex-col">
            @include('commons.admin-top-navi')
            

            {{-- メイン --}}
            <main class="flex-1 p-8 bg-base-200 overflow-y-auto">
                @include('commons.messages')
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- Livewire & Modals --}}
    @livewireScripts
    <x-modal name="image-viewer" maxWidth="2xl">
        <div x-data="{ src: '' }" x-on:set-image.window="src = $event.detail.src">
            <img :src="src" class="max-w-full max-h-screen object-contain mx-auto rounded shadow-lg">
        </div>
    </x-modal>
</body>
</html>
