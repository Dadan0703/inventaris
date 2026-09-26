<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="section">Beranda</x-slot>

    <!-- ===== Kartu ringkasan ===== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <x-card class="p-5">
            <span class="text-sm text-stone-500">Total Jenis Barang</span>
            <span class="block mt-2 text-3xl font-bold">{{ number_format($totalItems, 0, ',', '.') }}</span>
            <span class="text-xs text-stone-500">{{ $totalCategories }} kategori aktif</span>
        </x-card>

        <x-card class="p-5">
            <span class="text-sm text-stone-500">Barang Masuk</span>
            <span class="block mt-2 text-3xl font-bold">{{ number_format($stockInThisMonth, 0, ',', '.') }}</span>
            <span class="text-xs text-stone-500">unit, bulan {{ now()->translatedFormat('F') }}</span>
        </x-card>

        <x-card class="p-5">
            <span class="text-sm text-stone-500">Barang Keluar</span>
            <span class="block mt-2 text-3xl font-bold">{{ number_format($stockOutThisMonth, 0, ',', '.') }}</span>
            <span class="text-xs text-stone-500">unit, bulan {{ now()->translatedFormat('F') }}</span>
        </x-card>

        <x-card class="p-5">
            <span class="text-sm text-stone-500">Stok Menipis</span>
            <span class="block mt-2 text-3xl font-bold text-brand">{{ $lowStockItems->count() }}</span>
            <span class="text-xs text-stone-500">perlu segera diisi ulang</span>
        </x-card>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        <!-- ===== Grafik arus barang ===== -->
        <x-card class="p-6 xl:col-span-2">
            <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                <div>
                    <h2 class="font-semibold">Arus Barang</h2>
                    <span class="text-sm text-stone-500">Jumlah unit masuk dan keluar, 6 bulan terakhir</span>
                </div>
                <div class="flex gap-4 text-sm">
                    <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm bg-blue-700"></span>Masuk</span>
                    <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm bg-brand"></span>Keluar</span>
                </div>
            </div>

            {{-- Grafik batang sederhana dengan HTML + CSS. Tinggi batang = nilai / nilai tertinggi x 100% --}}
            <div class="flex items-end justify-between gap-2 h-56 border-b border-stone-200">
                @foreach ($chart as $month)
                    <div class="flex-1 h-full flex items-end justify-center gap-1">
                        <div class="w-1/3 max-w-6 bg-blue-700 rounded-t" style="height: {{ $month['in'] / $chartMax * 100 }}%" title="Masuk {{ $month['in'] }}"></div>
                        <div class="w-1/3 max-w-6 bg-brand rounded-t" style="height: {{ $month['out'] / $chartMax * 100 }}%" title="Keluar {{ $month['out'] }}"></div>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between gap-2 mt-2">
                @foreach ($chart as $month)
                    <span class="flex-1 text-center text-xs text-stone-500">{{ $month['label'] }}</span>
                @endforeach
            </div>
        </x-card>

        <!-- ===== Stok per kategori ===== -->
        <x-card class="p-6">
            <h2 class="font-semibold">Stok per Kategori</h2>
            <span class="text-sm text-stone-500">Persentase dari total unit tersimpan</span>

            <div class="mt-6 space-y-5">
                @forelse ($categoryShares as $share)
                    <div>
                        <div class="flex justify-between text-sm mb-1.5">
                            <span>{{ $share['name'] }}</span>
                            <span class="font-semibold">{{ $share['percent'] }}%</span>
                        </div>
                        {{-- Bar progres, lebarnya sesuai persentase --}}
                        <div class="h-2 rounded-full bg-stone-100">
                            <div class="h-2 rounded-full bg-brand" style="width: {{ $share['percent'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-stone-500">Belum ada kategori.</p>
                @endforelse
            </div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- ===== Transaksi terbaru ===== -->
        <x-card class="xl:col-span-2 overflow-hidden">
            <div class="flex items-center justify-between p-6">
                <h2 class="font-semibold">Transaksi Terbaru</h2>
                <a href="{{ route('reports.index') }}" class="text-sm font-medium text-brand hover:text-brand-dark">Lihat semua</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-stone-50 text-left text-stone-500">
                        <tr>
                            <th class="px-6 py-3 font-medium">Tanggal</th>
                            <th class="px-6 py-3 font-medium">No. Transaksi</th>
                            <th class="px-6 py-3 font-medium">Barang</th>
                            <th class="px-6 py-3 font-medium">Jenis</th>
                            <th class="px-6 py-3 font-medium text-right">Jumlah</th>
                            <th class="px-6 py-3 font-medium">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse ($latestTransactions as $transaction)
                            <tr>
                                <td class="px-6 py-3 whitespace-nowrap">{{ $transaction->date->translatedFormat('d M Y') }}</td>
                                <td class="px-6 py-3 whitespace-nowrap">{{ $transaction->transaction_number }}</td>
                                <td class="px-6 py-3">{{ $transaction->item->name }}</td>
                                <td class="px-6 py-3"><x-status-badge :status="$transaction->type" /></td>
                                <td class="px-6 py-3 text-right whitespace-nowrap">{{ number_format($transaction->quantity, 0, ',', '.') }} {{ $transaction->item->unit }}</td>
                                <td class="px-6 py-3 whitespace-nowrap">{{ $transaction->user->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-stone-500">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- ===== Stok perlu diisi ===== -->
        <x-card class="p-6">
            <h2 class="font-semibold mb-4">Stok Perlu Diisi</h2>

            <div class="divide-y divide-stone-100">
                {{-- Tampilkan maksimal 5 barang dengan stok paling sedikit --}}
                @forelse ($lowStockItems->take(5) as $item)
                    <div class="flex items-center justify-between gap-4 py-3">
                        <div>
                            <span class="block text-sm font-medium">{{ $item->name }}</span>
                            <span class="text-xs text-stone-500">Minimal {{ $item->min_stock }} {{ $item->unit }}</span>
                        </div>
                        <span class="text-sm font-semibold text-brand whitespace-nowrap">{{ $item->stock }} {{ $item->unit }}</span>
                    </div>
                @empty
                    <p class="py-3 text-sm text-stone-500">Semua stok masih aman.</p>
                @endforelse
            </div>

            <a href="{{ route('stock-ins.index') }}" class="mt-4 flex justify-center rounded-lg border border-brand px-4 py-2.5 text-sm font-semibold text-brand hover:bg-brand-light">
                Catat Barang Masuk
            </a>
        </x-card>
    </div>
</x-app-layout>
