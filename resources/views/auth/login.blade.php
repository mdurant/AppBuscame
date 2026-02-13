@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-6">Iniciar sesión</h1>

    @if(!empty($testCredentials))
    <div class="mb-6 rounded-lg border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-4">
        <p class="text-sm font-medium text-amber-800 dark:text-amber-200 mb-2">Usuarios de prueba (testing técnico)</p>
        <p class="text-xs text-amber-700 dark:text-amber-300 mb-3">Contraseña para todos: <code class="bg-amber-100 dark:bg-amber-900/50 px-1 rounded">{{ $testCredentials['password'] }}</code></p>
        <ul class="text-sm text-amber-800 dark:text-amber-200 space-y-1">
            @foreach($testCredentials['users'] as $u)
            <li>
                <strong>{{ $u['name'] }}</strong> — <code class="text-xs">{{ $u['email'] }}</code>
                @if(!empty($u['description']))
                    <span class="block text-xs text-amber-600 dark:text-amber-400">{{ $u['description'] }}</span>
                @endif
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo electrónico</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña</label>
            <input type="password" name="password" id="password" required autocomplete="current-password"
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="remember" id="remember" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <label for="remember" class="ml-2 text-sm text-gray-600 dark:text-gray-400">Recordarme</label>
        </div>
        <div>
            <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                Entrar
            </button>
        </div>
    </form>

    <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
        ¿No tienes cuenta? <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Regístrate</a>
    </p>
</div>
@endsection
