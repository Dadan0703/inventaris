<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Judul tab browser, contoh: "Data Barang - Inventaris" --}}
        <title>{{ isset($title) ? $title.' - ' : '' }}{{ config('app.name') }}</title>

        <!-- Font Poppins -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- CSS & JavaScript (Tailwind + Alpine.js) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-stone-100 text-stone-900">
        {{-- x-data: variabel Alpine.js untuk buka/tutup sidebar di layar kecil --}}
        <div x-data="{ sidebarOpen: false }" class="min-h-screen lg:flex">

            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Latar gelap saat sidebar terbuka di layar kecil -->
            <div x-show="sidebarOpen" x-on:click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black/40 lg:hidden print:hidden" x-cloak></div>

            <!-- Konten utama -->
            <div class="flex-1 min-w-0">
                <!-- Header halaman -->
                <header class="flex items-center justify-between gap-4 bg-white border-b border-stone-200 px-4 sm:px-8 py-4 print:hidden">
                    <div class="flex items-center gap-3">
                        {{-- Tombol menu, hanya muncul di layar kecil --}}
                        <button type="button" x-on:click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 rounded-lg text-stone-600 hover:bg-stone-100" aria-label="Buka menu">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        </button>

                        <div>
                            {{-- Nama bagian menu, contoh: "Master Data" --}}
                            <span class="block text-xs font-medium text-stone-500">{{ $section ?? '' }}</span>
                            {{-- Judul halaman --}}
                            <h1 class="text-xl font-bold">{{ $title ?? '' }}</h1>
                        </div>
                    </div>

                    {{-- Tanggal hari ini dalam Bahasa Indonesia, contoh: "Sabtu, 26 September 2026" --}}
                    <span class="hidden sm:block text-sm text-stone-500">{{ now()->translatedFormat('l, d F Y') }}</span>
                </header>

                <!-- Isi halaman -->
                <main class="p-4 sm:p-8">
                    <x-flash-message />

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
