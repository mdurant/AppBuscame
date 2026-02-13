<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <script src="https://code.iconify.design/iconify-icon/2.0.0/iconify-icon.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', ui-sans-serif, system-ui, sans-serif; }
        :root {
            --auth-primary: #375CFF;
            --auth-primary-hover: #2d4dd4;
            --auth-panel-bg: #15181D;
        }
        .auth-panel-right {
            background: var(--auth-panel-bg);
            background-image: linear-gradient(rgba(55, 92, 255, 0.06) 1px, transparent 1px), linear-gradient(90deg, rgba(55, 92, 255, 0.06) 1px, transparent 1px);
            background-size: 24px 24px;
        }
        .auth-btn-primary { background: var(--auth-primary); }
        .auth-btn-primary:hover { background: var(--auth-primary-hover); }
        .auth-link { color: var(--auth-primary); }
        .auth-link:hover { color: var(--auth-primary-hover); }
        .auth-input:focus { border-color: var(--auth-primary); box-shadow: 0 0 0 3px rgba(55, 92, 255, 0.2); }
    </style>
    @stack('auth-head')
</head>
<body class="min-h-screen flex flex-col bg-gray-50 antialiased">
    <div class="flex flex-1 min-h-0 flex-col lg:flex-row">
        {{-- Columna izquierda: formulario (Login, Register, Recovery, Reset, OTP, Verify) --}}
        <div class="flex flex-1 flex-col justify-center px-6 py-12 lg:w-1/2 lg:px-16">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-8 transition">
                <iconify-icon icon="tabler:arrow-left" width="18" height="18"></iconify-icon>
                Volver al inicio
            </a>
            @if (session('message'))
                <div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">{{ session('message') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">{{ session('error') }}</div>
            @endif
            @if (isset($errors) && $errors->any())
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </div>

        {{-- Columna derecha: branding (mismo diseño en todo el ciclo: Login, Register, Recovery, Reset, OTP, Verify) --}}
        <div class="auth-panel-right hidden lg:flex lg:w-1/2 flex-col justify-center items-center px-12 py-16 text-white relative overflow-hidden">
            {{-- Imagen de fondo opcional: descomenta y apunta a tu imagen en public --}}
            <img src="{{ asset('storage/images/img1.png') }}" alt="" class="absolute inset-0 w-full h-full object-cover opacity-20">
            <div class="absolute inset-0 opacity-30" style="background: radial-gradient(circle at 30% 20%, var(--auth-primary) 0%, transparent 50%);"></div>
            <div class="relative z-10 max-w-sm text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-6 shadow-lg focus:outline-none" style="background: var(--auth-primary);" aria-label="{{ config('app.name') }}">
                    @include('components.logo', ['class' => 'h-12 w-auto', 'variant' => 'light'])
                </a>
            </div>
        </div>
    </div>
    @include('components.footer')
</body>
</html>
