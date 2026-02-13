@props(['class' => 'h-8 w-auto', 'variant' => 'default'])
{{-- Logo BuscaMe: imagen logo.png desde images. Responsive (max-width 100%, height auto). --}}
<img
    src="{{ asset('images/logo.png') }}"
    alt="{{ config('app.name', 'BuscaMe') }}"
    class="{{ $class }} max-w-full object-contain"
    loading="lazy"
    width="120"
    height="40"
/>
