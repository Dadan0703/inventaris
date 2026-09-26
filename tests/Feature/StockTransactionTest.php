<?php

use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->item = Item::factory()->create(['stock' => 10]);
});

test('barang masuk menambah stok dan membuat nomor transaksi otomatis', function () {
    $this->actingAs($this->user)
        ->post('/barang-masuk', [
            'date' => '2026-09-26',
            'item_id' => $this->item->id,
            'quantity' => 5,
            'supplier' => 'PT Sumber Jaringan',
        ])
        ->assertRedirect('/barang-masuk');

    expect($this->item->fresh()->stock)->toBe(15)
        ->and(StockIn::first()->transaction_number)->toBe('BM-0926-001')
        ->and(StockIn::first()->user_id)->toBe($this->user->id);
});

test('nomor transaksi berurutan pada tanggal yang sama', function () {
    foreach ([1, 2] as $quantity) {
        $this->actingAs($this->user)->post('/barang-masuk', [
            'date' => '2026-09-26',
            'item_id' => $this->item->id,
            'quantity' => $quantity,
            'supplier' => 'PT Contoh',
        ]);
    }

    expect(StockIn::pluck('transaction_number')->all())->toBe(['BM-0926-001', 'BM-0926-002']);
});

test('barang keluar mengurangi stok', function () {
    $this->actingAs($this->user)
        ->post('/barang-keluar', [
            'date' => '2026-09-26',
            'item_id' => $this->item->id,
            'quantity' => 4,
            'recipient' => 'Lab Jaringan 1',
        ])
        ->assertRedirect('/barang-keluar');

    expect($this->item->fresh()->stock)->toBe(6)
        ->and(StockOut::first()->transaction_number)->toBe('BK-0926-001');
});

test('barang keluar tidak boleh melebihi stok', function () {
    $this->actingAs($this->user)
        ->post('/barang-keluar', [
            'date' => '2026-09-26',
            'item_id' => $this->item->id,
            'quantity' => 11,
            'recipient' => 'Lab Jaringan 1',
        ])
        ->assertSessionHasErrors('quantity');

    expect($this->item->fresh()->stock)->toBe(10)
        ->and(StockOut::count())->toBe(0);
});

test('halaman barang masuk dan keluar bisa dibuka', function () {
    $this->actingAs($this->user)->get('/barang-masuk')->assertOk()->assertSee('Catat Barang Masuk');
    $this->actingAs($this->user)->get('/barang-keluar')->assertOk()->assertSee('Catat Barang Keluar');
});

test('dropdown barang di halaman barang masuk berisi data barang', function () {
    $this->actingAs($this->user)
        ->get('/barang-masuk')
        ->assertOk()
        ->assertViewHas('itemOptions', function (array $options) {
            return $options[0]['value'] === $this->item->id
                && str_contains($options[0]['label'], $this->item->name);
        });
});

test('dropdown barang di halaman barang keluar berisi data barang', function () {
    $this->actingAs($this->user)
        ->get('/barang-keluar')
        ->assertOk()
        ->assertViewHas('itemOptions', function (array $options) {
            return $options[0]['value'] === $this->item->id
                && str_contains($options[0]['label'], $this->item->name);
        });
});
