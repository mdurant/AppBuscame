@extends('layouts.app')

@section('title', 'Términos y Condiciones')

@section('content')
<div class="max-w-3xl mx-auto prose prose-gray">
    <h1>Términos y Condiciones</h1>
    <p class="text-sm text-gray-500">Versión {{ $terms->version }} · Vigente desde {{ $terms->effective_at?->format('d/m/Y') ?? $terms->created_at->format('d/m/Y') }}</p>
    <div class="mt-6 text-gray-700 whitespace-pre-wrap">{{ $terms->content }}</div>
</div>
@endsection
