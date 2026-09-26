<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <!-- Font Poppins -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- CSS & JavaScript -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-stone-100 text-stone-900">
        <div class="min-h-screen flex">

            <!-- Panel kiri berwarna merah (disembunyikan di layar kecil) -->
            <section class="hidden lg:flex w-[40%] flex-col justify-between bg-brand text-white p-16">
                <div class="flex items-center gap-3">
                    <x-application-logo class="w-12 h-12 bg-white text-brand" />
                    <span class="text-lg font-semibold">Inventaris</span>
                </div>

                <div class="max-w-md space-y-5">
                    <h2 class="text-4xl font-bold leading-tight">Kelola stok barang dengan rapi dan tercatat.</h2>
                    <p class="text-brand-light leading-relaxed">Catat barang masuk dan keluar, pantau stok yang menipis, dan unduh laporan kapan pun dibutuhkan.</p>
                </div>

                <span class="text-sm text-brand-light">&copy; {{ date('Y') }} Inventaris Pintar</span>
            </section>

            <!-- Panel kanan berisi form (login, lupa password, dll) -->
            <section class="flex-1 flex items-center justify-center p-6">
                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </section>
        </div>
    </body>
</html>
