<div id="confirmPasswordModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/75 transition-opacity" onclick="closeConfirmModal()"></div>
        <div class="relative rounded-xl bg-white dark:bg-gray-800 shadow-xl max-w-md w-full p-6">
            <div class="flex justify-between items-start mb-4">
                <h3 id="confirmModalTitle" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Confirmar contraseña</h3>
                <button type="button" onclick="closeConfirmModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <iconify-icon icon="tabler:x" width="24" height="24"></iconify-icon>
                </button>
            </div>
            <p id="confirmModalMessage" class="text-sm text-gray-600 dark:text-gray-400 mb-4">Esta es una zona segura de la aplicación. Confirme su contraseña antes de continuar.</p>
            <form id="confirmPasswordForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contraseña</label>
                    <input type="password" name="password" id="confirm_password" required autocomplete="current-password" placeholder="Contraseña"
                        class="block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 shadow-sm focus:border-violet-500 focus:ring-violet-500">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeConfirmModal()" class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancelar</button>
                    <button type="submit" id="confirmModalSubmit" class="rounded-md bg-violet-600 px-4 py-2 text-sm font-medium text-white hover:bg-violet-700">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openConfirmModal(actionUrl, title, message, needsPassword = true) {
    var modal = document.getElementById('confirmPasswordModal');
    var form = document.getElementById('confirmPasswordForm');
    var titleEl = document.getElementById('confirmModalTitle');
    var messageEl = document.getElementById('confirmModalMessage');
    var submitBtn = document.getElementById('confirmModalSubmit');
    form.action = actionUrl;
    titleEl.textContent = title || 'Confirmar contraseña';
    messageEl.textContent = 'Esta es una zona segura. Confirme su contraseña para ' + (message || 'continuar') + '.';
    submitBtn.textContent = title === 'Eliminar cuenta' ? 'Eliminar cuenta' : 'Confirmar';
    if (needsPassword) {
        form.querySelector('input[name="password"]').required = true;
        form.querySelector('input[name="password"]').style.display = '';
    }
    modal.classList.remove('hidden');
}

function closeConfirmModal() {
    document.getElementById('confirmPasswordModal').classList.add('hidden');
}
</script>
