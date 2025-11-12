<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="cupcake" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ベーカリスタポータル') }}
        @hasSection('title')
         | @yield('title')
        @endif
    </title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <meta name="theme-color" content="#ffffff">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased overflow-x-hidden bg-base-200 text-base-content">
    <div class="min-h-screen flex flex-col">
        {{-- ナビゲーション --}}
        @livewire('layouts.navigation-menu', ['room' => $room ?? null])

        {{-- ページヘッダー --}}
        @isset($header)
            <header class="bg-base-100 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-lg font-semibold text-gray-700">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- ページ内容 --}}
        <main class="flex-1">
            @include('commons.messages')
            {{ $slot }}
        </main>

        {{-- フッター --}}
        @include('commons.footer')
    </div>

    {{-- 共通モーダル --}}
    <x-modal name="image-viewer" maxWidth="2xl">
        <div x-data="{ src: '' }" x-on:set-image.window="src = $event.detail.src">
            <img :src="src" class="max-w-full max-h-screen object-contain mx-auto rounded shadow-lg">
        </div>
    </x-modal>

    {{-- 投稿編集／コメント編集／通知／会員証モーダル --}}
    @livewire('posts.post-edit-modal')
    @livewire('comments.comment-edit-modal')
    @livewire('notifications.notification-modal')
    @livewire('membership-card.show')
    @livewireScripts
</body>
</html>
