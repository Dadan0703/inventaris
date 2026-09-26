<?php

use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    // Stok sekarang 17: awal 10, masuk 5 (September), keluar 2 (September), masuk 4 (Oktober)
    $this->item = Item::factory()->create(['code' => 'BRG-001', 'stock' => 17]);

    $transactions = [
        [StockIn::class, 'BM-0910-001', '2026-09-10', 5, 'supplier'],
        [StockOut::class, 'BK-0930-001', '2026-09-30', 2, 'recipient'],
        [StockIn::class, 'BM-1005-001', '2026-10-05', 4, 'supplier'],
    ];

    foreach ($transactions as [$model, $number, $date, $quantity, $partyColumn]) {
        $model::create([
            'transaction_number' => $number,
            'date' => $date,
            'item_id' => $this->item->id,
            'quantity' => $quantity,
            $partyColumn => 'Contoh',
            'user_id' => $this->user->id,
        ]);
    }
});

test('laporan mutasi menghitung stok awal dan stok akhir periode', function () {
    $response = $this->actingAs($this->user)
        ->get('/laporan?type=mutasi&start_date=2026-09-01&end_date=2026-09-30');

    $response->assertOk();

    // Baris: kode, nama, kategori, stok awal, masuk, keluar, stok akhir, satuan
    $row = $response->viewData('report')['rows'][0];
    expect(array_slice($row, 3, 4))->toBe([10, 5, 2, 13]);
});

test('laporan bisa diexport ke CSV', function () {
    $response = $this->actingAs($this->user)
        ->get('/laporan/export?type=masuk&start_date=2026-09-01&end_date=2026-09-30');

    $response->assertOk()->assertDownload('laporan-masuk-2026-09-01-sd-2026-09-30.csv');

    expect($response->streamedContent())
        ->toContain('BM-0910-001')
        ->not->toContain('BM-1005-001');
});

test('semua jenis laporan bisa ditampilkan', function (string $type) {
    $this->actingAs($this->user)->get('/laporan?type='.$type)->assertOk();
})->with(['mutasi', 'stok', 'masuk', 'keluar']);
