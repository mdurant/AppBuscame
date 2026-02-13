@extends('layouts.auth')

@section('title', 'Revisa tu correo')

@section('content')
<h1 class="text-3xl font-bold text-gray-900 mb-2">Revisa tu correo</h1>
<p class="text-gray-500 mb-6">
    Hemos enviado un enlace de verificación a <strong>{{ session('email', 'tu correo') }}</strong>. Haz clic en el enlace y luego introduce el código de 6 dígitos que te enviaremos.
</p>
<p class="text-sm text-gray-500 mb-6">Si no lo ves, revisa la carpeta de spam. El enlace expira en 60 minutos.</p>

@if(!empty($verificationUrl))
<div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4">
    <p class="text-sm font-medium text-amber-800 mb-2">Entorno de desarrollo (sin correo)</p>
    <p class="text-sm text-amber-700 mb-2">Usa este enlace para verificar sin abrir el correo.</p>
    <a href="{{ $verificationUrl }}" class="inline-block rounded-lg bg-amber-500 px-4 py-2 text-sm font-medium text-white hover:bg-amber-600">Verificar correo ahora</a>
</div>
@endif

<div class="flex flex-wrap items-center gap-2">
    <form method="POST" action="{{ route('email.resend') }}" class="inline">
        @csrf
        <input type="hidden" name="email" value="{{ session('email') }}">
        <button type="submit" class="text-sm font-medium auth-link">Reenviar enlace</button>
    </form>
    <span class="text-gray-400">|</span>
    <a href="{{ route('login') }}" class="text-sm font-medium auth-link">Volver a iniciar sesión</a>
</div>
@endsection
