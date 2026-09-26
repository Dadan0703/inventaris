{{-- Sidebar: tetap tampil di layar besar, bisa dibuka/tutup di layar kecil --}}
<aside
    class="fixed inset-y-0 left-0 z-40 w-64 flex flex-col bg-white border-r border-stone-200 transform transition-transform lg:static lg:translate-x-0 print:hidden"
    x-bind:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    <!-- Logo -->
    <div class="flex items-center gap-3 px-6 py-5">
        <x-application-logo class="w-10 h-10 bg-brand text-white text-sm" />
        <div>
            <span class="block font-semibold">Inventaris</span>
            <span class="block text-xs text-stone-500">Manajemen Barang</span>
        </div>
    </div>

    <!-- Menu navigasi -->
    <nav class="flex-1 px-4 space-y-1" aria-label="Menu utama">
        <span class="block px-3 pt-2 pb-2 text-xs font-semibold tracking-wider text-stone-400">MENU</span>

        {{-- request()->routeIs() mengecek apakah halaman yang sedang dibuka sesuai nama route --}}
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1.5" /><rect x="14" y="3" width="7" height="5" rx="1.5" /><rect x="14" y="12" width="7" height="9" rx="1.5" /><rect x="3" y="16" width="7" height="5" rx="1.5" /></svg>
            Dashboard
        </x-sidebar-link>

        <x-sidebar-link :href="route('items.index')" :active="request()->routeIs('items.*')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linejoin="round" d="M21 8l-9-5-9 5 9 5 9-5zM3 8v8l9 5 9-5V8M12 13v8" /></svg>
            Data Barang
        </x-sidebar-link>

        <x-sidebar-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linejoin="round" d="M3 12V4a1 1 0 011-1h8l9 9-9 9-9-9z" /><circle cx="7.5" cy="7.5" r="1.5" /></svg>
            Kategori
        </x-sidebar-link>

        <x-sidebar-link :href="route('stock-ins.index')" :active="request()->routeIs('stock-ins.*')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0l-5-5m5 5l5-5M4 20h16" /></svg>
            Barang Masuk
        </x-sidebar-link>

        <x-sidebar-link :href="route('stock-outs.index')" :active="request()->routeIs('stock-outs.*')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0L7 9m5-5l5 5M4 20h16" /></svg>
            Barang Keluar
        </x-sidebar-link>

        <x-sidebar-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linejoin="round" d="M6 3h9l4 4v14H6zM14 3v5h5M9 13h7M9 17h7" /></svg>
            Laporan
        </x-sidebar-link>
    </nav>

    <!-- Info user yang sedang login -->
    <div class="border-t border-stone-200 p-4 space-y-2">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-lg p-2 hover:bg-stone-100">
            {{-- Inisial nama user, contoh: "Admin Gudang" menjadi "AG" --}}
            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-light text-brand-dark text-sm font-semibold">
                {{ Str::upper(Str::substr(Auth::user()->name, 0, 2)) }}
            </span>
            <span class="min-w-0">
                <span class="block text-sm font-semibold truncate">{{ Auth::user()->name }}</span>
                <span class="block text-xs text-stone-500">Lihat profil</span>
            </span>
        </a>

        {{-- Logout harus memakai method POST agar aman --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-stone-600 hover:bg-stone-100 hover:text-brand">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 4h3a2 2 0 012 2v12a2 2 0 01-2 2h-3M10 16l-4-4 4-4M6 12h10" /></svg>
                Keluar
            </button>
        </form>
    </div>
</aside>
