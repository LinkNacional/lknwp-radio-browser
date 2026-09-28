/**
 * JavaScript específico para a página de ajuda do LKN Radio Browser
 * Inclui: navegação por abas, busca/filtro de conteúdo e cópia de shortcodes.
 */

(function ($) {
    'use strict';

    /**
     * Função para copiar texto para a área de transferência
     */
    window.copyToClipboard = function (text, button) {
        if (navigator.clipboard && window.isSecureContext !== false) {
            navigator.clipboard.writeText(text).then(function () {
                showCopySuccess(button);
            }).catch(function () {
                fallbackCopyTextToClipboard(text, button);
            });
        } else {
            fallbackCopyTextToClipboard(text, button);
        }
    };

    /**
     * Fallback para navegadores que não suportam navigator.clipboard
     */
    function fallbackCopyTextToClipboard(text, button) {
        var textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.top = '0';
        textArea.style.left = '0';
        textArea.style.position = 'fixed';

        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            if (document.execCommand('copy')) {
                showCopySuccess(button);
            }
        } catch (err) {
            // silencioso
        }

        document.body.removeChild(textArea);
    }

    /**
     * Mostra feedback visual de sucesso na cópia
     */
    function showCopySuccess(button) {
        if (!button) {
            return;
        }

        var isIcon = button.hasAttribute('data-copy') && button.textContent.trim().length <= 2;
        var originalText = button.getAttribute('data-original-text') || button.textContent;

        if (!button.getAttribute('data-original-text')) {
            button.setAttribute('data-original-text', originalText);
        }

        button.textContent = isIcon
            ? '✓'
            : (window.lknwpRadioTexts ? lknwpRadioTexts.copied : 'Copied!');
        button.classList.add('active');

        setTimeout(function () {
            button.textContent = originalText;
            button.classList.remove('active');
        }, 1600);
    }

    $(document).ready(function () {
        var $root = $('.lknwp-radio-help');
        if (!$root.length) {
            return;
        }

        /* ============================================================
         * Copiar shortcodes / parâmetros
         * ============================================================ */
        $(document).on('click', '.lknwp-radio-copy-btn, [data-copy]', function (e) {
            e.preventDefault();

            var value = $(this).attr('data-copy');
            if (!value) {
                // Compatibilidade: usa o <code> irmão quando não há data-copy
                value = $(this).siblings('code').first().text();
            }
            if (value) {
                copyToClipboard(value, this);
            }
        });

        $('.lknwp-radio-copy-btn').attr('title', function () {
            return window.lknwpRadioTexts ? lknwpRadioTexts.clickToCopy : 'Click to copy shortcode';
        });

        /* ============================================================
         * Navegação por abas
         * ============================================================ */
        var $navItems = $root.find('.lknwp-radio-nav-item');
        var $panels = $root.find('.lknwp-radio-panel');

        function activatePanel(id, updateHash) {
            var $panel = $('#' + id);
            if (!$panel.length) {
                return;
            }

            $navItems.removeClass('is-active');
            $navItems.filter('[data-target="' + id + '"]').addClass('is-active');

            $panels.removeClass('is-active');
            $panel.addClass('is-active');

            if (updateHash && history.replaceState) {
                history.replaceState(null, '', '#' + id);
            }
        }

        $navItems.on('click', function () {
            // Ao navegar manualmente, limpa a busca
            if ($root.hasClass('is-searching')) {
                $('#lknwp-radio-help-search').val('');
                clearSearch();
            }
            activatePanel($(this).data('target'), true);
        });

        // Abre a aba a partir do hash da URL (ex.: #panel-faq)
        if (location.hash) {
            var hashId = location.hash.replace('#', '');
            if ($navItems.filter('[data-target="' + hashId + '"]').length) {
                activatePanel(hashId, false);
            }
        }

        /* ============================================================
         * Busca / filtro de conteúdo
         * ============================================================ */
        var $search = $('#lknwp-radio-help-search');
        var $clear = $('#lknwp-radio-help-search-clear');
        var $noResults = $('#lknwp-radio-no-results');
        var $units = $root.find('[data-sf]');
        var $containers = $root.find('.lknwp-sf-container');

        function clearSearch() {
            $root.removeClass('is-searching');
            $units.removeClass('lknwp-sf-hidden lknwp-sf-match');
            $panels.removeClass('is-empty-search');
            $containers.removeClass('is-empty-search');
            $navItems.removeClass('is-dimmed');
            $noResults.attr('hidden', true);
            $clear.attr('hidden', true);
        }

        function runSearch(query) {
            query = (query || '').trim().toLowerCase();

            if (!query) {
                clearSearch();
                return;
            }

            $root.addClass('is-searching');
            $clear.removeAttr('hidden');

            $units.each(function () {
                var match = $(this).text().toLowerCase().indexOf(query) !== -1;
                $(this)
                    .toggleClass('lknwp-sf-match', match)
                    .toggleClass('lknwp-sf-hidden', !match);
            });

            // Oculta containers (ex.: cards com tabela) sem nenhum item visível
            $containers.each(function () {
                var any = $(this).find('[data-sf]:not(.lknwp-sf-hidden)').length > 0;
                $(this).toggleClass('is-empty-search', !any);
            });

            // Oculta painéis sem resultados e marca a navegação
            var anyPanelVisible = false;
            $panels.each(function () {
                var $p = $(this);
                var any = $p.find('[data-sf]:not(.lknwp-sf-hidden)').length > 0;
                $p.toggleClass('is-empty-search', !any);
                if (any) {
                    anyPanelVisible = true;
                }

                var targetId = $p.attr('id');
                $navItems.filter('[data-target="' + targetId + '"]')
                    .toggleClass('is-dimmed', !any);
            });

            if (anyPanelVisible) {
                $noResults.attr('hidden', true);
            } else {
                $noResults.removeAttr('hidden');
            }
        }

        var searchTimer = null;
        $search.on('input', function () {
            var val = this.value;
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () {
                runSearch(val);
            }, 120);
        });

        $clear.on('click', function () {
            $search.val('').trigger('focus');
            clearSearch();
        });

        // Esc limpa a busca
        $search.on('keydown', function (e) {
            if (e.key === 'Escape') {
                $search.val('');
                clearSearch();
            }
        });
    });

})(jQuery);
