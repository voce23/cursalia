{{-- Interruptor encender/apagar un método. Recibe: $name, $checked --}}
<label class="inline-flex items-center gap-2 cursor-pointer select-none">
    <input type="checkbox" name="{{ $name }}" value="1" @checked($checked) class="peer sr-only">
    <span class="w-11 h-6 rounded-full bg-ink-200 peer-checked:bg-green-500 relative transition after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:w-5 after:h-5 after:bg-white after:rounded-full after:transition peer-checked:after:translate-x-5"></span>
    <span class="text-xs font-bold text-ink-600">Encender</span>
</label>
