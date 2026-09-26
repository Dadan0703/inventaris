<x-app-layout>
    <x-slot name="title">Barang Masuk</x-slot>
    <x-slot name="section">Transaksi</x-slot>

    <div class="mb-6">
        <h2 class="text-lg font-semibold">Barang Masuk</h2>
        <p class="text-sm text-stone-500">Catat penerimaan barang dari supplier ke gudang.</p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
        <!-- ===== Form catat barang masuk ===== -->
        <x-card class="p-6 xl:col-span-2 self-start">
            <h3 class="font-semibold">Catat Barang Masuk</h3>
            <p class="text-sm text-stone-500 mb-6">Stok bertambah otomatis setelah disimpan</p>

            <form method="POST" action="{{ route('stock-ins.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <!-- No. transaksi: hanya ditampilkan, dibuat otomatis oleh sistem -->
                    <div>
                        <x-input-label for="transaction_number" value="No. Transaksi" />
                        <x-text-input id="transaction_number" type="text" class="mt-1 w-full bg-stone-50" :value="$nextNumber" readonly />
                    </div>

                    <!-- Tanggal (bawaan: hari ini) -->
                    <div>
                        <x-input-label for="date" value="Tanggal" />
                        <x-text-input id="date" name="date" type="date" class="mt-1 w-full" :value="old('date', now()->toDateString())" required />
                        <x-input-error :messages="$errors->get('date')" class="mt-2" />
                    </div>
                </div>

                <!-- Pilih barang -->
                <div>
                    <x-input-label for="item_id" value="Barang" />
                    {{-- Dropdown dengan kolom pencarian, komponennya ada di components/searchable-select.blade.php --}}
                    <x-searchable-select id="item_id" name="item_id" class="mt-1" :options="$itemOptions" :selected="old('item_id')" placeholder="Pilih barang" />
                    <x-input-error :messages="$errors->get('item_id')" class="mt-2" />
                </div>

                <!-- Jumlah -->
                <div>
                    <x-input-label for="quantity" value="Jumlah" />
                    <x-text-input id="quantity" name="quantity" type="number" min="1" class="mt-1 w-full" placeholder="0" :value="old('quantity')" required />
                    <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                </div>

                <!-- Supplier -->
                <div>
                    <x-input-label for="supplier" value="Supplier" />
                    <x-text-input id="supplier" name="supplier" type="text" class="mt-1 w-full" placeholder="Contoh: PT Sumber Jaringan" :value="old('supplier')" required />
                    <x-input-error :messages="$errors->get('supplier')" class="mt-2" />
                </div>

                <!-- Keterangan -->
                <div>
                    <x-input-label for="description" value="Keterangan" />
                    <x-textarea-input id="description" name="description" class="mt-1 w-full" placeholder="Opsional">{{ old('description') }}</x-textarea-input>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="flex justify-end gap-3">
                    <x-secondary-button type="reset">Reset</x-secondary-button>
                    <x-primary-button>Simpan Transaksi</x-primary-button>
                </div>
            </form>
        </x-card>

        <!-- ===== Riwayat barang masuk ===== -->
        <x-card class="xl:col-span-3 overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 p-6">
                <h3 class="font-semibold">Riwayat Barang Masuk</h3>

                <!-- Form pencarian riwayat -->
                <form method="GET" action="{{ route('stock-ins.index') }}" class="flex gap-2">
                    <x-text-input name="search" type="search" aria-label="Cari riwayat" placeholder="Cari transaksi" :value="$search" />
                    <x-secondary-button type="submit">Cari</x-secondary-button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-stone-50 text-left text-stone-500">
                        <tr>
                            <th class="px-6 py-3 font-medium">Tanggal</th>
                            <th class="px-6 py-3 font-medium">No. Transaksi</th>
                            <th class="px-6 py-3 font-medium">Barang</th>
                            <th class="px-6 py-3 font-medium text-right">Jumlah</th>
                            <th class="px-6 py-3 font-medium">Supplier</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse ($stockIns as $stockIn)
                            <tr>
                                <td class="px-6 py-3 whitespace-nowrap">{{ $stockIn->date->translatedFormat('d M Y') }}</td>
                                <td class="px-6 py-3 whitespace-nowrap">{{ $stockIn->transaction_number }}</td>
                                <td class="px-6 py-3">{{ $stockIn->item->name }}</td>
                                <td class="px-6 py-3 text-right whitespace-nowrap font-semibold text-blue-800">+{{ number_format($stockIn->quantity, 0, ',', '.') }} {{ $stockIn->item->unit }}</td>
                                <td class="px-6 py-3">{{ $stockIn->supplier }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-stone-500">Belum ada transaksi barang masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($stockIns->hasPages())
                <div class="p-4 border-t border-stone-200">
                    {{ $stockIns->links() }}
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>
