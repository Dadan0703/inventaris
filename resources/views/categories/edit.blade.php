<x-app-layout>
    <x-slot name="title">Edit Kategori</x-slot>
    <x-slot name="section">Master Data</x-slot>

    <x-card class="max-w-xl p-6 sm:p-8">
        <h2 class="text-lg font-semibold mb-6">Edit Kategori</h2>

        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf
            {{-- Form HTML hanya mendukung GET/POST, jadi method PUT ditulis dengan @method --}}
            @method('PUT')

            @include('categories._form')

            <div class="flex justify-end gap-3 mt-8">
                <a href="{{ route('categories.index') }}" class="px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm font-semibold hover:bg-stone-50">Batal</a>
                <x-primary-button>Simpan Perubahan</x-primary-button>
            </div>
        </form>
    </x-card>
</x-app-layout>
