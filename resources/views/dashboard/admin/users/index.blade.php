@extends('layouts.dashboard')

@section('title', 'Usuarios de la plataforma')
@section('breadcrumb', 'Admin · Usuarios')

@section('content')
<div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Usuarios de la plataforma</h1>
            <p class="text-sm text-gray-500 mt-0.5">Total: <strong>{{ number_format($total) }}</strong> usuarios</p>
        </div>
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-2 flex-1 sm:max-w-xs">
            <input type="hidden" name="sort" value="{{ request('sort', 'created_at') }}">
            <input type="hidden" name="dir" value="{{ request('dir', 'desc') }}">
            <div class="relative flex-1">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <iconify-icon icon="tabler:search" width="18" height="18"></iconify-icon>
                </span>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o email..."
                    class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-9 pr-4 text-sm text-gray-900 placeholder:text-gray-400 focus:border-[#375CFF] focus:ring-2 focus:ring-[#375CFF]/20">
            </div>
            <button type="submit" class="rounded-lg bg-[#375CFF] px-4 py-2 text-sm font-medium text-white hover:bg-[#2d4dd4]">Buscar</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'region', 'dir' => request('dir') === 'asc' && request('sort') === 'region' ? 'desc' : 'asc', 'page' => null]) }}" class="group inline-flex items-center gap-1 hover:text-gray-700">
                            Región
                            @if(request('sort') === 'region')
                                <iconify-icon icon="tabler:chevron-{{ request('dir') === 'asc' ? 'up' : 'down' }}" width="14" height="14"></iconify-icon>
                            @else
                                <iconify-icon icon="tabler:selector" width="14" height="14" class="opacity-50 group-hover:opacity-100"></iconify-icon>
                            @endif
                        </a>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'city', 'dir' => request('dir') === 'asc' && request('sort') === 'city' ? 'desc' : 'asc', 'page' => null]) }}" class="group inline-flex items-center gap-1 hover:text-gray-700">
                            Ciudad
                            @if(request('sort') === 'city')
                                <iconify-icon icon="tabler:chevron-{{ request('dir') === 'asc' ? 'up' : 'down' }}" width="14" height="14"></iconify-icon>
                            @else
                                <iconify-icon icon="tabler:selector" width="14" height="14" class="opacity-50 group-hover:opacity-100"></iconify-icon>
                            @endif
                        </a>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'dir' => request('dir') === 'asc' && request('sort') === 'created_at' ? 'desc' : 'asc', 'page' => null]) }}" class="group inline-flex items-center gap-1 hover:text-gray-700">
                            Fecha de creación
                            @if(request('sort') === 'created_at' || !request('sort'))
                                <iconify-icon icon="tabler:chevron-{{ (request('dir') ?: 'desc') === 'asc' ? 'up' : 'down' }}" width="14" height="14"></iconify-icon>
                            @else
                                <iconify-icon icon="tabler:selector" width="14" height="14" class="opacity-50 group-hover:opacity-100"></iconify-icon>
                            @endif
                        </a>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'total_consultations', 'dir' => request('dir') === 'asc' && request('sort') === 'total_consultations' ? 'desc' : 'asc', 'page' => null]) }}" class="group inline-flex items-center gap-1 hover:text-gray-700">
                            Consultas
                            @if(request('sort') === 'total_consultations')
                                <iconify-icon icon="tabler:chevron-{{ request('dir') === 'asc' ? 'up' : 'down' }}" width="14" height="14"></iconify-icon>
                            @else
                                <iconify-icon icon="tabler:selector" width="14" height="14" class="opacity-50 group-hover:opacity-100"></iconify-icon>
                            @endif
                        </a>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ranking</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Más detalles</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $u)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            @php
                                $initials = strtoupper(mb_substr($u->profile?->first_name ?? $u->name, 0, 1) . mb_substr($u->profile?->last_name ?? '', 0, 1) ?: mb_substr($u->email, 0, 2));
                            @endphp
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#375CFF]/10 text-sm font-semibold text-[#375CFF]">{{ $initials ?: 'U' }}</span>
                            <div>
                                <div class="font-medium text-gray-900">{{ $u->profile?->full_name ?? $u->name }}</div>
                                <div class="text-sm text-gray-500">{{ $u->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $u->profile?->region ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $u->profile?->city ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $u->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ number_format($u->total_consultations ?? 0) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">#{{ $u->ranking ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <button type="button" data-user-id="{{ $u->id }}" data-user-detail class="inline-flex items-center justify-center rounded-lg p-2 text-gray-500 hover:bg-[#375CFF]/10 hover:text-[#375CFF] transition" title="Ver detalles">
                            <iconify-icon icon="tabler:eye" width="20" height="20"></iconify-icon>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">No hay usuarios que coincidan con la búsqueda.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-sm text-gray-600">
            Mostrando {{ $users->firstItem() }}–{{ $users->lastItem() }} de {{ $users->total() }}
        </p>
        <nav class="flex gap-1">
            @if ($users->onFirstPage())
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-400 text-sm">Anterior</span>
            @else
                <a href="{{ $users->previousPageUrl() }}" class="inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">Anterior</a>
            @endif
            @foreach ($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                <a href="{{ $url }}" class="inline-flex items-center px-3 py-1.5 rounded-lg border text-sm {{ $page === $users->currentPage() ? 'border-[#375CFF] bg-[#375CFF] text-white' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">{{ $page }}</a>
            @endforeach
            @if ($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">Siguiente</a>
            @else
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-400 text-sm">Siguiente</span>
            @endif
        </nav>
    </div>
    @endif
</div>

{{-- Modal detalles usuario --}}
<div id="user-detail-modal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="fixed inset-0 bg-black/50" data-close-modal></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="relative w-full max-w-lg rounded-xl bg-white shadow-xl max-h-[90vh] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Detalles del usuario</h3>
                <button type="button" data-close-modal class="rounded-lg p-2 text-gray-500 hover:bg-gray-100">
                    <iconify-icon icon="tabler:x" width="22" height="22"></iconify-icon>
                </button>
            </div>
            <div id="user-detail-content" class="flex-1 overflow-y-auto px-6 py-4">
                <p class="text-gray-500 text-sm">Cargando…</p>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const modal = document.getElementById('user-detail-modal');
    const content = document.getElementById('user-detail-content');
    if (!modal || !content) return;

    function openModal() { modal.classList.remove('hidden'); }
    function closeModal() { modal.classList.add('hidden'); }

    modal.querySelectorAll('[data-close-modal]').forEach(function(el) {
        el.addEventListener('click', closeModal);
    });
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });

    document.querySelectorAll('[data-user-detail]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            if (!userId) return;
            content.innerHTML = '<p class="text-gray-500 text-sm">Cargando…</p>';
            openModal();
            fetch('{{ url("dashboard/admin/users") }}/' + userId, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    const p = data.profile || {};
                    content.innerHTML = [
                        '<dl class="space-y-3 text-sm">',
                        '<div><dt class="text-gray-500 font-medium">Nombre</dt><dd class="mt-0.5 font-medium text-gray-900">' + (data.name || '—') + '</dd></div>',
                        '<div><dt class="text-gray-500 font-medium">Email</dt><dd class="mt-0.5">' + (data.email || '—') + '</dd></div>',
                        '<div><dt class="text-gray-500 font-medium">Rol</dt><dd class="mt-0.5">' + (data.role || '—') + '</dd></div>',
                        '<div><dt class="text-gray-500 font-medium">Región</dt><dd class="mt-0.5">' + (p.region || '—') + '</dd></div>',
                        '<div><dt class="text-gray-500 font-medium">Ciudad</dt><dd class="mt-0.5">' + (p.city || '—') + '</dd></div>',
                        '<div><dt class="text-gray-500 font-medium">Teléfono</dt><dd class="mt-0.5">' + (p.phone || '—') + '</dd></div>',
                        '<div><dt class="text-gray-500 font-medium">Fecha de alta</dt><dd class="mt-0.5">' + (data.created_at || '—') + '</dd></div>',
                        '<div><dt class="text-gray-500 font-medium">Publicaciones</dt><dd class="mt-0.5">' + (data.published_properties_count || 0) + '</dd></div>',
                        '<div><dt class="text-gray-500 font-medium">Consultas en el portal</dt><dd class="mt-0.5">' + (data.total_consultations || 0) + '</dd></div>',
                        '</dl>',
                        (data.properties && data.properties.length ? '<div class="mt-4 pt-4 border-t border-gray-200"><h4 class="font-medium text-gray-900 mb-2">Propiedades</h4><ul class="space-y-1 text-sm text-gray-600">' + data.properties.map(function(prop) {
                            return '<li>#' + prop.id + ' ' + (prop.type || '') + ' – ' + (prop.address || '') + ' – ' + (prop.views_count || 0) + ' consultas</li>';
                        }).join('') + '</ul></div>' : '')
                    ].join('');
                })
                .catch(function() {
                    content.innerHTML = '<p class="text-red-600 text-sm">Error al cargar los detalles.</p>';
                });
        });
    });
})();
</script>
@endsection
