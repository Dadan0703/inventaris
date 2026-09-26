<?php

// Pesan error validasi dalam Bahasa Indonesia.
// :attribute akan diganti dengan nama kolom dari bagian 'attributes' di bawah.
return [
    'after_or_equal' => ':attribute harus tanggal setelah atau sama dengan :date.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Password salah.',
    'date' => ':attribute bukan tanggal yang valid.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'in' => ':attribute yang dipilih tidak valid.',
    'integer' => ':attribute harus berupa angka bulat.',
    'lowercase' => ':attribute harus huruf kecil.',
    'max' => [
        'numeric' => ':attribute tidak boleh lebih dari :max.',
        'string' => ':attribute tidak boleh lebih dari :max karakter.',
    ],
    'min' => [
        'numeric' => ':attribute minimal :min.',
        'string' => ':attribute minimal :min karakter.',
    ],
    'required' => ':attribute wajib diisi.',
    'string' => ':attribute harus berupa teks.',
    'unique' => ':attribute sudah digunakan.',
    'password' => [
        'letters' => ':attribute harus mengandung minimal satu huruf.',
        'mixed' => ':attribute harus mengandung huruf besar dan huruf kecil.',
        'numbers' => ':attribute harus mengandung minimal satu angka.',
        'symbols' => ':attribute harus mengandung minimal satu simbol.',
        'uncompromised' => ':attribute ini pernah bocor di internet. Silakan pilih password lain.',
    ],

    // Nama kolom yang tampil di pesan error
    'attributes' => [
        'code' => 'Kode barang',
        'name' => 'Nama',
        'category_id' => 'Kategori',
        'unit' => 'Satuan',
        'min_stock' => 'Stok minimum',
        'description' => 'Keterangan',
        'date' => 'Tanggal',
        'item_id' => 'Barang',
        'quantity' => 'Jumlah',
        'supplier' => 'Supplier',
        'recipient' => 'Tujuan / penerima',
        'type' => 'Jenis laporan',
        'start_date' => 'Dari tanggal',
        'end_date' => 'Sampai tanggal',
        'email' => 'Email',
        'password' => 'Password',
        'current_password' => 'Password saat ini',
    ],
];
