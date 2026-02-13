@extends('layouts.auth')

@section('title', 'Verificación en dos pasos')

@section('content')
<h1 class="text-3xl font-bold text-gray-900 mb-2">Código de verificación</h1>
<p class="text-gray-500 mb-6">
    Introduce el código de 6 dígitos enviado a <strong>{{ $email }}</strong>. Expira en 15 minutos.
</p>

@if(!empty($devOtp))
<div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4">
    <p class="text-sm font-medium text-amber-800 mb-2">Entorno de desarrollo (sin correo)</p>
    <p class="text-sm text-amber-700">Tu código OTP: <strong class="text-lg tracking-widest">{{ $devOtp }}</strong></p>
</div>
@endif

<form method="POST" action="{{ route('otp.verify') }}" class="space-y-5">
    @csrf
    <input type="hidden" name="email" value="{{ $email }}">
    <div>
        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Código de 6 dígitos *</label>
        <input type="text" name="code" id="code" value="{{ old('code') }}" required autofocus maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autocomplete="one-time-code" placeholder="000000"
            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-center text-xl tracking-[0.4em] text-gray-900 shadow-sm auth-input focus:ring-2 focus:ring-offset-0">
    </div>
    <button type="submit" class="w-full rounded-lg auth-btn-primary px-4 py-3 text-sm font-medium text-white focus:ring-2 focus:ring-[#375CFF] focus:ring-offset-2 transition">
        Verificar y continuar
    </button>
</form>

<form method="POST" action="{{ route('otp.resend') }}" class="mt-4">
    @csrf
    <input type="hidden" name="email" value="{{ $email }}">
    <button type="submit" class="text-sm font-medium auth-link">No recibí el código – Renovar</button>
</form>

<p class="mt-6 text-center text-sm text-gray-600">
    <a href="{{ route('login') }}" class="font-medium auth-link">Volver a iniciar sesión</a>
</p>
@endsection
