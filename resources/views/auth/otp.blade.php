@extends('layouts.app')

@section('title', 'Código de verificación')

@section('content')
<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-2">Código de verificación</h1>
    <p class="text-gray-600 dark:text-gray-400 mb-6">
        Introduce el código de 6 dígitos que te enviamos a <strong>{{ $email }}</strong>. Expira en 15 minutos.
    </p>

    <form method="POST" action="{{ route('otp.verify') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <div>
            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código de 6 dígitos</label>
            <input type="text" name="code" id="code" value="{{ old('code') }}" required autofocus
                maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autocomplete="one-time-code"
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-center text-lg tracking-widest text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="000000">
        </div>
        <div>
            <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                Verificar y continuar
            </button>
        </div>
    </form>

    <form method="POST" action="{{ route('otp.resend') }}" class="mt-4">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-500">
            No recibí el código – Renovar
        </button>
    </form>

    <p class="mt-4 text-sm text-gray-500">
        <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500">Volver al inicio de sesión</a>
    </p>
</div>
@endsection
