@extends('layouts.auth')

@section('title', 'Crear cuenta')

@section('content')
<h1 class="text-3xl font-bold text-gray-900 mb-2">Crear cuenta</h1>
<p class="text-gray-500 mb-8">Introduce tus datos para registrarte.</p>

<div class="flex flex-wrap gap-3 mb-6">
    <a href="#" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
        <iconify-icon icon="simple-icons:google" width="18" height="18"></iconify-icon>
        Registrarse con Google
    </a>
    <a href="#" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
        <iconify-icon icon="simple-icons:microsoft" width="18" height="18"></iconify-icon>
        Registrarse con Microsoft
    </a>
</div>

<div class="relative my-6">
    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
    <div class="relative flex justify-center text-sm"><span class="bg-white px-4 text-gray-500">O</span></div>
</div>

<form method="POST" action="{{ route('register') }}" class="space-y-5" id="register-form">
    @csrf
    @if($termsVersion)
    <input type="hidden" name="terms_version_id" value="{{ $termsVersion->id }}">
    @endif
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required autofocus placeholder="Tu nombre"
                class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 shadow-sm auth-input focus:ring-2 focus:ring-offset-0">
        </div>
        <div>
            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required placeholder="Tus apellidos"
                class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 shadow-sm auth-input focus:ring-2 focus:ring-offset-0">
        </div>
    </div>
    <x-input-group-email name="email" label="Correo" placeholder="info@gmail.com" required />
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña *</label>
        <input type="password" name="password" id="password" required autocomplete="new-password" placeholder="Mín. 8 caracteres, mayúscula, número y símbolo"
            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 shadow-sm auth-input focus:ring-2 focus:ring-offset-0">
    </div>
    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña *</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" placeholder="Repite la contraseña"
            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 shadow-sm auth-input focus:ring-2 focus:ring-offset-0">
    </div>

    @if($termsVersion)
    <label class="flex items-start gap-3 cursor-pointer">
        <input type="checkbox" name="terms_accepted" value="1" {{ old('terms_accepted') ? 'checked' : '' }} required
            class="mt-1 rounded border-gray-300 text-[#375CFF] focus:ring-[#375CFF]">
        <span class="text-sm text-gray-600">
            Acepto los
            <a href="#" data-open-terms-modal="terms" class="font-medium auth-link hover:underline">Términos y Condiciones</a>
            y la
            <a href="#" data-open-terms-modal="privacy" class="font-medium auth-link hover:underline">Política de Privacidad</a>
            (versión {{ $termsVersion->version }}).
        </span>
    </label>
    @else
    <p class="text-sm text-amber-700">No hay términos publicados. Contacta al administrador.</p>
    @endif

    <button type="submit" class="w-full rounded-lg auth-btn-primary px-4 py-3 text-sm font-medium text-white focus:ring-2 focus:ring-[#375CFF] focus:ring-offset-2 transition" @if(!$termsVersion) disabled @endif>
        Crear cuenta
    </button>
</form>

<p class="mt-6 text-center text-sm text-gray-600">
    ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="font-medium auth-link">Iniciar sesión</a>
</p>

@if($termsVersion)
@include('components.terms-privacy-modal', ['termsVersion' => $termsVersion])
@endif
@endsection
