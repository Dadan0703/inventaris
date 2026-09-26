{{-- Kotak putih pembungkus konten --}}
<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-stone-200']) }}>
    {{ $slot }}
</div>
