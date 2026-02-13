<section class="rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 max-w-2xl">
    <h2 class="text-lg font-semibold mb-2">Autenticación de Dos Factores (2FA)</h2>
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Gestiona la configuración de autenticación de dos factores de tu cuenta.</p>

    @if($user->hasTwoFactorEnabled())
        <div class="flex items-center gap-2 mb-4">
            <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-3 py-1 text-sm font-medium text-green-800 dark:text-green-200">Activado</span>
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Cuando actives la autenticación de dos factores, se te solicitará un código PIN seguro durante el inicio de sesión. Este código puede obtenerse de una aplicación compatible con TOTP en tu teléfono.</p>
        <form method="POST" action="{{ route('settings.2fa.disable') }}" id="form-disable-2fa" class="inline">
            @csrf
            <button type="button" onclick="openConfirmModal('{{ route('settings.2fa.disable') }}', 'Desactivar 2FA', 'desactivar la autenticación de dos factores', true)" class="inline-flex items-center gap-2 rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                <iconify-icon icon="tabler:shield-off" width="18" height="18"></iconify-icon>
                Desactivar 2FA
            </button>
        </form>
    @else
        <div class="flex items-center gap-2 mb-4">
            <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-3 py-1 text-sm font-medium text-red-800 dark:text-red-200">Desactivado</span>
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Cuando actives la autenticación de dos factores, se te solicitará un código PIN seguro durante el inicio de sesión. Este código puede obtenerse de una aplicación compatible con TOTP en tu teléfono.</p>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Aplicaciones recomendadas (cada enlace se abre en una nueva pestaña):</p>
        <ul class="flex flex-wrap gap-2 mb-6">
            <li><a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" rel="noopener" class="text-violet-600 dark:text-violet-400 hover:underline">Google Authenticator</a></li>
            <li><a href="https://2fas.com/" target="_blank" rel="noopener" class="text-violet-600 dark:text-violet-400 hover:underline">2FAS Authenticator</a></li>
            <li><a href="https://www.microsoft.com/en-us/security/mobile-authenticator-app" target="_blank" rel="noopener" class="text-violet-600 dark:text-violet-400 hover:underline">Microsoft Authenticator</a></li>
            <li><a href="https://authy.com/" target="_blank" rel="noopener" class="text-violet-600 dark:text-violet-400 hover:underline">Authy</a></li>
        </ul>
        @if(session('2fa_qr_url'))
            <div class="rounded-lg border border-violet-200 dark:border-violet-800 p-4 mb-6 bg-violet-50/50 dark:bg-violet-900/10">
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-4">Para terminar de activar la autenticación de dos factores, escanea el código QR o ingresa la clave de configuración en tu aplicación de autenticación.</p>
                <div class="flex flex-col items-center gap-4">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(session('2fa_qr_url')) }}" alt="Código QR 2FA" class="rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400">o, ingresa el código manualmente:</p>
                    <div class="flex items-center gap-2 w-full max-w-xs">
                        <input type="text" readonly value="{{ session('2fa_secret') }}" id="2fa-secret-input" class="flex-1 rounded-md border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 px-3 py-2 text-sm font-mono">
                        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('2fa-secret-input').value); this.textContent='Copiado'" class="rounded-md bg-gray-200 dark:bg-gray-700 px-3 py-2" title="Copiar">
                            <iconify-icon icon="tabler:copy" width="18" height="18"></iconify-icon>
                        </button>
                    </div>
                </div>
                <form method="POST" action="{{ route('settings.2fa.confirm') }}" class="mt-6">
                    @csrf
                    <label for="2fa_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Código de 6 dígitos</label>
                    <div class="flex gap-2">
                        <input type="text" name="code" id="2fa_code" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" placeholder="000000" required
                            class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-center text-lg tracking-widest">
                        <button type="submit" class="rounded-md bg-violet-600 px-4 py-2 text-sm font-medium text-white hover:bg-violet-700">Continuar</button>
                    </div>
                </form>
            </div>
        @else
            <form method="POST" action="{{ route('settings.2fa.enable') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-violet-600 px-4 py-2 text-sm font-medium text-white hover:bg-violet-700">
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
