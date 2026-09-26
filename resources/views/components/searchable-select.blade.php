@props([
    'id',                          // id tombol, dipakai oleh <label for="...">
    'name',                        // nama field yang dikirim ke server
    'options' => [],               // daftar pilihan: [['value' => 1, 'label' => 'BRG-001 — Router'], ...]
    'selected' => null,            // nilai yang terpilih di awal
    'placeholder' => 'Pilih salah satu',
])

{{--
    Dropdown dengan kolom pencarian, dibuat dengan Alpine.js (sudah termasuk di Breeze).
    Nilai yang dipilih disimpan di <input type="hidden"> agar ikut terkirim bersama form.
--}}
<div
    x-data="{
        open: false,                  // apakah daftar pilihan sedang terbuka
        search: '',                   // kata kunci pencarian
        options: @js($options),       // data pilihan dari PHP diubah menjadi array JavaScript
        selected: @js($selected),     // nilai yang sedang dipilih

        // Pilihan yang labelnya mengandung kata kunci pencarian
        get filteredOptions() {
            return this.options.filter((option) => option.label.toLowerCase().includes(this.search.toLowerCase()));
        },

        // Label dari pilihan yang sedang dipilih
        get selectedLabel() {
            const option = this.options.find((option) => option.value == this.selected);
            return option ? option.label : '';
        },

        // Buka daftar lalu arahkan kursor ke kolom pencarian
        toggle() {
            this.open = ! this.open;
            this.$nextTick(() => this.$refs.search.focus());
        },

        // Simpan pilihan, lalu tutup dan kosongkan pencarian
        choose(option) {
            this.selected = option.value;
            this.open = false;
            this.search = '';
        },
    }"
    x-on:click.outside="open = false"
    x-on:keydown.escape="open = false"
    {{ $attributes->merge(['class' => 'relative']) }}
>
    <!-- Nilai yang dikirim ke server -->
    <input type="hidden" name="{{ $name }}" x-bind:value="selected">

    <!-- Tombol yang menampilkan pilihan saat ini -->
    <button
        type="button"
        id="{{ $id }}"
        x-on:click="toggle()"
        class="flex w-full items-center justify-between gap-2 rounded-lg border border-stone-300 bg-white px-3 py-2 text-left text-sm shadow-sm focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
    >
        <span x-text="selectedLabel || '{{ $placeholder }}'" x-bind:class="selectedLabel ? '' : 'text-stone-500'" class="truncate"></span>
        <svg class="h-4 w-4 shrink-0 text-stone-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" /></svg>
    </button>

    <!-- Daftar pilihan (muncul saat tombol diklik) -->
    <div x-show="open" x-cloak class="absolute z-20 mt-1 w-full rounded-lg border border-stone-200 bg-white shadow-lg">
        <!-- Kolom pencarian. Enter tidak mengirim form. -->
        <div class="border-b border-stone-100 p-2">
            <input
                type="text"
                x-ref="search"
                x-model="search"
                x-on:keydown.enter.prevent
                placeholder="Cari kode atau nama..."
                aria-label="Cari pilihan"
                class="w-full rounded-md border-stone-300 text-sm focus:border-brand focus:ring-brand"
            >
        </div>

        <ul class="max-h-60 overflow-y-auto py-1">
            {{-- template x-for: mengulang <li> untuk setiap pilihan hasil pencarian --}}
            <template x-for="option in filteredOptions" x-bind:key="option.value">
                <li>
                    <button
                        type="button"
                        x-on:click="choose(option)"
                        x-text="option.label"
                        x-bind:class="option.value == selected ? 'bg-brand-light font-semibold text-brand-dark' : 'hover:bg-stone-100'"
                        class="w-full px-3 py-2 text-left text-sm"
                    ></button>
                </li>
            </template>

            <!-- Pesan jika tidak ada hasil -->
            <li x-show="filteredOptions.length === 0" class="px-3 py-2 text-sm text-stone-500">Data tidak ditemukan</li>
        </ul>
    </div>
</div>
