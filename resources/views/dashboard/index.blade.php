@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('breadcrumb', 'Inicio')

@section('content')
<div>
    <h1 class="text-2xl font-bold mb-2">Hola, {{ $user->profile?->first_name ?? $user->name }}</h1>
    <p class="text-gray-600 dark:text-gray-400 mb-8">Bienvenido al panel de control.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('dashboard.publications') }}" class="flex items-center gap-4 block p-6 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-violet-500 transition">
            <span class="rounded-lg bg-violet-100 dark:bg-violet-900/30 p-3">
                <iconify-icon icon="tabler:file-text" width="28" height="28" class="text-violet-600 dark:text-violet-400"></iconify-icon>
            </span>
            <div class="text-left">
                <h3 class="font-semibold text-lg mb-1">Mis Publicaciones</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Publicar y gestionar avisos</p>
            </div>
        </a>
        <a href="{{ route('dashboard.messages') }}" class="flex items-center gap-4 block p-6 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-violet-500 transition">
            <span class="rounded-lg bg-violet-100 dark:bg-violet-900/30 p-3">
                <iconify-icon icon="tabler:message-circle" width="28" height="28" class="text-violet-600 dark:text-violet-400"></iconify-icon>
            </span>
            <div class="text-left">
                <h3 class="font-semibold text-lg mb-1">Mis Mensajes</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Conversaciones con arrendatarios</p>
            </div>
        </a>
        <a href="{{ route('dashboard.payments') }}" class="flex items-center gap-4 block p-6 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-violet-500 transition">
            <span class="rounded-lg bg-violet-100 dark:bg-violet-900/30 p-3">
                <iconify-icon icon="tabler:credit-card" width="28" height="28" class="text-violet-600 dark:text-violet-400"></iconify-icon>
            </span>
            <div class="text-left">
                <h3 class="font-semibold text-lg mb-1">Pagos e historial</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Suscripción y facturas</p>
            </div>
        </a>
    </div>

    <div class="mt-8 p-6 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-lg">Tu perfil</h3>
            <a href="{{ route('settings.profile') }}" class="inline-flex items-center gap-2 text-sm font-medium text-violet-600 dark:text-violet-400 hover:underline">
                Configuración
                <iconify-icon icon="tabler:settings" width="18" height="18"></iconify-icon>
            </a>
        </div>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
            <dt class="text-gray-500 dark:text-gray-400">Nombre</dt>
            <dd>{{ $user->profile?->full_name ?? $user->name }}</dd>
            <dt class="text-gray-500 dark:text-gray-400">Correo</dt>
            <dd>{{ $user->email }}</dd>
            <dt class="text-gray-500 dark:text-gray-400">Estado de verificación</dt>
            <dd>Verificado</dd>
        </dl>
    </div>
</div>
@endsection
