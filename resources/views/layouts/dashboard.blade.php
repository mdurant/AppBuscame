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
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen antialiased">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="w-64 flex-shrink-0 border-r border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <div class="flex flex-col h-full py-4">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-6 py-3 text-lg font-semibold text-gray-800 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <iconify-icon icon="tabler:layout-dashboard" width="24" height="24" class="text-violet-600"></iconify-icon>
                    {{ config('app.name', 'BuscaMe') }}
                </a>
                <nav class="mt-6 flex-1 px-3 space-y-0.5" aria-label="Menú principal">
                    @php $rn = request()->route()?->getName(); @endphp
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $rn === 'dashboard' ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50' }}">
                        <iconify-icon icon="tabler:home" width="20" height="20"></iconify-icon>
                        Inicio
                    </a>
                    <a href="{{ route('dashboard.publications') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $rn === 'dashboard.publications' ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50' }}">
                        <iconify-icon icon="tabler:file-text" width="20" height="20"></iconify-icon>
                        Mis Publicaciones
                    </a>
                    <a href="{{ route('dashboard.history') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $rn === 'dashboard.history' ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50' }}">
                        <iconify-icon icon="tabler:history" width="20" height="20"></iconify-icon>
                        Mi Historial
                    </a>
                    <a href="{{ route('dashboard.messages') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $rn === 'dashboard.messages' ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50' }}">
                        <iconify-icon icon="tabler:message-circle" width="20" height="20"></iconify-icon>
                        Mis Mensajes
                    </a>
                    <a href="{{ route('dashboard.payments') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $rn === 'dashboard.payments' ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50' }}">
                        <iconify-icon icon="tabler:credit-card" width="20" height="20"></iconify-icon>
                        Pagos e Historial
                    </a>
                    <div class="my-2 border-t border-gray-200 dark:border-gray-700"></div>
                    <a href="{{ route('settings.profile') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ str_starts_with($rn ?? '', 'settings.') ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50' }}">
                        <iconify-icon icon="tabler:user-cog" width="20" height="20"></iconify-icon>
                        Mi Perfil
                    </a>
                </nav>
            </div>
        </aside>

        <div class="flex flex-1 flex-col min-w-0">
            {{-- Top bar: user menu + avatar --}}
            <header class="sticky top-0 z-40 flex h-16 flex-shrink-0 items-center justify-between border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-6">
                <div class="flex items-center gap-4">
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">@yield('breadcrumb', 'Panel')</h2>
                </div>
                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" @click.outside="open = false" class="flex items-center gap-3 rounded-full p-1.5 pr-3 text-left hover:bg-gray-100 dark:hover:bg-gray-700 transition focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                        @php
                            $user = auth()->user()->load('profile');
                            $avatarUrl = $user->profile?->avatar_path ? asset('storage/'.$user->profile->avatar_path) : null;
                            $initials = strtoupper(mb_substr($user->profile?->first_name ?? $user->name, 0, 1) . mb_substr($user->profile?->last_name ?? '', 0, 1) ?: mb_substr($user->email, 0, 2));
                        @endphp
                        @if($avatarUrl)
                            <img class="h-9 w-9 rounded-full object-cover" src="{{ $avatarUrl }}" alt="">
                        @else
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-violet-600 text-sm font-medium text-white">{{ $initials ?: 'U' }}</span>
                        @endif
                        <span class="hidden sm:block text-sm font-medium text-gray-700 dark:text-gray-200 truncate max-w-[140px]">{{ $user->profile?->first_name ?? $user->name }}</span>
                        <iconify-icon icon="tabler:chevron-down" width="18" height="18" class="text-gray-500 flex-shrink-0"></iconify-icon>
                    </button>
                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-56 origin-top-right rounded-xl bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black/5 dark:ring-white/10 focus:outline-none py-1" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" style="display: none;">
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $user->profile?->full_name ?? $user->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
                        </div>
                        <a href="{{ route('settings.profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50" role="menuitem">
                            <iconify-icon icon="tabler:user" width="18" height="18"></iconify-icon>
                            Mi Perfil
                        </a>
                        <a href="{{ route('settings.password') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50" role="menuitem">
                            <iconify-icon icon="tabler:lock" width="18" height="18"></iconify-icon>
                            Contraseña
                        </a>
                        <a href="{{ route('settings.sessions') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50" role="menuitem">
                            <iconify-icon icon="tabler:device-desktop" width="18" height="18"></iconify-icon>
                            Sesiones
                        </a>
                        <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}" role="menuitem">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <iconify-icon icon="tabler:logout" width="18" height="18"></iconify-icon>
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-auto p-6">
                @if (session('message'))
                    <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 text-green-800 dark:text-green-200 text-sm">
                        {{ session('message') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/20 p-4 text-red-800 dark:text-red-200 text-sm">
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/20 p-4 text-red-800 dark:text-red-200 text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
</body>
</html>
