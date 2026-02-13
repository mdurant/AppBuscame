@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('breadcrumb', 'Inicio')

@section('content')
<div class="space-y-8">
    {{-- Bienvenida --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Hola, {{ $user->profile?->first_name ?? $user->name }}</h1>
        <p class="text-sm text-gray-500">Bienvenido al Dashboard. Ajusta tu perfil, fortalece tu seguridad y comienza a operar en la plataforma.</p>
    </div>

    {{-- Acciones rápidas (cajas blancas sobreadas) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <a href="{{ route('settings.profile') }}" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm flex items-center gap-4 transition hover:shadow-md hover:border-[#375CFF]/30">
            <span class="rounded-xl p-3 bg-[#375CFF]/10">
                <iconify-icon icon="tabler:user" width="28" height="28" class="text-[#375CFF]"></iconify-icon>
            </span>
            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-gray-900 mb-1">Perfil</h3>
                <p class="text-sm text-gray-500">Actualiza tu nombre, email y avatar.</p>
            </div>
            <span class="text-sm font-medium text-[#375CFF]">Abrir →</span>
        </a>
        <a href="{{ route('settings.password') }}" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm flex items-center gap-4 transition hover:shadow-md hover:border-[#375CFF]/30">
            <span class="rounded-xl p-3 bg-emerald-100">
                <iconify-icon icon="tabler:lock" width="28" height="28" class="text-emerald-600"></iconify-icon>
            </span>
            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-gray-900 mb-1">Seguridad</h3>
                <p class="text-sm text-gray-500">Cambia tu contraseña y protege tu cuenta.</p>
            </div>
            <span class="text-sm font-medium text-[#375CFF]">Abrir →</span>
        </a>
        <a href="{{ route('settings.2fa') }}" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm flex items-center gap-4 transition hover:shadow-md hover:border-[#375CFF]/30">
            <span class="rounded-xl p-3 bg-emerald-100">
                <iconify-icon icon="tabler:shield" width="28" height="28" class="text-emerald-600"></iconify-icon>
            </span>
            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-gray-900 mb-1">2FA</h3>
                <p class="text-sm text-gray-500">Recomendado: activa 2FA con QR y guarda tus códigos.</p>
            </div>
            <span class="text-sm font-medium text-[#375CFF]">Abrir →</span>
        </a>
    </div>

    {{-- Gráficos (cajas blancas sobreadas) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Propiedades ofertadas</h3>
                <span class="rounded-lg p-2 bg-[#375CFF]/10">
                    <iconify-icon icon="tabler:building" width="22" height="22" class="text-[#375CFF]"></iconify-icon>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900 mb-4">Total ofertadas: {{ number_format($chartData['chart_properties_offered']['total']) }}</p>
            <div class="h-48"><canvas id="chart-properties"></canvas></div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Búsquedas de propiedades publicadas</h3>
                <span class="rounded-lg p-2 bg-[#375CFF]/10">
                    <iconify-icon icon="tabler:search" width="22" height="22" class="text-[#375CFF]"></iconify-icon>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900 mb-4">Total búsquedas: {{ number_format($chartData['chart_property_searches']['total']) }}</p>
            <div class="h-48"><canvas id="chart-searches"></canvas></div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Ingresos por arriendo</h3>
                <span class="rounded-lg p-2 bg-emerald-100">
                    <iconify-icon icon="tabler:currency-dollar" width="22" height="22" class="text-emerald-600"></iconify-icon>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900 mb-4">Promedio mensual: {{ $chartData['chart_rental_income']['total_percent'] }}%</p>
            <div class="h-48"><canvas id="chart-income"></canvas></div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Mensajes solicitados / contestados</h3>
                <span class="rounded-lg p-2 bg-[#375CFF]/10">
                    <iconify-icon icon="tabler:message-circle" width="22" height="22" class="text-[#375CFF]"></iconify-icon>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900 mb-4">Total mensajes: {{ number_format($chartData['chart_messages']['total']) }}</p>
            <div class="h-48"><canvas id="chart-messages"></canvas></div>
        </div>
    </div>

    {{-- KPIs (cajas blancas sobreadas) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($chartData['kpis'] as $key => $kpi)
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm flex items-center gap-4">
            <span class="rounded-xl p-3 bg-[#375CFF]/10">
                <iconify-icon icon="tabler:chart-bar" width="24" height="24" class="text-[#375CFF]"></iconify-icon>
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 truncate">{{ $kpi['label'] }}</p>
                <p class="text-xl font-bold text-gray-900 truncate">{{ is_float($kpi['value']) ? number_format($kpi['value'], 1) : number_format($kpi['value']) }}{{ $kpi['suffix'] }}</p>
                <p class="text-xs mt-0.5 {{ $kpi['trend'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ $kpi['trend'] >= 0 ? '↑' : '↓' }} Última semana {{ $kpi['trend'] >= 0 ? '+' : '' }}{{ $kpi['trend'] }}%
                </p>
            </div>
        </div>
        @endforeach
    </div>

   

    
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.06)' }, ticks: { color: '#5f6a87' } },
            x: { grid: { display: false }, ticks: { color: '#5f6a87', maxRotation: 0 } }
        }
    };
    var primary = 'rgb(55, 92, 255)';
    var primaryBg = 'rgba(55, 92, 255, 0.15)';
    var success = 'rgb(8, 163, 107)';
    var successBg = 'rgba(8, 163, 107, 0.15)';

    @php
        $p = $chartData['chart_properties_offered'];
        $s = $chartData['chart_property_searches'];
        $i = $chartData['chart_rental_income'];
        $m = $chartData['chart_messages'];
    @endphp

    new Chart(document.getElementById('chart-properties'), {
        type: 'line',
        data: {
            labels: @json($p['labels']),
            datasets: [{ label: 'Ofertadas', data: @json($p['data']), borderColor: primary, backgroundColor: primaryBg, fill: true, tension: 0.35 }]
        },
        options: chartOptions
    });
    new Chart(document.getElementById('chart-searches'), {
        type: 'bar',
        data: {
            labels: @json($s['labels']),
            datasets: [{ label: 'Búsquedas', data: @json($s['data']), backgroundColor: primaryBg, borderColor: primary, borderWidth: 1 }]
        },
        options: chartOptions
    });
    new Chart(document.getElementById('chart-income'), {
        type: 'line',
        data: {
            labels: @json($i['labels']),
            datasets: [{ label: 'Ingresos %', data: @json($i['data']), borderColor: success, backgroundColor: successBg, fill: true, tension: 0.35 }]
        },
        options: chartOptions
    });
    new Chart(document.getElementById('chart-messages'), {
        type: 'line',
        data: {
            labels: @json($m['labels']),
            datasets: [{ label: 'Mensajes', data: @json($m['data']), borderColor: primary, backgroundColor: primaryBg, fill: true, tension: 0.35 }]
        },
        options: chartOptions
    });
});
</script>
@endsection
