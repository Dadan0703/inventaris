<x-app-layout>
    <x-slot name="title">Laporan</x-slot>
    <x-slot name="section">Laporan</x-slot>

    <!-- Judul & tombol export -->
    <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-semibold">Laporan {{ $types[$filters['type']] }}</h2>
            <p class="text-sm text-stone-500">Pilih jenis laporan, periode, dan kategori, lalu klik Tampilkan.</p>
        </div>

        <div class="flex gap-2 print:hidden">
            {{-- Export memakai filter yang sama dengan yang sedang tampil --}}
            <a href="{{ route('reports.export', $filters) }}" class="inline-flex items-center px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm font-semibold hover:bg-stone-50">
                Export Excel
            </a>

            {{-- Membuka jendela cetak browser. Pilih "Save as PDF" untuk menyimpan sebagai PDF. --}}
            <x-primary-button type="button" onclick="window.print()">
                Export PDF
            </x-primary-button>
        </div>
    </div>

    <!-- Form filter laporan -->
    <x-card class="p-4 mb-6 print:hidden">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <div>
                <x-input-label for="type" value="Jenis Laporan" />
                <x-select-input id="type" name="type" class="mt-1 w-full">
                    @foreach ($types as $key => $label)
                        <option value="{{ $key }}" @selected($filters['type'] == $key)>{{ $label }}</option>
                    @endforeach
                </x-select-input>
            </div>

            <div>
                <x-input-label for="start_date" value="Dari Tanggal" />
                <x-text-input id="start_date" name="start_date" type="date" class="mt-1 w-full" :value="$filters['start_date']" />
            </div>

            <div>
                <x-input-label for="end_date" value="Sampai Tanggal" />
                <x-text-input id="end_date" name="end_date" type="date" class="mt-1 w-full" :value="$filters['end_date']" />
            </div>

            <div>
                <x-input-label for="category_id" value="Kategori" />
                <x-select-input id="category_id" name="category_id" class="mt-1 w-full">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($filters['category_id'] == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </x-select-input>
            </div>

            <x-primary-button>Tampilkan</x-primary-button>
        </form>

        {{-- Pesan error jika filter tidak valid, misalnya tanggal akhir sebelum tanggal awal --}}
        <x-input-error :messages="$errors->all()" class="mt-3" />
    </x-card>

    <!-- Kartu ringkasan -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach ($report['summary'] as $label => $value)
            <x-card class="p-5">
                <span class="text-sm text-stone-500">{{ $label }}</span>
                <span class="block mt-1 text-2xl font-bold">{{ number_format($value, 0, ',', '.') }}</span>
            </x-card>
        @endforeach
    </div>

    <!-- Tabel laporan -->
    <x-card class="overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-2 p-6">
            {{-- Laporan stok barang menampilkan kondisi saat ini, jadi tidak memakai periode --}}
            @if ($filters['type'] == 'stok')
                <h3 class="font-semibold">Per {{ now()->translatedFormat('d F Y') }}</h3>
            @else
                <h3 class="font-semibold">
                    Periode {{ \Carbon\Carbon::parse($filters['start_date'])->translatedFormat('d F Y') }}
                    – {{ \Carbon\Carbon::parse($filters['end_date'])->translatedFormat('d F Y') }}
                </h3>
            @endif
            <span class="text-sm text-stone-500">{{ count($report['rows']) }} baris data</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-stone-50 text-left text-stone-500">
                    <tr>
                        @foreach ($report['columns'] as $column)
                            <th class="px-4 py-3 font-medium whitespace-nowrap">{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($report['rows'] as $row)
                        <tr>
                            @foreach ($row as $cell)
                                {{-- Angka diformat dengan titik ribuan dan rata kanan --}}
                                @if (is_int($cell))
                                    <td class="px-4 py-3 text-right">{{ number_format($cell, 0, ',', '.') }}</td>
                                @else
                                    <td class="px-4 py-3">{{ $cell }}</td>
                                @endif
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($report['columns']) }}" class="px-4 py-10 text-center text-stone-500">Tidak ada data pada filter ini.</td>
                        </tr>
                    @endforelse

                    {{-- Baris total (hanya untuk laporan mutasi) --}}
                    @if ($report['total'] && count($report['rows']) > 0)
                        <tr class="bg-stone-50 font-semibold">
                            @foreach ($report['total'] as $cell)
                                @if (is_int($cell))
                                    <td class="px-4 py-3 text-right">{{ number_format($cell, 0, ',', '.') }}</td>
                                @else
                                    <td class="px-4 py-3">{{ $cell }}</td>
                                @endif
                            @endforeach
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>
