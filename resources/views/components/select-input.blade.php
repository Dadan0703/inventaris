{{-- Dropdown (select) dengan gaya yang sama seperti input teks. Isi <option> ditulis di dalam slot. --}}
<select {{ $attributes->merge(['class' => 'border-stone-300 focus:border-brand focus:ring-brand rounded-lg shadow-sm text-sm']) }}>
    {{ $slot }}
</select>
