<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\{Category, Item, StockIn, StockOut};

class ReportController extends Controller
{
    // Jenis laporan yang tersedia: kunci => label
    public const TYPES = [
        'mutasi' => 'Mutasi Stok',
        'stok' => 'Stok Barang',
        'masuk' => 'Barang Masuk',
        'keluar' => 'Barang Keluar',
    ];

    /**
     * Menampilkan halaman laporan sesuai filter yang dipilih.
     */
    public function index(Request $request)
    {
        $filters = $this->getFilters($request);
        $report = $this->buildReport($filters);
        $categories = Category::orderBy('name')->get();
        $types = self::TYPES;

        return view('reports.index', compact('filters', 'report', 'categories', 'types'));
    }

    /**
     * Mengunduh laporan dalam format CSV yang rapih untuk Excel Indonesia
     */
    public function export(Request $request)
    {
        $filters = $this->getFilters($request);
        $report = $this->buildReport($filters);

        $fileName = 'laporan-'.$filters['type'].'-'.$filters['start_date'].'-sd-'.$filters['end_date'].'.csv';

        return response()->streamDownload(function () use ($report) {
            $file = fopen('php://output', 'w');

            // Tambah BOM biar huruf gak aneh di Excel
            fwrite($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // PAKAI TITIK KOMA ; BIAR LANGSUNG RAPIH DI EXCEL INDO
            fputcsv($file, $report['columns'], ';');

            foreach ($report['rows'] as $row) {
                fputcsv($file, $row, ';');
            }

            if ($report['total']) {
                fputcsv($file, $report['total'], ';');
            }

            fclose($file);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Mengambil filter dari URL, lalu mengisi nilai bawaan jika kosong.
     */
    private function getFilters(Request $request): array
    {
        $validated = $request->validate([
            'type' => 'nullable|in:'.implode(',', array_keys(self::TYPES)),
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        return [
            'type' => $validated['type'] ?? 'mutasi',
            'start_date' => $validated['start_date'] ?? now()->startOfMonth()->toDateString(),
            'end_date' => $validated['end_date'] ?? now()->endOfMonth()->toDateString(),
            'category_id' => $validated['category_id'] ?? null,
        ];
    }

    private function buildReport(array $filters): array
    {
        if ($filters['type'] == 'stok') {
            return $this->stockReport($filters);
        }
        if ($filters['type'] == 'masuk') {
            return $this->stockInReport($filters);
        }
        if ($filters['type'] == 'keluar') {
            return $this->stockOutReport($filters);
        }
        return $this->mutationReport($filters);
    }

    private function mutationReport(array $filters): array
    {
        $startDate = $filters['start_date'];
        $endDate = $filters['end_date'];

        $query = Item::with('category')
            ->withSum(['stockIns as in_period' => function ($query) use ($startDate, $endDate) {
                $query->whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate);
            }], 'quantity')
            ->withSum(['stockOuts as out_period' => function ($query) use ($startDate, $endDate) {
                $query->whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate);
            }], 'quantity')
            ->withSum(['stockIns as in_after' => function ($query) use ($endDate) {
                $query->whereDate('date', '>', $endDate);
            }], 'quantity')
            ->withSum(['stockOuts as out_after' => function ($query) use ($endDate) {
                $query->whereDate('date', '>', $endDate);
            }], 'quantity');

        if ($filters['category_id']) {
            $query->where('category_id', $filters['category_id']);
        }

        $items = $query->orderBy('code')->get();

        $rows = [];
        $totalStart = 0;
        $totalIn = 0;
        $totalOut = 0;
        $totalEnd = 0;

        foreach ($items as $item) {
            $in = (int) $item->in_period;
            $out = (int) $item->out_period;
            $endStock = $item->stock - (int) $item->in_after + (int) $item->out_after;
            $startStock = $endStock - $in + $out;

            $rows[] = [$item->code, $item->name, $item->category->name, $startStock, $in, $out, $endStock, $item->unit];

            $totalStart += $startStock;
            $totalIn += $in;
            $totalOut += $out;
            $totalEnd += $endStock;
        }

        return [
            'summary' => [
                'Stok Awal' => $totalStart,
                'Total Masuk' => $totalIn,
                'Total Keluar' => $totalOut,
                'Stok Akhir' => $totalEnd,
            ],
            'columns' => ['Kode', 'Nama Barang', 'Kategori', 'Stok Awal', 'Masuk', 'Keluar', 'Stok Akhir', 'Satuan'],
            'rows' => $rows,
            'total' => ['Total', '', '', $totalStart, $totalIn, $totalOut, $totalEnd, ''],
        ];
    }

    private function stockReport(array $filters): array
    {
        $query = Item::with('category');
        if ($filters['category_id']) {
            $query->where('category_id', $filters['category_id']);
        }
        $items = $query->orderBy('code')->get();
        $rows = [];
        foreach ($items as $item) {
            $rows[] = [$item->code, $item->name, $item->category->name, $item->stock, $item->unit, $item->min_stock, $item->status()];
        }
        return [
            'summary' => [
                'Jenis Barang' => $items->count(),
                'Total Unit' => $items->sum('stock'),
                'Stok Menipis' => $items->filter(function ($item) { return $item->status() == 'Menipis'; })->count(),
                'Stok Habis' => $items->where('stock', 0)->count(),
            ],
            'columns' => ['Kode', 'Nama Barang', 'Kategori', 'Stok', 'Satuan', 'Min. Stok', 'Status'],
            'rows' => $rows,
            'total' => null,
        ];
    }

    private function stockInReport(array $filters): array
    {
        $query = StockIn::with('item', 'user')
            ->whereDate('date', '>=', $filters['start_date'])
            ->whereDate('date', '<=', $filters['end_date']);
        if ($filters['category_id']) {
            $query->whereHas('item', function ($query) use ($filters) {
                $query->where('category_id', $filters['category_id']);
            });
        }
        $stockIns = $query->orderBy('date')->orderBy('id')->get();
        $rows = [];
        foreach ($stockIns as $stockIn) {
            $rows[] = [
                $stockIn->date->format('d/m/Y'),
                $stockIn->transaction_number,
                $stockIn->item->name,
                $stockIn->quantity,
                $stockIn->item->unit,
                $stockIn->supplier,
                $stockIn->user->name,
            ];
        }
        return [
            'summary' => [
                'Jumlah Transaksi' => $stockIns->count(),
                'Total Unit Masuk' => $stockIns->sum('quantity'),
            ],
            'columns' => ['Tanggal', 'No. Transaksi', 'Barang', 'Jumlah', 'Satuan', 'Supplier', 'Petugas'],
            'rows' => $rows,
            'total' => null,
        ];
    }

    private function stockOutReport(array $filters): array
    {
        $query = StockOut::with('item', 'user')
            ->whereDate('date', '>=', $filters['start_date'])
            ->whereDate('date', '<=', $filters['end_date']);
        if ($filters['category_id']) {
            $query->whereHas('item', function ($query) use ($filters) {
                $query->where('category_id', $filters['category_id']);
            });
        }
        $stockOuts = $query->orderBy('date')->orderBy('id')->get();
        $rows = [];
        foreach ($stockOuts as $stockOut) {
            $rows[] = [
                $stockOut->date->format('d/m/Y'),
                $stockOut->transaction_number,
                $stockOut->item->name,
                $stockOut->quantity,
                $stockOut->item->unit,
                $stockOut->recipient,
                $stockOut->user->name,
            ];
        }
        return [
            'summary' => [
                'Jumlah Transaksi' => $stockOuts->count(),
                'Total Unit Keluar' => $stockOuts->sum('quantity'),
            ],
            'columns' => ['Tanggal', 'No. Transaksi', 'Barang', 'Jumlah', 'Satuan', 'Tujuan / Penerima', 'Petugas'],
            'rows' => $rows,
            'total' => null,
        ];
    }
}