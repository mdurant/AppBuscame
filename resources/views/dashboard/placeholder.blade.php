@extends('layouts.dashboard')

@section('title', $title ?? 'Panel')
@section('breadcrumb', $title ?? 'Panel')

@section('content')
<div>
    <h1 class="text-2xl font-bold mb-2">{{ $title ?? 'Sección' }}</h1>
    <p class="text-gray-600 dark:text-gray-400 mb-8">{{ $description ?? 'Contenido en desarrollo.' }}</p>
    <div class="rounded-lg border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-800/50 p-12 text-center text-gray-500 dark:text-gray-400">
        <iconify-icon icon="tabler:tool" width="48" height="48" class="mx-auto mb-4 opacity-50"></iconify-icon>
        <p>Próximamente</p>
    </div>
</div>
@endsection
