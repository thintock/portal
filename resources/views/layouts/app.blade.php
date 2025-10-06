<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="cupcake">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased overflow-x-hidden">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @include('commons.messages')
                {{ $slot }}
                @livewireScripts
            </main>
        </div>
        {{--共通モーダルの定義--}}
        <x-modal name="image-viewer" maxWidth="2xl">
            <div x-data="{ src: '' }" x-on:set-image.window="src = $event.detail.src">
                <img :src="src" class="max-w-full max-h-screen object-contain mx-auto rounded shadow-lg">
            </div>
        </x-modal> 
          {{-- 投稿修正モーダル --}}
          @livewire('posts.post-edit-modal')
          @livewire('comments.comment-edit-modal')
    </body>
</html>
