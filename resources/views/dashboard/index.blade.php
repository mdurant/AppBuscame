@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div>
    <h1 class="text-2xl font-bold mb-2">Hola, {{ $user->profile?->first_name ?? $user->name }}</h1>
    <p class="text-gray-600 dark:text-gray-400 mb-8">Bienvenido al panel de control.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="#" class="block p-6 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-indigo-500 transition">
            <h3 class="font-semibold text-lg mb-1">Mis propiedades</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Publicar y gestionar avisos</p>
        </a>
        <a href="#" class="block p-6 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-indigo-500 transition">
            <h3 class="font-semibold text-lg mb-1">Mensajes</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Conversaciones con arrendatarios</p>
        </a>
        <a href="#" class="block p-6 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-indigo-500 transition">
            <h3 class="font-semibold text-lg mb-1">Pagos e historial</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Suscripción y facturas</p>
        </a>
    </div>

    <div class="mt-8 p-6 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
        <h3 class="font-semibold text-lg mb-2">Tu perfil</h3>
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
