{{-- Textarea dengan gaya yang sama seperti input teks. Nilai awal ditulis di dalam slot. --}}
<textarea {{ $attributes->merge(['rows' => 3, 'class' => 'border-stone-300 focus:border-brand focus:ring-brand rounded-lg shadow-sm text-sm']) }}>{{ $slot }}</textarea>
