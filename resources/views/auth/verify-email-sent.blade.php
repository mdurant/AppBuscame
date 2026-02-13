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
