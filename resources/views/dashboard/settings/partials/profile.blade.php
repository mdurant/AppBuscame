<div class="space-y-6">
    {{-- Fila horizontal: formulario (2 cols) + info actividad --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Formulario de perfil (ocupa 2 columnas en lg) --}}
        <section class="lg:col-span-2 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Datos personales</h2>
            <p class="text-sm text-gray-500 mb-6">Actualiza tu información personal y profesional.</p>

            <form method="POST" action="{{ route('settings.profile.update') }}" class="space-y-4" id="profile-form">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Nombres</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->profile?->first_name) }}" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-gray-900 shadow-sm focus:border-[#375CFF] focus:ring-[#375CFF]">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Apellidos</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->profile?->last_name) }}" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-gray-900 shadow-sm focus:border-[#375CFF] focus:ring-[#375CFF]">
                    </div>
                </div>
                <div>
                    <x-input-group-email name="email_display" label="Correo electrónico" :value="$user->email" placeholder="info@gmail.com" disabled />
                    <p class="mt-1 text-xs text-gray-500">El correo no se puede cambiar desde aquí.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-group-phone name="phone" label="Teléfono (celular Chile)" :value="$user->profile?->phone" placeholder="+56 9 1234 5678" />
                    </div>
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Sexo</label>
                        <select name="gender" id="gender" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-gray-900 shadow-sm focus:border-[#375CFF] focus:ring-[#375CFF]">
                            <option value="">Seleccionar</option>
                            <option value="Hombre" {{ old('gender', $user->profile?->gender) === 'Hombre' ? 'selected' : '' }}>Hombre</option>
                            <option value="Mujer" {{ old('gender', $user->profile?->gender) === 'Mujer' ? 'selected' : '' }}>Mujer</option>
                            <option value="Prefiero no aportar" {{ old('gender', $user->profile?->gender) === 'Prefiero no aportar' ? 'selected' : '' }}>Prefiero no aportar</option>
                        </select>
                    </div>
                </div>
                <div class="max-w-xs">
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Fecha de nacimiento</label>
                    <input type="text" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $user->profile?->date_of_birth?->format('d-m-Y')) }}" placeholder="DD-MM-AAAA" readonly
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-gray-900 shadow-sm focus:border-[#375CFF] focus:ring-[#375CFF]">
                </div>
                <div>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-[#375CFF] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#2d4dd4] transition">
                        <iconify-icon icon="tabler:device-floppy" width="18" height="18"></iconify-icon>
                        Guardar
                    </button>
                </div>
            </form>
        </section>

        {{-- Info: actividad y última sesión --}}
        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900 mb-2 flex items-center gap-2">
                <iconify-icon icon="tabler:activity" width="22" height="22" class="text-[#375CFF]"></iconify-icon>
                Actividad
            </h2>
            <p class="text-sm text-gray-500 mb-4">Estado en la plataforma y última sesión.</p>
            <dl class="space-y-4">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Activo en la plataforma</dt>
                    <dd class="mt-1 flex items-center gap-2">
                        @php
                            $last = $latestActivityAt ?? null;
                            $isActive = $last && $last->diffInMinutes(now()) < 5;
                        @endphp
                        @if($last)
                            <span class="inline-flex h-2.5 w-2.5 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                            @if($isActive)
                                <span class="text-sm font-medium text-emerald-700">Activo ahora</span>
                            @else
                                <span class="text-sm text-gray-700">Última actividad {{ $last->diffForHumans() }}</span>
                            @endif
                        @else
                            <span class="inline-flex h-2.5 w-2.5 rounded-full bg-gray-300"></span>
                            <span class="text-sm text-gray-500">Sin registro reciente</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Última sesión</dt>
                    <dd class="mt-1 text-sm text-gray-700">
                        @if($sessionStartedAt ?? null)
                            Iniciada el {{ $sessionStartedAt->format('d/m/Y') }} a las {{ $sessionStartedAt->format('H:i') }}
                        @elseif($latestActivityAt ?? null)
                            Última actividad: {{ $latestActivityAt->format('d/m/Y H:i') }}
                        @else
                            —
                        @endif
                    </dd>
                </div>
            </dl>
            <a href="{{ route('settings.sessions') }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-medium text-[#375CFF] hover:text-[#2d4dd4]">
                Ver sesiones activas
                <iconify-icon icon="tabler:arrow-right" width="16" height="16"></iconify-icon>
            </a>
        </section>
    </div>

    {{-- Eliminar cuenta (fila aparte) --}}
    <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-red-600 mb-2">Eliminar cuenta</h2>
        <p class="text-sm text-gray-500 mb-4">Eliminar tu cuenta y todos tus recursos.</p>
        <button type="button" onclick="openConfirmModal('{{ route('settings.account.destroy') }}', 'Eliminar cuenta', 'eliminar tu cuenta')" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-700">
            <iconify-icon icon="tabler:trash" width="18" height="18"></iconify-icon>
            Eliminar cuenta
        </button>
    </section>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var input = document.getElementById('date_of_birth');
    if (!input) return;
    flatpickr(input, {
        dateFormat: 'd-m-Y',
        altInput: false,
        allowInput: true,
        locale: 'es',
        maxDate: 'today',
        disableMobile: true,
        onReady: function(selectedDates, dateStr, instance) {
            instance.calendarContainer.classList.add('flatpickr-theme-dashboard');
        }
    });
});
</script>
<style>
.flatpickr-theme-dashboard.flatpickr-calendar { box-shadow: 0 10px 40px -10px rgba(0,0,0,0.15); border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; }
.flatpickr-theme-dashboard .flatpickr-day.selected,
.flatpickr-theme-dashboard .flatpickr-day.startRange,
.flatpickr-theme-dashboard .flatpickr-day.endRange { background: #375CFF !important; border-color: #375CFF !important; }
.flatpickr-theme-dashboard .flatpickr-day:hover { background: rgba(55, 92, 255, 0.12) !important; border-color: rgba(55, 92, 255, 0.2) !important; }
.flatpickr-theme-dashboard .flatpickr-months .flatpickr-month { background: #375CFF !important; }
.flatpickr-theme-dashboard .flatpickr-current-month .numInputWrapper:hover { background: rgba(255,255,255,0.1) !important; }
.flatpickr-theme-dashboard span.flatpickr-weekday { color: #5f6a87; }
.flatpickr-theme-dashboard .flatpickr-weekdays { background: #f8fafc !important; }
</style>
