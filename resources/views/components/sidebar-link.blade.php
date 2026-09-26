@props(['active' => false])

@php
    // Link aktif diberi latar merah muda, link lain abu-abu
    $classes = $active
        ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold bg-brand-light text-brand-dark'
        : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} @if ($active) aria-current="page" @endif>
    {{ $slot }}
</a>
