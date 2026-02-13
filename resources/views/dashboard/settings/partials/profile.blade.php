<div class="space-y-8">
    <section class="rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold mb-2">Perfil</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Actualiza tu información personal y profesional.</p>

        <form method="POST" action="{{ route('settings.profile.update') }}" class="space-y-4 max-w-xl">
            @csrf
            @method('PUT')
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombres</label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->profile?->first_name) }}" required
                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 shadow-sm focus:border-violet-500 focus:ring-violet-500">
            </div>
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellidos</label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->profile?->last_name) }}" required
                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 shadow-sm focus:border-violet-500 focus:ring-violet-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo electrónico</label>
                <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->profile?->phone) }}" placeholder="Opcional"
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

    <section class="rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold mb-2 text-red-600 dark:text-red-400">Eliminar cuenta</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Eliminar tu cuenta y todos tus recursos.</p>
        <button type="button" onclick="openConfirmModal('{{ route('settings.account.destroy') }}', 'Eliminar cuenta', 'eliminar tu cuenta')" class="inline-flex items-center gap-2 rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
            <iconify-icon icon="tabler:trash" width="18" height="18"></iconify-icon>
            Eliminar cuenta
        </button>
    </section>
</div>
