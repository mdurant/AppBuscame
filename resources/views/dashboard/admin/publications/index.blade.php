@extends('layouts.dashboard')

@section('title', 'Publicaciones ofertadas')
@section('breadcrumb', 'Admin · Publicaciones')

@section('content')
<div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h1 class="text-xl font-semibold text-gray-900">Todas las publicaciones ofertadas</h1>
        <p class="text-sm text-gray-500 mt-0.5">Publicaciones de los usuarios de la plataforma.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Propiedad</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Publicante</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Publicado</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consultas</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($properties as $p)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            @if($p->photos->isNotEmpty())
                                <img src="{{ asset('storage/' . $p->photos->first()->path) }}" alt="" class="h-12 w-12 rounded-lg object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            @endif
                            <span class="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center {{ $p->photos->isNotEmpty() ? 'hidden' : '' }}">
                                <iconify-icon icon="tabler:photo" width="24" height="24" class="text-gray-400"></iconify-icon>
                            </span>
                            <div>
                                <div class="font-medium text-gray-900">{{ ucfirst($p->type ?? 'Propiedad') }} #{{ $p->id }}</div>
                                <div class="text-sm text-gray-500">{{ $p->cost_amount ? number_format($p->cost_amount) . ' ' . $p->cost_currency : '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $p->user->profile?->full_name ?? $p->user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $p->user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $p->address ? $p->address->city . ', ' . $p->address->region : '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $p->published_at ? $p->published_at->format('d/m/Y') : '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ number_format($p->property_views_count ?? 0) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <a href="{{ route('admin.publications.show', $p) }}" class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-medium text-[#375CFF] hover:bg-[#375CFF]/10">
                            <iconify-icon icon="tabler:eye" width="18" height="18"></iconify-icon>
                            Ver detalle
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">No hay publicaciones.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($properties->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-sm text-gray-600">
            Mostrando {{ $properties->firstItem() }}–{{ $properties->lastItem() }} de {{ $properties->total() }}
        </p>
        <nav class="flex gap-1">
            @if ($properties->onFirstPage())
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-400 text-sm">Anterior</span>
            @else
                <a href="{{ $properties->previousPageUrl() }}" class="inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">Anterior</a>
            @endif
            @foreach ($properties->getUrlRange(max(1, $properties->currentPage() - 2), min($properties->lastPage(), $properties->currentPage() + 2)) as $page => $url)
                <a href="{{ $url }}" class="inline-flex items-center px-3 py-1.5 rounded-lg border text-sm {{ $page === $properties->currentPage() ? 'border-[#375CFF] bg-[#375CFF] text-white' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">{{ $page }}</a>
            @endforeach
            @if ($properties->hasMorePages())
                <a href="{{ $properties->nextPageUrl() }}" class="inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">Siguiente</a>
            @else
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-400 text-sm">Siguiente</span>
            @endif
        </nav>
    </div>
    @endif
</div>
@endsection
