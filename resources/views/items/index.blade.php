<x-app-layout>
    <x-slot name="title">Data Barang</x-slot>
    <x-slot name="section">Master Data</x-slot>

    <!-- Judul & tombol tambah -->
    <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-semibold">Daftar Barang</h2>
            <p class="text-sm text-stone-500">Kelola data master barang, kategori, satuan, dan batas stok minimum.</p>
        </div>
        <a href="{{ route('items.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-brand text-sm font-semibold text-white hover:bg-brand-dark">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
            Tambah Barang
        </a>
    </div>

    <x-card class="overflow-hidden">
        <!-- Form pencarian & filter. Method GET agar filter tampil di URL -->
        <form method="GET" action="{{ route('items.index') }}" class="flex flex-wrap items-end gap-3 p-4 border-b border-stone-200">
            <div class="flex-1 min-w-48">
                <x-input-label for="search" value="Cari barang" class="sr-only" />
                <x-text-input id="search" name="search" type="search" class="w-full" placeholder="Cari kode atau nama barang" :value="$search" />
            </div>

            <div>
                <x-input-label for="category_id" value="Kategori" class="sr-only" />
                <x-select-input id="category_id" name="category_id">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($categoryId == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </x-select-input>
            </div>

            <x-secondary-button type="submit">Terapkan</x-secondary-button>

            <span class="ml-auto text-sm text-stone-500">Total {{ $items->total() }} barang</span>
        </form>

        <!-- Tabel data barang -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-stone-50 text-left text-stone-500">
                    <tr>
                        <th class="px-4 py-3 font-medium">Kode</th>
                        <th class="px-4 py-3 font-medium">Nama Barang</th>
                        <th class="px-4 py-3 font-medium">Kategori</th>
                        <th class="px-4 py-3 font-medium text-right">Stok</th>
                        <th class="px-4 py-3 font-medium">Satuan</th>
                        <th class="px-4 py-3 font-medium text-right">Min. Stok</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($items as $item)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap font-medium">{{ $item->code }}</td>
                            <td class="px-4 py-3">{{ $item->name }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $item->category->name }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($item->stock, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ $item->unit }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($item->min_stock, 0, ',', '.') }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$item->status()" /></td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <!-- Tombol edit -->
                                    <a href="{{ route('items.edit', $item) }}" class="px-3 py-1.5 rounded-lg border border-stone-300 text-xs font-semibold hover:bg-stone-50">Edit</a>

                                    <!-- Tombol hapus: memakai form karena method-nya DELETE -->
                                    <form method="POST" action="{{ route('items.destroy', $item) }}" onsubmit="return confirm('Yakin ingin menghapus {{ $item->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg border border-red-200 text-xs font-semibold text-brand hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center">
                                <span class="block font-medium">Barang tidak ditemukan</span>
                                <span class="text-sm text-stone-500">Coba kata kunci lain atau ganti filter kategori.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Navigasi halaman (pagination) -->
        @if ($items->hasPages())
            <div class="p-4 border-t border-stone-200">
                {{ $items->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
