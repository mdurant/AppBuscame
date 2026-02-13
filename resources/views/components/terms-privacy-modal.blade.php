@props(['termsVersion'])

@php
    $content = $termsVersion->content ?? '';
    $parts = preg_split('/\n---+\s*\n/', $content, 2);
    $termsContent = trim($parts[0] ?? $content);
    $privacyContent = trim($parts[1] ?? '');
    if ($privacyContent === '' && preg_match('/\n(PRIVACIDAD|Política de [Pp]rivacidad).*$/s', $content, $m)) {
        $termsContent = trim(preg_replace('/\n(PRIVACIDAD|Política de [Pp]rivacidad).*$/s', '', $content));
        $privacyContent = trim(preg_replace('/^.*?\n(PRIVACIDAD|Política de [Pp]rivacidad)\s*\n/s', '', $content));
    }
@endphp

<div id="terms-privacy-modal" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true" aria-labelledby="terms-modal-title" aria-hidden="true">
    {{-- Overlay con transición --}}
    <div id="terms-modal-backdrop" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>

    {{-- Contenedor centrado --}}
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div id="terms-modal-panel" class="relative w-full max-w-2xl max-h-[85vh] flex flex-col rounded-2xl border border-gray-200 bg-white shadow-2xl transform transition-all duration-300 scale-95 opacity-0">
            {{-- Cabecera --}}
            <div class="flex items-center justify-between flex-shrink-0 px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-2xl">
                <h2 id="terms-modal-title" class="sr-only">Términos y Condiciones y Política de Privacidad</h2>
                <div class="flex rounded-lg p-1 bg-white border border-gray-200 shadow-sm" role="tablist">
                    <button type="button" data-terms-tab="terms" class="terms-tab px-4 py-2 text-sm font-medium rounded-md transition tab-active bg-[#375CFF] text-white">
                        Términos y Condiciones
                    </button>
                    <button type="button" data-terms-tab="privacy" class="terms-tab px-4 py-2 text-sm font-medium rounded-md transition text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        Política de Privacidad
                    </button>
                </div>
                <button type="button" id="terms-modal-close" class="p-2 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-200 transition" aria-label="Cerrar">
                    <iconify-icon icon="tabler:x" width="24" height="24"></iconify-icon>
                </button>
            </div>

            {{-- Cuerpo con scroll --}}
            <div class="flex-1 overflow-y-auto px-6 py-5">
                <div id="terms-tab-content" class="terms-tab-pane prose prose-gray max-w-none">
                    <p class="text-xs text-gray-500 mb-4">Versión {{ $termsVersion->version }} · Vigente desde {{ $termsVersion->effective_at?->format('d/m/Y') ?? $termsVersion->created_at->format('d/m/Y') }}</p>
                    <div class="text-gray-700 whitespace-pre-wrap text-sm leading-relaxed">{{ $termsContent }}</div>
                </div>
                <div id="privacy-tab-content" class="terms-tab-pane hidden prose prose-gray max-w-none">
                    @if($privacyContent !== '')
                        <div class="text-gray-700 whitespace-pre-wrap text-sm leading-relaxed">{{ $privacyContent }}</div>
                    @else
                        <p class="text-gray-500">La política de privacidad se incluye en los términos anteriores.</p>
                    @endif
                </div>
            </div>

            {{-- Pie --}}
            <div class="flex-shrink-0 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button type="button" id="terms-modal-close-btn" class="w-full rounded-lg bg-[#375CFF] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#2d4dd4] transition">
                    Entendido
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var modal = document.getElementById('terms-privacy-modal');
    var backdrop = document.getElementById('terms-modal-backdrop');
    var panel = document.getElementById('terms-modal-panel');
    var closeBtn = document.getElementById('terms-modal-close');
    var closeBtnFooter = document.getElementById('terms-modal-close-btn');
    var tabTerms = document.querySelector('[data-terms-tab="terms"]');
    var tabPrivacy = document.querySelector('[data-terms-tab="privacy"]');
    var contentTerms = document.getElementById('terms-tab-content');
    var contentPrivacy = document.getElementById('privacy-tab-content');

    function openModal(showPrivacy) {
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        if (showPrivacy) {
            tabTerms.classList.remove('tab-active', 'bg-[#375CFF]', 'text-white');
            tabTerms.classList.add('text-gray-600');
            tabPrivacy.classList.add('tab-active', 'bg-[#375CFF]', 'text-white');
            tabPrivacy.classList.remove('text-gray-600');
            contentTerms.classList.add('hidden');
            contentPrivacy.classList.remove('hidden');
        } else {
            tabPrivacy.classList.remove('tab-active', 'bg-[#375CFF]', 'text-white');
            tabPrivacy.classList.add('text-gray-600');
            tabTerms.classList.add('tab-active', 'bg-[#375CFF]', 'text-white');
            tabTerms.classList.remove('text-gray-600');
            contentPrivacy.classList.add('hidden');
            contentTerms.classList.remove('hidden');
        }
        requestAnimationFrame(function() {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('scale-95', 'opacity-0');
            panel.classList.add('scale-100', 'opacity-100');
        });
    }

    function closeModal() {
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('scale-100', 'opacity-100');
        panel.classList.add('scale-95', 'opacity-0');
        setTimeout(function() {
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }, 250);
    }

    document.querySelectorAll('[data-open-terms-modal]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            openModal(el.getAttribute('data-open-terms-modal') === 'privacy');
        });
    });

    if (tabTerms) tabTerms.addEventListener('click', function() {
        tabTerms.classList.add('tab-active', 'bg-[#375CFF]', 'text-white');
        tabTerms.classList.remove('text-gray-600');
        tabPrivacy.classList.remove('tab-active', 'bg-[#375CFF]', 'text-white');
        tabPrivacy.classList.add('text-gray-600');
        contentPrivacy.classList.add('hidden');
        contentTerms.classList.remove('hidden');
    });
    if (tabPrivacy) tabPrivacy.addEventListener('click', function() {
        tabPrivacy.classList.add('tab-active', 'bg-[#375CFF]', 'text-white');
        tabPrivacy.classList.remove('text-gray-600');
        tabTerms.classList.remove('tab-active', 'bg-[#375CFF]', 'text-white');
        tabTerms.classList.add('text-gray-600');
        contentTerms.classList.add('hidden');
        contentPrivacy.classList.remove('hidden');
    });

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (closeBtnFooter) closeBtnFooter.addEventListener('click', closeModal);
    if (backdrop) backdrop.addEventListener('click', closeModal);

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });
})();
</script>
