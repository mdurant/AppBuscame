<section class="space-y-6">
    <p class="text-sm text-gray-600 dark:text-gray-400">Lista de sesiones activas. Puedes terminarlas haciendo clic en el botón de eliminar.</p>

    @php
        $currentHash = $currentSessionIdHash ?? null;
    @endphp

    @forelse($sessions ?? [] as $session)
        @php
            $isCurrent = $currentHash && hash_equals($currentHash, $session->token_hash);
        @endphp
        <div class="rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 {{ $isCurrent ? 'ring-2 ring-violet-500' : '' }}">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="rounded-lg bg-violet-100 dark:bg-violet-900/30 p-3">
                        <iconify-icon icon="tabler:device-desktop" width="28" height="28" class="text-violet-600 dark:text-violet-400"></iconify-icon>
                    </div>
                    <div>
                        @if($isCurrent)
                            <span class="inline-block text-xs font-medium text-violet-600 dark:text-violet-400 mb-1">Sesión actual</span>
                        @endif
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $session->device }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $session->browser }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $session->ip_address ?? '—' }} | {{ $session->last_activity_at?->format('d/m/Y H:i') ?? '—' }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-500">IP: {{ $session->ip_address ?? '—' }}</p>
                    </div>
                </div>
                @if(!$isCurrent)
                    <form method="POST" action="{{ route('settings.sessions.destroy', $session) }}" class="inline" onsubmit="return confirm('¿Terminar esta sesión?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm font-medium text-red-600 dark:text-red-400 hover:underline">Terminar</button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 text-center text-gray-500 dark:text-gray-400">
            No hay otras sesiones registradas.
        </div>
    @endforelse
</section>
