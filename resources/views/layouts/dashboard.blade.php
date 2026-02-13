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
        [x-cloak] { display: none !important; }
        .dashboard-sidebar { background: #ffffff; border-color: #e5e7eb; }
        .dashboard-sidebar .nav-link { color: #5f6a87; }
        .dashboard-sidebar .nav-link iconify-icon { color: #5f6a87; }
        .dashboard-sidebar .nav-link:hover { color: #375CFF; background: rgba(55, 92, 255, 0.08); }
        .dashboard-sidebar .nav-link:hover iconify-icon { color: #375CFF; }
        .dashboard-sidebar .nav-link.active { background: rgba(55, 92, 255, 0.12); color: #375CFF; }
        .dashboard-sidebar .nav-link.active iconify-icon { color: #375CFF; }
    </style>
</head>
<body class="min-h-screen antialiased bg-gray-100 text-gray-900">
    <div class="flex min-h-screen">
        {{-- Sidebar (estilo TailAdmin: blanco, acentos azules #375CFF) --}}
        <aside class="dashboard-sidebar w-64 flex-shrink-0 border-r shadow-sm">
            <div class="flex flex-col h-full py-4">
                <a href="{{ route('dashboard') }}" class="flex items-center justify-center px-6 py-3 text-gray-900 hover:text-[#375CFF] transition focus:outline-none" aria-label="{{ config('app.name') }}">
                    @include('components.logo', ['class' => 'h-9 w-auto'])
                </a>
                <nav class="mt-6 flex-1 px-3 space-y-0.5" aria-label="Menú principal">
                    @php $rn = request()->route()?->getName(); @endphp
                    <a href="{{ route('dashboard') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $rn === 'dashboard' ? 'active' : '' }}">
                        <iconify-icon icon="tabler:home" width="20" height="20"></iconify-icon>
                        Inicio
                    </a>
                    <a href="{{ route('dashboard.publications') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $rn === 'dashboard.publications' ? 'active' : '' }}">
                        <iconify-icon icon="tabler:file-text" width="20" height="20"></iconify-icon>
                        Mis Publicaciones
                    </a>
                    <a href="{{ route('dashboard.history') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $rn === 'dashboard.history' ? 'active' : '' }}">
                        <iconify-icon icon="tabler:history" width="20" height="20"></iconify-icon>
                        Mi Historial
                    </a>
                    <a href="{{ route('dashboard.messages') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $rn === 'dashboard.messages' ? 'active' : '' }}">
                        <iconify-icon icon="tabler:message-circle" width="20" height="20"></iconify-icon>
                        Mis Mensajes
                    </a>
                    <a href="{{ route('dashboard.payments') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $rn === 'dashboard.payments' ? 'active' : '' }}">
                        <iconify-icon icon="tabler:credit-card" width="20" height="20"></iconify-icon>
                        Pagos e Historial
                    </a>
                    <div class="my-2 border-t border-gray-200"></div>
                    <a href="{{ route('settings.profile') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ str_starts_with($rn ?? '', 'settings.') ? 'active' : '' }}">
                        <iconify-icon icon="tabler:user-cog" width="20" height="20"></iconify-icon>
                        Mi Perfil
                    </a>
                </nav>
            </div>
        </aside>

        <div class="flex flex-1 flex-col min-w-0">
            {{-- Top bar (blanco, estilo TailAdmin) --}}
            <header class="sticky top-0 z-40 flex h-16 flex-shrink-0 items-center justify-between border-b border-gray-200 bg-white px-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <h2 class="text-sm font-medium text-gray-500 truncate">@yield('breadcrumb', 'Panel')</h2>
                </div>
                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" @click.outside="open = false" class="flex items-center gap-3 rounded-full p-1.5 pr-3 text-left text-gray-900 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 hover:bg-gray-50" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                        @php
                            $user = auth()->user()->load('profile');
                            $avatarUrl = $user->profile?->avatar_path ? asset('storage/'.$user->profile->avatar_path) : null;
                            $initials = strtoupper(mb_substr($user->profile?->first_name ?? $user->name, 0, 1) . mb_substr($user->profile?->last_name ?? '', 0, 1) ?: mb_substr($user->email, 0, 2));
                        @endphp
                        @if($avatarUrl)
                            <img class="h-9 w-9 rounded-full object-cover" src="{{ $avatarUrl }}" alt="">
                        @else
                            <span class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-medium text-white bg-[#375CFF]">{{ $initials ?: 'U' }}</span>
                        @endif
                        <span class="hidden sm:block text-sm font-medium text-gray-900 truncate max-w-[140px]">{{ $user->profile?->first_name ?? $user->name }}</span>
                        <iconify-icon icon="tabler:chevron-down" width="18" height="18" class="flex-shrink-0 text-gray-500"></iconify-icon>
                    </button>
                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-56 origin-top-right rounded-xl border border-gray-200 bg-white py-1 shadow-lg focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button">
                        <div class="border-b border-gray-100 px-4 py-3">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $user->profile?->full_name ?? $user->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                        </div>
                        <a href="{{ route('settings.profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#375CFF] transition" role="menuitem">
                            <iconify-icon icon="tabler:user" width="18" height="18"></iconify-icon>
                            Mi Perfil
                        </a>
                        <a href="{{ route('settings.password') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#375CFF] transition" role="menuitem">
                            <iconify-icon icon="tabler:lock" width="18" height="18"></iconify-icon>
                            Contraseña
                        </a>
                        <a href="{{ route('settings.sessions') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#375CFF] transition" role="menuitem">
                            <iconify-icon icon="tabler:device-desktop" width="18" height="18"></iconify-icon>
                            Sesiones
                        </a>
                        <div class="my-1 border-t border-gray-100"></div>
                        <form method="POST" action="{{ route('logout') }}" role="menuitem">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                <iconify-icon icon="tabler:logout" width="18" height="18"></iconify-icon>
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-auto bg-gray-100 p-6">
                @yield('content')
            </main>
            @include('components.footer')
        </div>
    </div>

    @php
        $toasts = [];
        if (session('toast')) $toasts = array_merge($toasts, is_array(session('toast')) ? session('toast') : [session('toast')]);
        if (session('message')) $toasts[] = ['type' => 'success', 'message' => session('message')];
        if (session('error')) $toasts[] = ['type' => 'error', 'message' => session('error')];
        if (isset($errors) && $errors->any()) foreach ($errors->all() as $e) $toasts[] = ['type' => 'error', 'message' => $e];
    @endphp
    @include('components.toaster', ['toasts' => $toasts])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
</body>
</html>
