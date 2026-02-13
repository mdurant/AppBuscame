@extends('layouts.app')

@section('title', 'Buscar propiedades - ' . config('app.name'))

@section('content')
{{-- Hero full-bleed: ocupa todo el ancho del viewport (incl. zonas laterales) — breakout del contenedor main --}}
<div class="hero-full-bleed">
<div class="relative min-h-[40vh] flex flex-col justify-start overflow-hidden">
    {{-- Capa de fondos: 3 imágenes en bucle (crossfade) + Ken Burns (zoom suave tipo documental) — cubren 100% del espacio --}}
    <div class="absolute inset-0 hero-kenburns-wrap">
        <div class="hero-slide hero-slide-1" style="background-image: url('{{ asset('images/home1.png') }}');"></div>
        <div class="hero-slide hero-slide-2" style="background-image: url('{{ asset('images/home2.png') }}');"></div>
        <div class="hero-slide hero-slide-3" style="background-image: url('{{ asset('images/truful-truful-web.webp') }}');"></div>
    </div>
    {{-- Overlay para legibilidad del contenido --}}
    <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-black/70" aria-hidden="true"></div>

    {{-- Contenido alineado lo más superior posible: título + buscador (minimalista, mejor uso del espacio) --}}
    <div class="relative z-10 px-4 sm:px-6 lg:px-8 pt-6 pb-10 lg:pt-8 lg:pb-12">
        <div class="max-w-3xl mx-auto text-center mb-5">
            <h1 class="text-2xl lg:text-4xl font-semibold text-white mb-1.5 drop-shadow-lg">Arrienda propiedades sin comisión</h1>
            <p class="text-sm lg:text-base text-white/90">Busca en múltiples fuentes. Descubre paisajes y destinos.</p>
        </div>

        <div class="max-w-5xl mx-auto">
            <form method="GET" action="{{ route('home') }}" id="search-form" class="rounded-xl border border-white/20 bg-white/95 shadow-xl backdrop-blur-sm p-5 lg:p-6 space-y-4">
                <div class="flex flex-wrap gap-2 border-b border-gray-100 pb-4">
                    <span class="px-3 py-1.5 rounded-lg bg-[#375CFF]/10 text-[#375CFF] text-xs font-medium">Para arriendo</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="category" class="block text-xs font-medium text-gray-500 mb-1">Categoría</label>
                        <select name="category" id="category" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2.5 text-sm focus:border-[#375CFF] focus:ring-1 focus:ring-[#375CFF]">
                            <option value="">Todas</option>
                            @foreach(\App\Enums\PropertyType::cases() as $type)
                                <option value="{{ $type->value }}" @selected(($filters['category'] ?? '') === $type->value)>{{ ucfirst($type->value) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="location" class="block text-xs font-medium text-gray-500 mb-1">Ubicación</label>
                        <input type="text" name="location" id="location" value="{{ $filters['location'] ?? '' }}" placeholder="Ciudad o región"
                            class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2.5 text-sm focus:border-[#375CFF] focus:ring-1 focus:ring-[#375CFF]">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="source" class="block text-xs font-medium text-gray-500 mb-1">Fuente</label>
                        <select name="source" id="source" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2.5 text-sm focus:border-[#375CFF] focus:ring-1 focus:ring-[#375CFF]">
                            <option value="">Todas las fuentes</option>
                            @foreach($sources as $src)
                                <option value="{{ $src->slug }}" @selected(($filters['source'] ?? '') === $src->slug)>{{ $src->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 pt-1">
                    <button type="button" onclick="document.getElementById('modal-advanced').showModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 focus:ring-2 focus:ring-[#375CFF] focus:ring-offset-1">
                        <iconify-icon icon="tabler:filter" width="18" height="18"></iconify-icon>
                        Filtros avanzados
                    </button>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg bg-[#375CFF] text-white text-sm font-medium hover:bg-[#2d4dd4] transition focus:ring-2 focus:ring-[#375CFF] focus:ring-offset-2 focus:ring-offset-white">
                        <iconify-icon icon="tabler:search" width="18" height="18"></iconify-icon>
                        Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<style>
/* Hero a ancho completo: escapa del max-w del main y usa todo el viewport (zonas que antes quedaban en blanco) */
.hero-full-bleed {
    width: 100vw;
    margin-left: calc(50% - 50vw);
    margin-right: calc(50% - 50vw);
}
/* Contenedor: zoom suave tipo cortometraje (Ken Burns) en bucle */
.hero-kenburns-wrap {
    animation: hero-kenburns 20s ease-in-out infinite;
}
@keyframes hero-kenburns {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.06); }
}
/* Cada slide: ocupa todo, cover; solo crossfade */
.hero-slide {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}
.hero-slide-1 { animation: hero-fade-1 18s ease-in-out infinite; }
.hero-slide-2 { animation: hero-fade-2 18s ease-in-out infinite; }
.hero-slide-3 { animation: hero-fade-3 18s ease-in-out infinite; }
/* Crossfade: 18s ciclo, ~5s por imagen + transición */
@keyframes hero-fade-1 {
    0%, 28% { opacity: 1; }
    33%, 94% { opacity: 0; }
    100% { opacity: 1; }
}
@keyframes hero-fade-2 {
    0%, 27% { opacity: 0; }
    33%, 61% { opacity: 1; }
    66%, 100% { opacity: 0; }
}
@keyframes hero-fade-3 {
    0%, 60% { opacity: 0; }
    66%, 94% { opacity: 1; }
    100% { opacity: 0; }
}
</style>

{{-- Modal Filtros avanzados --}}
<dialog id="modal-advanced" class="rounded-xl shadow-xl border border-gray-200 p-6 w-full max-w-2xl bg-white text-gray-900 backdrop:bg-black/20">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Filtros avanzados</h2>
        <button type="button" onclick="document.getElementById('modal-advanced').close()" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100">
            <iconify-icon icon="tabler:x" width="20" height="20"></iconify-icon>
        </button>
    </div>
    <p class="text-sm text-gray-500 mb-4">Ajusta por rango de precio.</p>
    <div class="space-y-4">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Precio mín. ($)</label>
                <input type="number" name="price_min" form="search-form" value="{{ $filters['price_min'] ?? '' }}" min="0" step="1000" placeholder="100" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-[#375CFF] focus:ring-1 focus:ring-[#375CFF]">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Precio máx. ($)</label>
                <input type="number" name="price_max" form="search-form" value="{{ $filters['price_max'] ?? '' }}" min="0" step="1000" placeholder="5000000" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-[#375CFF] focus:ring-1 focus:ring-[#375CFF]">
            </div>
        </div>
    </div>
    <div class="mt-6 flex justify-end gap-2">
        <button type="button" onclick="document.getElementById('modal-advanced').close()" class="px-4 py-2.5 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">Cerrar</button>
        <button type="submit" form="search-form" onclick="document.getElementById('modal-advanced').close()" class="px-4 py-2.5 rounded-lg bg-[#375CFF] text-white text-sm font-medium hover:bg-[#2d4dd4]">Buscar</button>
    </div>
</dialog>

{{-- Resultados --}}
<div class="mt-8">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Resultados ({{ $properties->total() }})</h2>

    @if($properties->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white p-8 text-center shadow-sm">
            <p class="text-gray-500">No hay propiedades que coincidan.</p>
            <a href="{{ route('home') }}" class="mt-3 inline-block text-sm font-medium text-[#375CFF] hover:text-[#2d4dd4]">Ver todas</a>
        </div>
    @else
        @php
            $refImages = [asset('images/cabanas-en-chile.jpg'), asset('images/casa-las-condes.webp')];
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($properties as $property)
                <a href="{{ url('/') }}?category={{ $property->type }}" class="group block rounded-xl border border-gray-200 bg-white overflow-hidden shadow-sm hover:shadow-md hover:border-[#375CFF]/20 transition">
                    <div class="aspect-[4/3] bg-gray-100 relative">
                        @if($property->photos->isNotEmpty())
                            <img src="{{ Storage::url($property->photos->first()->path) }}" alt="" class="w-full h-full object-cover">
                        @else
                            {{-- Imágenes de referencia en forma random (alternan según índice + id para variedad) --}}
                            <img src="{{ $refImages[($property->id + $loop->index) % 2] }}" alt="" class="w-full h-full object-cover">
                        @endif
                        <span class="absolute top-2 left-2 px-2 py-1 rounded-lg text-xs font-medium bg-[#375CFF]/90 text-white">Arriendo</span>
                        <span class="absolute top-2 right-2 px-2 py-1 rounded-lg text-xs font-medium
                            @if($property->source)
                                @if($property->source->slug === 'buscame') bg-gray-800 text-white
                                @elseif($property->source->slug === 'booking') bg-gray-700 text-white
                                @elseif($property->source->slug === 'arrienda_apartamentos') bg-gray-600 text-white
                                @else bg-gray-600 text-white
                                @endif
                            @else bg-gray-600 text-white
                            @endif
                        ">
                            {{ $property->source?->name ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">{{ ucfirst($property->type) }} @if($property->address) — {{ $property->address->city }}@endif</p>
                        <h3 class="font-semibold text-gray-900 mt-1 group-hover:text-[#375CFF] transition">
                            {{ $property->address?->address_line ?: 'Propiedad #' . $property->id }}
                        </h3>
                        @if($property->cost_amount)
                            <p class="mt-2 text-sm font-medium text-gray-700">
                                {{ number_format($property->cost_amount, 0, ',', '.') }} {{ $property->cost_currency }}
                            </p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $properties->links() }}
        </div>
    @endif
</div>
@endsection
