<x-app-layout>
    <x-slot name="title">Edit Barang</x-slot>
    <x-slot name="section">Master Data</x-slot>

    <x-card class="max-w-3xl p-6 sm:p-8">
        <h2 class="text-lg font-semibold mb-1">Edit Barang</h2>
        <p class="text-sm text-stone-500 mb-6">Stok saat ini: <strong>{{ $item->stock }} {{ $item->unit }}</strong>. Stok hanya berubah lewat transaksi.</p>

        <form method="POST" action="{{ route('items.update', $item) }}">
            @csrf
            {{-- Form HTML hanya mendukung GET/POST, jadi method PUT ditulis dengan @method --}}
            @method('PUT')

            @include('items._form')

            <div class="flex justify-end gap-3 mt-8">
                <a href="{{ route('items.index') }}" class="px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm font-semibold hover:bg-stone-50">Batal</a>
                <x-primary-button>Simpan Perubahan</x-primary-button>
            </div>
        </form>
    </x-card>
</x-app-layout>
