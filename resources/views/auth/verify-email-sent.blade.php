@extends('layouts.app')

@section('title', 'Revisa tu correo')

@section('content')
<div class="max-w-md mx-auto text-center">
    <h1 class="text-2xl font-bold mb-4">Revisa tu correo</h1>
    <p class="text-gray-600 dark:text-gray-400 mb-6">
        Te hemos enviado un enlace de verificación a <strong>{{ session('email', 'tu correo') }}</strong>. Haz clic en el enlace para verificar tu cuenta y luego introduce el código de 6 dígitos que te enviaremos.
    </p>
    <p class="text-sm text-gray-500 dark:text-gray-500 mb-6">
        Si no lo ves, revisa la carpeta de spam. El enlace expira en 60 minutos.
    </p>

    @if(!empty($verificationUrl))
    <div class="mb-6 rounded-lg border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-4 text-left">
        <p class="text-sm font-medium text-amber-800 dark:text-amber-200 mb-2">Entorno de desarrollo (sin correo)</p>
        <p class="text-sm text-amber-700 dark:text-amber-300 mb-2">Usa este enlace para verificar sin abrir el correo. También puedes revisar <code class="text-xs bg-amber-100 dark:bg-amber-900/40 px-1 rounded">storage/logs/laravel.log</code> para ver el enlace y el OTP.</p>
        <a href="{{ $verificationUrl }}" class="inline-block rounded-md bg-amber-500 px-3 py-2 text-sm font-medium text-white hover:bg-amber-600">
            Verificar correo ahora
        </a>
    </div>
    @endif

    <form method="POST" action="{{ route('email.resend') }}" class="inline">
        @csrf
        <input type="hidden" name="email" value="{{ session('email') }}">
        <button type="submit" class="text-indigo-600 hover:text-indigo-500 font-medium">
            Reenviar enlace
        </button>
    </form>
    <span class="text-gray-400 mx-2">|</span>
    <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500 font-medium">Volver al inicio de sesión</a>
</div>
@endsection
