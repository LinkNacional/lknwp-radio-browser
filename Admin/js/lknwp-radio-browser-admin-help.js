/**
 * JavaScript específico para a página de ajuda do LKN Radio Browser
 */

(function ($) {
    'use strict';

    /**
     * Função para copiar texto para a área de transferência
     */
    window.copyToClipboard = function (text, button) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function () {
                showCopySuccess(button);
            }).catch(function (err) {
                console.error('Erro ao copiar: ', err);
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
        var textArea = document.createElement("textarea");
        textArea.value = text;

        // Avoid scrolling to bottom
        textArea.style.top = "0";
        textArea.style.left = "0";
        textArea.style.position = "fixed";

        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            var successful = document.execCommand('copy');
            if (successful) {
                showCopySuccess(button);
            } else {
                console.error('Fallback: Não foi possível copiar');
            }
        } catch (err) {
            console.error('Fallback: Erro ao copiar', err);
        }

        document.body.removeChild(textArea);
    }

    /**
     * Mostra feedback visual de sucesso na cópia
     */
    function showCopySuccess(button) {
        if (!button || !button.textContent) {
            console.error('Botão não encontrado ou inválido');
            return;
        }

        // Salva o texto original antes de qualquer modificação
        var originalText = button.getAttribute('data-original-text') || button.textContent;

        // Salva o texto original como atributo para próximas chamadas
        if (!button.getAttribute('data-original-text')) {
            button.setAttribute('data-original-text', originalText);
        }

        // Adiciona classe active e muda texto
        button.textContent = 'Copiado!';
        button.classList.add('active');

        // Restaura após 2 segundos
        setTimeout(function () {
            button.textContent = originalText;
            button.classList.remove('active');
        }, 2000);
    }

    /**
     * Inicialização quando o documento estiver pronto
     */
    $(document).ready(function () {
        // Adiciona event listeners para todos os botões de copiar
        $('.lknwp-radio-copy-btn').on('click', function (e) {
            e.preventDefault();

            // Obtém o código do elemento code anterior ao botão
            var codeElement = $(this).siblings('code').first();
            if (codeElement.length) {
                var textToCopy = codeElement.text();
                copyToClipboard(textToCopy, this);
            }
        });

        // Adiciona tooltips aos botões (opcional)
        $('.lknwp-radio-copy-btn').attr('title', 'Clique para copiar o shortcode');

        // Log para debug
        console.log('LKN Radio Browser Admin Help Page carregada');
    });

})(jQuery);