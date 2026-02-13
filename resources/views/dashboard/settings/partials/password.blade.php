<section class="rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 max-w-xl">
    <h2 class="text-lg font-semibold mb-2">Seguridad</h2>
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Asegúrate de usar una contraseña larga y aleatoria para mantener tu cuenta segura.</p>

    <form method="POST" action="{{ route('settings.password.update') }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña actual</label>
            <input type="password" name="current_password" id="current_password" required autocomplete="current-password"
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 shadow-sm focus:border-violet-500 focus:ring-violet-500">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nueva contraseña</label>
            <input type="password" name="password" id="password" required autocomplete="new-password"
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 shadow-sm focus:border-violet-500 focus:ring-violet-500">
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 shadow-sm focus:border-violet-500 focus:ring-violet-500">
        </div>
        <div>
            <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-violet-600 px-4 py-2 text-sm font-medium text-white hover:bg-violet-700">
                <iconify-icon icon="tabler:device-floppy" width="18" height="18"></iconify-icon>
                Guardar
            </button>
        </div>
    </form>
</section>
