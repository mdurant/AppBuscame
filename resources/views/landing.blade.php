@extends('layouts.app')

@section('title', 'Buscar propiedades - ' . config('app.name'))

@section('content')
{{-- Hero + Buscador --}}
<div class="relative bg-gradient-to-br from-slate-800 to-slate-900 text-white -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 pt-8 pb-20 lg:pb-24">
    <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-3xl lg:text-4xl font-bold mb-2">Arrienda propiedades sin comisión</h1>
        <p class="text-slate-300 mb-8">Busca en BuscaMe, Booking, Arrienda Apartamentos y más. Siempre mostramos la fuente.</p>
    </div>

    {{-- Widget de búsqueda (referencia: pestañas + Category + Location + Advanced + Search) --}}
    <div class="max-w-4xl mx-auto">
        <form method="GET" action="{{ route('home') }}" id="search-form" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 lg:p-5 space-y-4">
            <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-600 pb-3">
                <span class="px-4 py-2 rounded-t bg-amber-500 text-white font-medium text-sm">Para arriendo</span>
                <span class="px-4 py-2 text-gray-500 dark:text-gray-400 text-sm">Comprar</span>
                <span class="px-4 py-2 text-gray-500 dark:text-gray-400 text-sm">Vender</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Categoría</label>
                    <select name="category" id="category" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm">
                        <option value="">Todas</option>
                        @foreach(\App\Enums\PropertyType::cases() as $type)
                            <option value="{{ $type->value }}" @selected(($filters['category'] ?? '') === $type->value)>{{ ucfirst($type->value) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="location" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Ubicación</label>
                    <input type="text" name="location" id="location" value="{{ $filters['location'] ?? '' }}" placeholder="Ciudad o región"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="source" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Fuente</label>
                    <select name="source" id="source" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm">
                        <option value="">Todas las fuentes</option>
                        @foreach($sources as $src)
                            <option value="{{ $src->slug }}" @selected(($filters['source'] ?? '') === $src->slug)>{{ $src->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" onclick="document.getElementById('modal-advanced').showModal()" class="inline-flex items-center px-4 py-2 rounded-lg border border-amber-500 text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 text-sm font-medium hover:bg-amber-100 dark:hover:bg-amber-900/30">
                    Filtros avanzados
                </button>
                <button type="submit" class="inline-flex items-center px-5 py-2 rounded-lg bg-amber-500 text-white text-sm font-medium hover:bg-amber-600">
                    Buscar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Filtros avanzados --}}
<dialog id="modal-advanced" class="rounded-xl shadow-xl p-6 w-full max-w-2xl bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Filtros avanzados</h2>
        <button type="button" onclick="document.getElementById('modal-advanced').close()" class="text-gray-500 hover:text-gray-700">&times;</button>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Categoría, ubicación y fuente se configuran arriba. Aquí puedes afinar por precio.</p>
    <div class="space-y-4">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Precio mín. ($)</label>
                <input type="number" name="price_min" form="search-form" value="{{ $filters['price_min'] ?? '' }}" min="0" step="1000" placeholder="100" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Precio máx. ($)</label>
                <input type="number" name="price_max" form="search-form" value="{{ $filters['price_max'] ?? '' }}" min="0" step="1000" placeholder="5000000" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm">
            </div>
        </div>
    </div>
    <div class="mt-6 flex justify-end gap-2">
        <button type="button" onclick="document.getElementById('modal-advanced').close()" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm">Cerrar</button>
        <button type="submit" form="search-form" onclick="document.getElementById('modal-advanced').close()" class="px-4 py-2 rounded-lg bg-amber-500 text-white text-sm font-medium">Buscar</button>
    </div>
</dialog>

{{-- Resultados --}}
<div class="mt-8">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Resultados ({{ $properties->total() }})</h2>

    @if($properties->isEmpty())
        <p class="text-gray-500 dark:text-gray-400 py-8">No hay propiedades que coincidan. Prueba otros filtros o <a href="{{ route('home') }}" class="text-indigo-600 dark:text-indigo-400">ver todas</a>.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($properties as $property)
                <a href="{{ url('/') }}?category={{ $property->type }}" class="group block rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden hover:shadow-lg transition">
                    <div class="aspect-[4/3] bg-gray-200 dark:bg-gray-700 relative">
                        @if($property->photos->isNotEmpty())
                            <img src="{{ Storage::url($property->photos->first()->path) }}" alt="" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">Sin imagen</div>
                        @endif
                        <span class="absolute top-2 left-2 px-2 py-0.5 rounded text-xs font-medium bg-amber-500 text-white">Para arriendo</span>
                        {{-- Badge de fuente (best practice: siempre visible) --}}
                        <span class="absolute top-2 right-2 px-2 py-0.5 rounded text-xs font-medium
                            @if($property->source)
                                @if($property->source->slug === 'buscame') bg-indigo-600 text-white
                                @elseif($property->source->slug === 'booking') bg-blue-600 text-white
                                @elseif($property->source->slug === 'arrienda_apartamentos') bg-emerald-600 text-white
                                @elseif($property->source->slug === 'latam_airline') bg-red-600 text-white
                                @elseif($property->source->slug === 'social') bg-violet-600 text-white
                                @else bg-gray-600 text-white
                                @endif
                            @else bg-gray-600 text-white
                            @endif
                        ">
                            {{ $property->source?->name ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="p-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ ucfirst($property->type) }} @if($property->address) — {{ $property->address->city }}@endif</p>
                        <h3 class="font-semibold text-gray-900 dark:text-white mt-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                            {{ $property->address?->address_line ?: 'Propiedad #' . $property->id }}
                        </h3>
                        @if($property->cost_amount)
                            <p class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">
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
