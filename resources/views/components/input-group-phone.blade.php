@props([
    'name' => 'phone',
    'id' => null,
    'value' => '',
    'label' => 'Teléfono',
    'placeholder' => '+56 9 1234 5678',
    'required' => false,
    'inputClass' => '',
])
@php
    $id = $id ?? $name;
    $raw = old($name, $value);
    // Mostrar en formato +56 9 1234 5678
    if ($raw && preg_match('/^\+569\d{8}$/', preg_replace('/\s/', '', $raw))) {
        $displayValue = preg_replace('/^\+569(\d{4})(\d{4})$/', '+56 9 $1 $2', preg_replace('/\s/', '', $raw));
    } else {
        $displayValue = $raw ?: '';
    }
    $inputClass = trim('block w-full rounded-r-lg border border-gray-300 border-l-0 bg-white py-2.5 px-4 text-gray-900 placeholder:text-gray-400 shadow-sm focus:border-[#375CFF] focus:ring-2 focus:ring-[#375CFF]/20 focus:border-l-gray-300 ' . ($errors->first($name) ? 'border-red-500' : '') . ' ' . $inputClass);
@endphp
<div {{ $attributes->only('class')->merge(['class' => '']) }}>
    <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 mb-1.5">
        {{ $label }}
        @if($required)<span class="text-red-500">*</span>@endif
    </label>
    <div class="flex rounded-lg overflow-hidden border border-gray-300 bg-white shadow-sm focus-within:ring-2 focus-within:ring-[#375CFF]/20 focus-within:border-[#375CFF] @error($name) border-red-500 @enderror">
        <span class="inline-flex items-center gap-1.5 rounded-l-lg border-r border-gray-300 bg-gray-50 px-3.5 py-2.5 text-sm font-medium text-gray-700">
            <span class="text-base">🇨🇱</span>
            <span>+56</span>
        </span>
        <input
            type="tel"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ $displayValue }}"
            placeholder="{{ $placeholder }}"
            inputmode="numeric"
                    data-phone-input
            {{ $required ? 'required' : '' }}
            class="{{ $inputClass }}"
            maxlength="16"
            {{ $attributes->except('class', 'label') }}
        >
    </div>
    <p class="mt-1 text-xs text-gray-500">Solo celular Chile: +56 9 y 8 dígitos (ej. 9123 4567)</p>
    @error($name)
        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
@once
<script>
(function() {
    function formatChilePhone(input) {
        var v = input.value.replace(/\D/g, '');
        if (v.startsWith('56')) v = v.slice(2);
        if (v.startsWith('9')) v = v.slice(0, 9);
        else if (v.length > 0) v = '9' + v.slice(0, 8);
        v = v.slice(0, 9);
        if (v.length <= 1) return v;
        if (v.length <= 5) return v.slice(0,1) + ' ' + v.slice(1);
        return v.slice(0,1) + ' ' + v.slice(1,5) + ' ' + v.slice(5);
    }
    function toE164(input) {
        var v = input.value.replace(/\D/g, '');
        if (v.startsWith('56')) v = v.slice(2);
        if (v.startsWith('9')) v = v.slice(0, 9);
        else v = '9' + v.slice(0, 8);
        return '+56' + v.slice(0, 9);
    }
    document.addEventListener('input', function(e) {
        if (!e.target.matches('[data-phone-input]')) return;
        var start = e.target.selectionStart, oldLen = e.target.value.length;
        e.target.value = formatChilePhone(e.target);
        var newLen = e.target.value.length;
        e.target.setSelectionRange(start + (newLen - oldLen), start + (newLen - oldLen));
    });
    document.addEventListener('blur', function(e) {
        if (!e.target.matches('[data-phone-input]')) return;
        var normalized = toE164(e.target);
        if (normalized.length >= 12) {
            e.target.value = normalized.replace(/^\+56(\d)(\d{4})(\d{4})$/, '+56 $1 $2 $3');
        }
    });
    document.addEventListener('submit', function(e) {
        var form = e.target;
        if (!form || form.tagName !== 'FORM') return;
        form.querySelectorAll('[data-phone-input]').forEach(function(input) {
            if (input.value.replace(/\s/g, '').length >= 11) {
                input.value = toE164(input);
            }
        });
    });
})();
</script>
@endonce
