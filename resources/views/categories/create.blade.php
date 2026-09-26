<x-app-layout>
    <x-slot name="title">Tambah Kategori</x-slot>
    <x-slot name="section">Master Data</x-slot>

    <x-card class="max-w-xl p-6 sm:p-8">
        <h2 class="text-lg font-semibold mb-6">Tambah Kategori</h2>

        <form method="POST" action="{{ route('categories.store') }}">
            @csrf

            {{-- Memanggil form yang sama dengan halaman edit --}}
            @include('categories._form')

            <div class="flex justify-end gap-3 mt-8">
                <a href="{{ route('categories.index') }}" class="px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm font-semibold hover:bg-stone-50">Batal</a>
                <x-primary-button>Simpan Kategori</x-primary-button>
            </div>
        </form>
    </x-card>
</x-app-layout>
