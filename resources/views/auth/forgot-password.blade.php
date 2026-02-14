@extends('layouts.auth')

@section('title', 'Recuperar contraseña')

@section('content')
<h1 class="text-3xl font-bold text-gray-900 mb-2">¿Olvidaste tu contraseña?</h1>
<p class="text-gray-500 mb-8">Introduce tu correo y te enviaremos un enlace para restablecerla.</p>

<form method="POST" action="{{ route('password.email') }}" class="space-y-5">
    @csrf
    <x-input-group-email name="email" label="Correo" placeholder="info@gmail.com" required autofocus />
    <button type="submit" class="w-full rounded-lg auth-btn-primary px-4 py-3 text-sm font-medium text-white focus:ring-2 focus:ring-[#375CFF] focus:ring-offset-2 transition">
        Enviar enlace
    </button>
</form>

<p class="mt-6 text-center text-sm text-gray-600">
    <a href="{{ route('login') }}" class="font-medium auth-link">Volver a iniciar sesión</a>
</p>
@endsection
