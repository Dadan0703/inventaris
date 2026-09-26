<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->category = Category::factory()->create();
});

test('daftar barang bisa dicari berdasarkan nama', function () {
    Item::factory()->create(['name' => 'Router MikroTik']);
    Item::factory()->create(['name' => 'Kabel UTP']);

    $this->actingAs($this->user)
        ->get('/barang?search=Router')
        ->assertOk()
        ->assertSee('Router MikroTik')
        ->assertDontSee('Kabel UTP');
});

test('barang baru bisa ditambahkan dengan stok awal 0', function () {
    $this->actingAs($this->user)
        ->post('/barang', [
            'code' => 'BRG-100',
            'name' => 'Switch 8 Port',
            'category_id' => $this->category->id,
            'unit' => 'unit',
            'min_stock' => 3,
        ])
        ->assertRedirect('/barang');

    $item = Item::where('code', 'BRG-100')->first();
    expect($item->name)->toBe('Switch 8 Port')
        ->and($item->stock)->toBe(0);
});

test('kode barang tidak boleh kembar', function () {
    Item::factory()->create(['code' => 'BRG-100']);

    $this->actingAs($this->user)
        ->post('/barang', [
            'code' => 'BRG-100',
            'name' => 'Barang Lain',
            'category_id' => $this->category->id,
            'unit' => 'pcs',
            'min_stock' => 0,
        ])
        ->assertSessionHasErrors('code');
});

test('barang bisa diubah', function () {
    $item = Item::factory()->create();

    $this->actingAs($this->user)
        ->put("/barang/{$item->id}", [
            'code' => $item->code,
            'name' => 'Nama Baru',
            'category_id' => $this->category->id,
            'unit' => 'box',
            'min_stock' => 10,
        ])
        ->assertRedirect('/barang');

    expect($item->fresh()->name)->toBe('Nama Baru');
});

test('barang tanpa transaksi bisa dihapus', function () {
    $item = Item::factory()->create();

    $this->actingAs($this->user)->delete("/barang/{$item->id}");

    expect(Item::find($item->id))->toBeNull();
});

test('barang yang sudah punya transaksi tidak bisa dihapus', function () {
    $item = Item::factory()->create();
    StockIn::create([
        'transaction_number' => 'BM-0101-001',
        'date' => '2026-01-01',
        'item_id' => $item->id,
        'quantity' => 5,
        'supplier' => 'PT Contoh',
        'user_id' => $this->user->id,
    ]);

    $this->actingAs($this->user)
        ->delete("/barang/{$item->id}")
        ->assertSessionHas('error');

    expect(Item::find($item->id))->not->toBeNull();
});

test('status stok dihitung dari stok dan batas minimum', function () {
    expect(Item::factory()->make(['stock' => 0, 'min_stock' => 5])->status())->toBe('Habis')
        ->and(Item::factory()->make(['stock' => 5, 'min_stock' => 5])->status())->toBe('Menipis')
        ->and(Item::factory()->make(['stock' => 6, 'min_stock' => 5])->status())->toBe('Aman');
});

test('halaman tambah dan edit barang bisa dibuka', function () {
    $item = Item::factory()->create();

    $this->actingAs($this->user)->get('/barang/create')->assertOk()->assertSee('Simpan Barang');
    $this->actingAs($this->user)->get("/barang/{$item->id}/edit")->assertOk()->assertSee($item->name);
});
