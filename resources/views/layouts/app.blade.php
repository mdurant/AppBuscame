<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <script src="https://code.iconify.design/iconify-icon/2.0.0/iconify-icon.min.js"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <style>
        body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen antialiased">
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-semibold text-gray-800 dark:text-white">
                        {{ config('app.name', 'BuscaMe') }}
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
                        <a href="{{ route('register') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Registrarse</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('message'))
            <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/20 p-4 text-green-800 dark:text-green-200">
                {{ session('message') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-md bg-red-50 dark:bg-red-900/20 p-4 text-red-800 dark:text-red-200">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 dark:bg-red-900/20 p-4 text-red-800 dark:text-red-200">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
