@props(['toasts' => []])

<div id="toaster-container" class="toaster-zone" aria-live="polite" aria-label="Notificaciones"></div>

@if (!empty($toasts))
<script type="application/json" id="toaster-initial">@json($toasts)</script>
@endif

<style>
.toaster-zone {
    position: fixed;
    top: 1rem;
    right: 1rem;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    max-width: 24rem;
    width: 100%;
    pointer-events: none;
}
.toast-item {
    pointer-events: auto;
    padding: 0.75rem 1rem;
    border-radius: var(--radius-card, 0.75rem);
    font-size: 0.875rem;
    line-height: 1.4;
    box-shadow: var(--shadow-card, 0 1px 3px rgb(0 0 0 / 0.08));
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    animation: toast-in 0.25s ease-out;
}
.toast-item.toast-out {
    animation: toast-out 0.2s ease-in forwards;
}
.toast-item.toast-success {
    background: var(--color-success-bg, #e6f6f0);
    color: var(--color-success, #08a36b);
    border: 1px solid rgb(8 163 107 / 0.2);
}
.toast-item.toast-error {
    background: var(--color-error-bg, #ffefef);
    color: var(--color-error, #ff5e5e);
    border: 1px solid rgb(255 94 94 / 0.2);
}
.toast-item.toast-info {
    background: var(--color-primary-light, #f3efff);
    color: var(--color-primary, #633dfe);
    border: 1px solid rgb(99 61 254 / 0.2);
}
.toast-item .toast-icon { flex-shrink: 0; margin-top: 0.125rem; }
.toast-item .toast-message { flex: 1; color: var(--color-text, #2c2c2c); }
.toast-item.toast-success .toast-message { color: #0d5c42; }
.toast-item.toast-error .toast-message { color: #b91c1c; }
.toast-item.toast-info .toast-message { color: var(--color-text); }
@keyframes toast-in {
    from { opacity: 0; transform: translateX(100%); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes toast-out {
    from { opacity: 1; transform: translateX(0); }
    to { opacity: 0; transform: translateX(100%); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('toaster-container');
    if (!container) return;
    var initialEl = document.getElementById('toaster-initial');
    var toasts = initialEl ? JSON.parse(initialEl.textContent) : [];
    var duration = 5000;

    function showToast(type, message) {
        var icons = { success: 'tabler:circle-check', error: 'tabler:circle-x', info: 'tabler:info-circle' };
        var icon = icons[type] || icons.info;
        var div = document.createElement('div');
        div.className = 'toast-item toast-' + type;
        div.setAttribute('role', 'alert');
        div.innerHTML = '<span class="toast-icon"><iconify-icon icon="' + icon + '" width="20" height="20"></iconify-icon></span><span class="toast-message">' + escapeHtml(message) + '</span>';
        container.appendChild(div);
        if (window.iconify && window.iconify.scan) window.iconify.scan(div);
        setTimeout(function() {
            div.classList.add('toast-out');
            setTimeout(function() { div.remove(); }, 220);
        }, duration);
    }
    function escapeHtml(s) {
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }
    toasts.forEach(function(t) { showToast(t.type || 'success', t.message); });
    window.toaster = { show: showToast };
});
</script>
