{{--
    Form barang yang dipakai bersama oleh halaman Tambah dan Edit.
    Variabel $item bisa kosong (saat tambah) atau berisi data barang (saat edit).
    old('field', nilai_bawaan) menampilkan input lama jika validasi gagal.
--}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    <!-- Kode barang -->
    <div>
        <x-input-label for="code" value="Kode Barang" />
        <x-text-input id="code" name="code" type="text" class="mt-1 w-full" placeholder="BRG-009" :value="old('code', $item->code ?? '')" required />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>

    <!-- Kategori -->
    <div>
        <x-input-label for="category_id" value="Kategori" />
        <x-select-input id="category_id" name="category_id" class="mt-1 w-full" required>
            <option value="">Pilih kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $item->category_id ?? '') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </x-select-input>
        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
    </div>

    <!-- Nama barang (lebar penuh) -->
    <div class="sm:col-span-2">
        <x-input-label for="name" value="Nama Barang" />
        <x-text-input id="name" name="name" type="text" class="mt-1 w-full" placeholder="Contoh: Switch 8 Port Gigabit" :value="old('name', $item->name ?? '')" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <!-- Satuan -->
    <div>
        <x-input-label for="unit" value="Satuan" />
        <x-select-input id="unit" name="unit" class="mt-1 w-full" required>
            @foreach ($units as $unit)
                <option value="{{ $unit }}" @selected(old('unit', $item->unit ?? '') == $unit)>{{ $unit }}</option>
            @endforeach
        </x-select-input>
        <x-input-error :messages="$errors->get('unit')" class="mt-2" />
    </div>

    <!-- Stok minimum -->
    <div>
        <x-input-label for="min_stock" value="Stok Minimum" />
        <x-text-input id="min_stock" name="min_stock" type="number" min="0" class="mt-1 w-full" placeholder="0" :value="old('min_stock', $item->min_stock ?? '')" required />
        <x-input-error :messages="$errors->get('min_stock')" class="mt-2" />
    </div>

    <!-- Keterangan (lebar penuh) -->
    <div class="sm:col-span-2">
        <x-input-label for="description" value="Keterangan" />
        <x-textarea-input id="description" name="description" class="mt-1 w-full" placeholder="Opsional">{{ old('description', $item->description ?? '') }}</x-textarea-input>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>
</div>
