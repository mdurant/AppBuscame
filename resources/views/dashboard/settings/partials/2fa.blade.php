<section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm max-w-2xl">
    <h2 class="text-lg font-semibold text-gray-900 mb-2">Autenticación de Dos Factores (2FA)</h2>
    <p class="text-sm text-gray-500 mb-6">Gestiona la configuración de autenticación de dos factores de tu cuenta.</p>

    @if($user->hasTwoFactorEnabled())
        <div class="flex items-center gap-2 mb-4">
            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-sm font-medium text-emerald-800">Activado</span>
        </div>
        <p class="text-sm text-gray-500 mb-6">Se te solicitará un código de 6 dígitos al iniciar sesión. Obtén el código desde tu app de autenticación (Google Authenticator, etc.).</p>
        <form method="POST" action="{{ route('settings.2fa.disable') }}" id="form-disable-2fa" class="inline">
            @csrf
            <button type="button" onclick="openConfirmModal('{{ route('settings.2fa.disable') }}', 'Desactivar 2FA', 'desactivar la autenticación de dos factores', true)" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-700">
                <iconify-icon icon="tabler:shield-off" width="18" height="18"></iconify-icon>
                Desactivar 2FA
            </button>
        </form>
    @else
        <div class="flex items-center gap-2 mb-4">
            <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-700">Desactivado</span>
        </div>
        <p class="text-sm text-gray-500 mb-4">Al activar 2FA se te pedirá un código de 6 dígitos en cada inicio de sesión. Usa una app TOTP en tu teléfono.</p>
        <p class="text-sm text-gray-500 mb-4">Aplicaciones recomendadas:</p>
        <ul class="flex flex-wrap gap-2 mb-6">
            <li><a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" rel="noopener" class="text-[#375CFF] hover:underline font-medium">Google Authenticator</a></li>
            <li><a href="https://2fas.com/" target="_blank" rel="noopener" class="text-[#375CFF] hover:underline font-medium">2FAS</a></li>
            <li><a href="https://www.microsoft.com/en-us/security/mobile-authenticator-app" target="_blank" rel="noopener" class="text-[#375CFF] hover:underline font-medium">Microsoft Authenticator</a></li>
            <li><a href="https://authy.com/" target="_blank" rel="noopener" class="text-[#375CFF] hover:underline font-medium">Authy</a></li>
        </ul>
        @if(session('2fa_qr_url'))
            <div class="rounded-xl border border-gray-200 bg-gray-50/80 p-6 mb-6">
                <p class="text-sm font-medium text-gray-800 mb-4">Escanea el código QR con tu app (Google Authenticator, etc.) y luego introduce aquí los <strong>6 dígitos</strong> que muestra la app.</p>
                <div class="flex flex-col items-center gap-4">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode(session('2fa_qr_url')) }}" alt="Código QR 2FA" class="rounded-lg border border-gray-200 bg-white p-1">
                    <p class="text-xs text-gray-500">Si no puedes escanear, ingresa esta clave manualmente en la app:</p>
                    <div class="flex items-center gap-2 w-full max-w-xs">
                        <input type="text" readonly value="{{ session('2fa_secret') }}" id="2fa-secret-input" class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-mono text-gray-700">
                        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('2fa-secret-input').value); this.textContent='Copiado'" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-600 hover:bg-gray-50" title="Copiar">
                            <iconify-icon icon="tabler:copy" width="18" height="18"></iconify-icon>
                        </button>
                    </div>
                </div>
                <form method="POST" action="{{ route('settings.2fa.confirm') }}" class="mt-6">
                    @csrf
                    <label for="2fa_code" class="block text-sm font-medium text-gray-700 mb-2">Introduce los 6 dígitos que muestra tu app</label>
                    <div class="flex flex-col sm:flex-row gap-3 items-start">
                        <input type="text" name="code" id="2fa_code" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autocomplete="one-time-code" required
                            placeholder="123456"
                            class="w-full sm:w-48 rounded-lg border-2 border-gray-300 bg-white px-4 py-3 text-center text-xl font-semibold tracking-[0.35em] text-gray-900 focus:border-[#375CFF] focus:ring-2 focus:ring-[#375CFF]/20">
                        <button type="submit" class="rounded-lg bg-[#375CFF] px-5 py-3 text-sm font-medium text-white hover:bg-[#2d4dd4] transition">
                            Verificar y activar 2FA
                        </button>
                    </div>
                    @if($errors->has('code'))
                        <p class="mt-2 text-sm text-red-600">{{ $errors->first('code') }}</p>
                    @endif
                </form>
            </div>
        @else
            <form method="POST" action="{{ route('settings.2fa.enable') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-[#375CFF] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#2d4dd4]">
                    <iconify-icon icon="tabler:shield" width="18" height="18"></iconify-icon>
                    Activar 2FA
                </button>
            </form>
        @endif
    @endif
</section>

@if(session('backup_codes'))
<div class="mt-6 rounded-lg border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-6 max-w-2xl">
    <h3 class="font-semibold text-amber-800 dark:text-amber-200 mb-2">Códigos de respaldo</h3>
    <p class="text-sm text-amber-700 dark:text-amber-300 mb-4">Guarda estos códigos en un lugar seguro. Cada uno solo puede usarse una vez.</p>
    <pre class="text-sm font-mono bg-white dark:bg-gray-800 p-4 rounded overflow-x-auto">{{ implode(' ', session('backup_codes')) }}</pre>
</div>
@endif
