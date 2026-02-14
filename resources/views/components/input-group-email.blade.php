@props([
    'name' => 'email',
    'id' => null,
    'value' => '',
    'label' => 'Correo',
    'placeholder' => 'info@gmail.com',
    'required' => false,
    'autocomplete' => 'email',
    'autofocus' => false,
    'disabled' => false,
    'inputClass' => '',
])
@php
    $id = $id ?? $name;
    $inputClass = trim('block w-full rounded-lg border border-gray-300 py-2.5 pl-11 pr-4 text-gray-900 placeholder:text-gray-400 shadow-sm focus:border-[#375CFF] focus:ring-2 focus:ring-[#375CFF]/20 ' . ($errors->first($name) ? 'border-red-500' : '') . ' ' . ($disabled ? 'bg-gray-50 cursor-not-allowed' : 'bg-white') . ' ' . $inputClass);
@endphp
<div {{ $attributes->only('class')->merge(['class' => '']) }}>
    <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 mb-1.5">
        {{ $label }}
        @if($required)<span class="text-red-500">*</span>@endif
    </label>
    <div class="relative">
        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
            <iconify-icon icon="tabler:mail" width="20" height="20" aria-hidden="true"></iconify-icon>
        </span>
        <input
            type="email"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            autocomplete="{{ $autocomplete }}"
            {{ $required ? 'required' : '' }}
            {{ $autofocus ? 'autofocus' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            class="{{ $inputClass }}"
            {{ $attributes->except('class', 'label') }}
        >
    </div>
    @error($name)
        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
