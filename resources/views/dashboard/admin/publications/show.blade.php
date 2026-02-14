@extends('layouts.dashboard')

@section('title', 'Detalle de publicación')
@section('breadcrumb', 'Admin · Publicación #'.$property->id)

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center gap-4">
        <a href="{{ route('admin.publications.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-[#375CFF]">
            <iconify-icon icon="tabler:arrow-left" width="18" height="18"></iconify-icon>
            Volver al listado
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Publicación #{{ $property->id }} — {{ ucfirst($property->type ?? 'Propiedad') }}</h2>
                    <p class="text-sm text-gray-500">Creada el {{ $property->created_at->format('d/m/Y H:i') }}, publicada el {{ $property->published_at ? $property->published_at->format('d/m/Y H:i') : '—' }}</p>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div><dt class="text-gray-500">Tipo</dt><dd class="font-medium text-gray-900">{{ ucfirst($property->type ?? '—') }}</dd></div>
                        <div><dt class="text-gray-500">Estado</dt><dd class="font-medium text-gray-900">{{ $property->status?->value ?? $property->status }}</dd></div>
                        <div><dt class="text-gray-500">Precio</dt><dd class="font-medium text-gray-900">{{ $property->cost_amount ? number_format($property->cost_amount) . ' ' . ($property->cost_currency ?? 'CLP') : '—' }}</dd></div>
                        <div><dt class="text-gray-500">Consultas en el portal</dt><dd class="font-medium text-gray-900">{{ number_format($property->property_views_count ?? 0) }}</dd></div>
                    </dl>
                    @if($property->address)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <dt class="text-gray-500 text-sm">Ubicación</dt>
                            <dd class="mt-1 text-gray-900">{{ $property->address->address_line }}, {{ $property->address->city }}, {{ $property->address->region }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Fotos</h3>
                </div>
                <div class="p-6">
                    @if($property->photos->isNotEmpty())
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @foreach($property->photos as $photo)
                                <img src="{{ asset('storage/' . $photo->path) }}" alt="" class="rounded-lg border border-gray-200 w-full aspect-video object-cover" onerror="this.src='{{ asset('images/cabanas-en-chile.jpg') }}';">
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Sin fotos cargadas.</p>
                        <img src="{{ asset('images/cabanas-en-chile.jpg') }}" alt="" class="mt-2 rounded-lg w-full max-w-sm aspect-video object-cover">
                    @endif
                </div>
            </div>

            @if($threads->isNotEmpty())
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Interacciones con potenciales clientes</h3>
                    <p class="text-sm text-gray-500">{{ $threads->count() }} conversación(es)</p>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($threads as $thread)
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-900">Hilo #{{ $thread->id }}</span>
                            <span class="text-sm text-gray-500">{{ $thread->messages_count }} mensaje(s)</span>
                        </div>
                        @foreach($thread->messages->take(3) as $msg)
                            <div class="text-sm text-gray-600 pl-4 border-l-2 border-gray-200 my-2">
                                <span class="font-medium text-gray-800">{{ $msg->user->profile?->full_name ?? $msg->user->name }}</span>
                                {{ $msg->created_at->format('d/m H:i') }}: {{ Str::limit($msg->body, 120) }}
                            </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div>
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden sticky top-24">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Publicante</h3>
                </div>
                <div class="p-6">
                    @php $owner = $property->user; @endphp
                    <div class="flex items-center gap-3 mb-4">
                        @php
                            $initials = strtoupper(mb_substr($owner->profile?->first_name ?? $owner->name, 0, 1) . mb_substr($owner->profile?->last_name ?? '', 0, 1) ?: mb_substr($owner->email, 0, 2));
                        @endphp
                        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-[#375CFF]/10 text-sm font-semibold text-[#375CFF]">{{ $initials ?: 'U' }}</span>
                        <div>
                            <div class="font-medium text-gray-900">{{ $owner->profile?->full_name ?? $owner->name }}</div>
                            <div class="text-sm text-gray-500">{{ $owner->email }}</div>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.index') }}?search={{ urlencode($owner->email) }}" class="inline-flex items-center gap-2 text-sm font-medium text-[#375CFF] hover:underline">
                        Ver en listado de usuarios
                        <iconify-icon icon="tabler:arrow-right" width="16" height="16"></iconify-icon>
                    </a>
                </div>
            </div>

            @if($property->propertyViews->isNotEmpty())
            <div class="mt-6 rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Últimas consultas</h3>
                </div>
                <ul class="divide-y divide-gray-200 max-h-64 overflow-y-auto">
                    @foreach($property->propertyViews->take(20) as $pv)
                    <li class="px-6 py-2 text-sm text-gray-600">
                        {{ $pv->viewed_at->format('d/m/Y H:i') }}
                        @if($pv->user_id)
                            — {{ $pv->user->profile?->full_name ?? $pv->user->email }}
                        @else
                            — Visitante
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
