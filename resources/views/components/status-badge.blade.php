@props(['status'])

@php
    // Pilih warna badge sesuai status stok
    $colors = [
        'Aman' => 'bg-blue-50 text-blue-800',
        'Menipis' => 'bg-amber-50 text-amber-800',
        'Habis' => 'bg-red-50 text-red-800',
        'Masuk' => 'bg-blue-50 text-blue-800',
        'Keluar' => 'bg-red-50 text-red-800',
    ];
@endphp

<span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $colors[$status] ?? 'bg-stone-100 text-stone-700' }}">
    {{ $status }}
</span>
