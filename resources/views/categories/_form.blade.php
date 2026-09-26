{{--
    Form kategori yang dipakai bersama oleh halaman Tambah dan Edit.
    Variabel $category kosong saat tambah, dan berisi data saat edit.
--}}
<div>
    <x-input-label for="name" value="Nama Kategori" />
    <x-text-input id="name" name="name" type="text" class="mt-1 w-full" placeholder="Contoh: Perangkat Jaringan" :value="old('name', $category->name ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>
