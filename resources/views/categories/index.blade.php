<x-app-layout>
    <x-slot name="title">Kategori</x-slot>
    <x-slot name="section">Master Data</x-slot>

    <!-- Judul & tombol tambah -->
    <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-semibold">Daftar Kategori</h2>
            <p class="text-sm text-stone-500">Kelompokkan barang agar lebih mudah dicari dan dilaporkan.</p>
        </div>
        <a href="{{ route('categories.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-brand text-sm font-semibold text-white hover:bg-brand-dark">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
            Tambah Kategori
        </a>
    </div>

    <x-card class="max-w-3xl overflow-hidden">
        <!-- Form pencarian -->
        <form method="GET" action="{{ route('categories.index') }}" class="flex flex-wrap items-center gap-3 p-4 border-b border-stone-200">
            <x-text-input name="search" type="search" class="flex-1 min-w-48" aria-label="Cari kategori" placeholder="Cari nama kategori" :value="$search" />
            <x-secondary-button type="submit">Cari</x-secondary-button>
        </form>

        <!-- Tabel kategori -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-stone-50 text-left text-stone-500">
                    <tr>
                        <th class="px-4 py-3 font-medium">Nama Kategori</th>
                        <th class="px-4 py-3 font-medium text-right">Jumlah Barang</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($categories as $category)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $category->name }}</td>
                            {{-- items_count berasal dari withCount('items') di controller --}}
                            <td class="px-4 py-3 text-right">{{ $category->items_count }} barang</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <!-- Tombol edit -->
                                    <a href="{{ route('categories.edit', $category) }}" class="px-3 py-1.5 rounded-lg border border-stone-300 text-xs font-semibold hover:bg-stone-50">Edit</a>

                                    <!-- Tombol hapus: memakai form karena method-nya DELETE -->
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Yakin ingin menghapus kategori {{ $category->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg border border-red-200 text-xs font-semibold text-brand hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-10 text-center text-stone-500">Kategori tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Navigasi halaman (pagination) -->
        @if ($categories->hasPages())
            <div class="p-4 border-t border-stone-200">
                {{ $categories->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
