@extends('layouts.auth')

@section('title', 'Iniciar sesión')

@section('content')
<h1 class="text-3xl font-bold text-gray-900 mb-2">Iniciar sesión</h1>
<p class="text-gray-500 mb-8">Introduce tu correo y contraseña para acceder.</p>

@if(!empty($testCredentials))
<div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4">
    <p class="text-sm font-medium text-amber-800 mb-2">Usuarios de prueba</p>
    <p class="text-xs text-amber-700 mb-2">Contraseña: <code class="bg-amber-100 px-1 rounded">{{ $testCredentials['password'] }}</code></p>
    <ul class="text-sm text-amber-800 space-y-1">
        @foreach($testCredentials['users'] as $u)
        <li><strong>{{ $u['name'] }}</strong> — <code class="text-xs">{{ $u['email'] }}</code></li>
        @endforeach
    </ul>
</div>
@endif

<div class="flex flex-wrap gap-3 mb-6">
    <a href="#" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
        <iconify-icon icon="simple-icons:google" width="18" height="18"></iconify-icon>
        Google
    </a>
    <a href="#" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
        <iconify-icon icon="simple-icons:microsoft" width="18" height="18"></iconify-icon>
        Microsoft
    </a>
</div>

<div class="relative my-6">
    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
    <div class="relative flex justify-center text-sm"><span class="bg-white px-4 text-gray-500">O</span></div>
</div>

<form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo *</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="tu@correo.com"
            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 shadow-sm auth-input focus:ring-2 focus:ring-offset-0">
    </div>
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña *</label>
        <input type="password" name="password" id="password" required autocomplete="current-password" placeholder="••••••••"
            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 shadow-sm auth-input focus:ring-2 focus:ring-offset-0">
    </div>
    <div class="flex items-center justify-between">
        <label class="inline-flex items-center">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-[#375CFF] focus:ring-[#375CFF]">
            <span class="ml-2 text-sm text-gray-600">Recordarme</span>
        </label>
        <a href="{{ route('password.request') }}" class="text-sm font-medium auth-link">¿Olvidaste tu contraseña?</a>
    </div>
    <button type="submit" class="w-full rounded-lg auth-btn-primary px-4 py-3 text-sm font-medium text-white focus:ring-2 focus:ring-[#375CFF] focus:ring-offset-2 transition">
        Iniciar sesión
    </button>
</form>

<p class="mt-6 text-center text-sm text-gray-600">
    ¿No tienes cuenta? <a href="{{ route('register') }}" class="font-medium auth-link">Regístrate</a>
</p>
@endsection
