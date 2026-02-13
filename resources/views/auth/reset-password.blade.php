@extends('layouts.auth')

@section('title', 'Nueva contraseña')

@section('content')
<h1 class="text-3xl font-bold text-gray-900 mb-2">Nueva contraseña</h1>
<p class="text-gray-500 mb-8">Introduce tu correo y la nueva contraseña.</p>

<form method="POST" action="{{ route('password.update') }}" class="space-y-5">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo *</label>
        <input type="email" name="email" id="email" value="{{ old('email', $email) }}" required autofocus autocomplete="email"
            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 shadow-sm auth-input focus:ring-2 focus:ring-offset-0">
    </div>
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña *</label>
        <input type="password" name="password" id="password" required autocomplete="new-password" placeholder="Mín. 8 caracteres, mayúscula, número y símbolo"
            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 shadow-sm auth-input focus:ring-2 focus:ring-offset-0">
    </div>
    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña *</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 shadow-sm auth-input focus:ring-2 focus:ring-offset-0">
    </div>
    <button type="submit" class="w-full rounded-lg auth-btn-primary px-4 py-3 text-sm font-medium text-white focus:ring-2 focus:ring-[#375CFF] focus:ring-offset-2 transition">
        Restablecer contraseña
    </button>
</form>

<p class="mt-6 text-center text-sm text-gray-600">
    <a href="{{ route('login') }}" class="font-medium auth-link">Volver a iniciar sesión</a>
</p>
@endsection
