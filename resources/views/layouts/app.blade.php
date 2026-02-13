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
            --color-primary: #375CFF;
            --color-surface: #f3f4f6;
            --color-surface-elevated: #ffffff;
            --color-text: #2c2c2c;
            --color-text-muted: #5f6a87;
            --color-border: #e5e5e5;
            --color-success: #08a36b;
            --color-success-bg: #e6f6f0;
            --color-error: #ff5e5e;
            --color-error-bg: #ffefef;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased overflow-x-hidden" style="background: var(--color-surface); color: var(--color-text);">
    <nav class="border-b flex-shrink-0" style="background: var(--color-surface-elevated); border-color: var(--color-border);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center focus:outline-none" aria-label="{{ config('app.name') }}">
                        @include('components.logo', ['class' => 'h-9 w-auto sm:h-10'])
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Dashboard</a>
                        <a href="{{ route('settings.profile') }}" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Mi Perfil</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Cerrar sesión</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Iniciar sesión</a>
                        <a href="{{ route('register') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition" style="background: var(--color-primary);">Registrarse</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto flex-1 w-full px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    @include('components.footer')

    @php
        $toasts = [];
        if (session('toast')) $toasts = array_merge($toasts, is_array(session('toast')) ? session('toast') : [session('toast')]);
        if (session('message')) $toasts[] = ['type' => 'success', 'message' => session('message')];
        if (session('error')) $toasts[] = ['type' => 'error', 'message' => session('error')];
        if (isset($errors) && $errors->any()) foreach ($errors->all() as $e) $toasts[] = ['type' => 'error', 'message' => $e];
    @endphp
    @include('components.toaster', ['toasts' => $toasts])
</body>
</html>
