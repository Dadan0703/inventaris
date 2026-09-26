<x-app-layout>
    <x-slot name="title">Tambah Barang</x-slot>
    <x-slot name="section">Master Data</x-slot>

    <x-card class="max-w-3xl p-6 sm:p-8">
        <h2 class="text-lg font-semibold mb-1">Tambah Barang</h2>
        <p class="text-sm text-stone-500 mb-6">Stok awal barang baru adalah 0. Tambahkan stok melalui menu Barang Masuk.</p>

        <form method="POST" action="{{ route('items.store') }}">
            @csrf

            {{-- Memanggil form yang sama dengan halaman edit --}}
            @include('items._form')

            <div class="flex justify-end gap-3 mt-8">
                <a href="{{ route('items.index') }}" class="px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm font-semibold hover:bg-stone-50">Batal</a>
                <x-primary-button>Simpan Barang</x-primary-button>
            </div>
        </form>
    </x-card>
</x-app-layout>
