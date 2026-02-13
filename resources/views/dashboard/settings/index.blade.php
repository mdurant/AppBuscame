@extends('layouts.dashboard')

@section('title', 'Configuración')
@section('breadcrumb', 'Configuración')

@section('content')
<div>
    <h1 class="text-2xl font-bold mb-2">Configuración</h1>
    <p class="text-gray-600 dark:text-gray-400 mb-8">Administra tu perfil y la configuración de tu cuenta.</p>

    <nav class="flex flex-wrap gap-1 border-b border-gray-200 mb-8" aria-label="Tabs">
        <a href="{{ route('settings.profile') }}" class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium rounded-t-lg border-b-2 transition {{ ($activeTab ?? '') === 'profile' ? 'border-[#375CFF] text-[#375CFF]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            <iconify-icon icon="tabler:user" width="20" height="20"></iconify-icon>
            Ficha Personal
        </a>
        <a href="{{ route('settings.password') }}" class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium rounded-t-lg border-b-2 transition {{ ($activeTab ?? '') === 'password' ? 'border-[#375CFF] text-[#375CFF]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            <iconify-icon icon="tabler:lock" width="20" height="20"></iconify-icon>
            Contraseña
        </a>
        <a href="{{ route('settings.2fa') }}" class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium rounded-t-lg border-b-2 transition {{ ($activeTab ?? '') === '2fa' ? 'border-[#375CFF] text-[#375CFF]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            <iconify-icon icon="tabler:shield" width="20" height="20"></iconify-icon>
            Two-Factor (2FA)
        </a>
        <a href="{{ route('settings.sessions') }}" class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium rounded-t-lg border-b-2 transition {{ ($activeTab ?? '') === 'sessions' ? 'border-[#375CFF] text-[#375CFF]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            <iconify-icon icon="tabler:device-desktop" width="20" height="20"></iconify-icon>
            Sesiones Activas
        </a>
    </nav>

    @if($activeTab === 'profile')
        @include('dashboard.settings.partials.profile')
    @elseif($activeTab === 'password')
        @include('dashboard.settings.partials.password')
    @elseif($activeTab === '2fa')
        @include('dashboard.settings.partials.2fa')
    @elseif($activeTab === 'sessions')
        @include('dashboard.settings.partials.sessions')
    @endif
</div>

@include('dashboard.settings.partials.confirm-password-modal')
@endsection
