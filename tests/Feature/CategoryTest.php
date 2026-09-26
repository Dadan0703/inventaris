<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('daftar kategori tampil dengan jumlah barangnya', function () {
    $category = Category::factory()->create(['name' => 'Perangkat Jaringan']);
    Item::factory()->count(2)->create(['category_id' => $category->id]);

    $this->actingAs($this->user)
        ->get('/kategori')
        ->assertOk()
        ->assertSee('Perangkat Jaringan')
        ->assertSee('2 barang');
});

test('halaman tambah dan edit kategori bisa dibuka', function () {
    $category = Category::factory()->create();

    $this->actingAs($this->user)->get('/kategori/create')->assertOk()->assertSee('Simpan Kategori');
    $this->actingAs($this->user)->get("/kategori/{$category->id}/edit")->assertOk()->assertSee($category->name);
});

test('kategori baru bisa ditambahkan', function () {
    $this->actingAs($this->user)
        ->post('/kategori', ['name' => 'Komputer'])
        ->assertRedirect('/kategori');

    expect(Category::where('name', 'Komputer')->exists())->toBeTrue();
});

test('nama kategori tidak boleh kembar', function () {
    Category::factory()->create(['name' => 'Komputer']);

    $this->actingAs($this->user)
        ->post('/kategori', ['name' => 'Komputer'])
        ->assertSessionHasErrors('name');
});

test('kategori bisa diubah dengan nama yang sama seperti sebelumnya', function () {
    $category = Category::factory()->create(['name' => 'Komputer']);

    $this->actingAs($this->user)
        ->put("/kategori/{$category->id}", ['name' => 'Komputer'])
        ->assertSessionHasNoErrors();

    $this->actingAs($this->user)
        ->put("/kategori/{$category->id}", ['name' => 'Laptop & Komputer'])
        ->assertRedirect('/kategori');

    expect($category->fresh()->name)->toBe('Laptop & Komputer');
});

test('kategori kosong bisa dihapus', function () {
    $category = Category::factory()->create();

    $this->actingAs($this->user)->delete("/kategori/{$category->id}");

    expect(Category::find($category->id))->toBeNull();
});

test('kategori yang masih dipakai barang tidak bisa dihapus', function () {
    $item = Item::factory()->create();

    $this->actingAs($this->user)
        ->delete("/kategori/{$item->category_id}")
        ->assertSessionHas('error');

    expect(Category::find($item->category_id))->not->toBeNull();
});
