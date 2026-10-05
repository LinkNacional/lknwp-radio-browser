document.addEventListener("DOMContentLoaded", function () {
    // Detectar se é um reload/refresh da página
    var isPageReload = (performance.navigation && performance.navigation.type === 1) ||
        (performance.getEntriesByType("navigation")[0] &&
            performance.getEntriesByType("navigation")[0].type === "reload");

    if (isPageReload) {
        // Pequeno delay para garantir limpeza completa
        setTimeout(function () {
            initializePlayer();
        }, 300);
    } else {
        initializePlayer();
    }

    function initializePlayer() {
        var player = document.getElementById("lknwp-radio-player");
        var playBtn = document.getElementById("lknwp-radio-play-btn");
        var playIcon = document.getElementById("lknwp-radio-play-icon");
        var volumeSlider = document.getElementById("lknwp-radio-volume");
        var volumeValue = document.getElementById("lknwp-radio-volume-value");
        var isPlaying = false;
        var streamUrl = player.getAttribute("src");
        var hlsInstance = null;
        player.volume = 0.2;

        // Proxy same-origin (admin-ajax) do áudio. O visualizador usa a Web Audio
        // API, que só consegue LER o áudio se ele for legível pelo navegador. Quando
        // a rádio cross-origin não responde com cabeçalhos CORS, o áudio fica opaco e
        // as waves não se mexem; este proxy entrega os bytes no mesmo domínio do site.
        var streamProxyCfg = window.lknwpRadioTextsPlayer || {};
        var streamProxyUrl = "";
        if (streamUrl && streamProxyCfg.ajaxUrl && streamProxyCfg.streamProxyNonce) {
            streamProxyUrl = streamProxyCfg.ajaxUrl
                + "?action=lknwp_radio_stream"
                + "&nonce=" + encodeURIComponent(streamProxyCfg.streamProxyNonce)
                + "&stream=" + encodeURIComponent(streamUrl);
        }

        // Variáveis do Visualizer Real
        var isVisualizerActive = false;
        var visualizerInterval = null;
        var audioContext = null;
        var analyser = null;
        var source = null;
        var dataArray = null;
        var proxyElement = null;
        var debugCount = 0; // Contador para logs de debug
        var timeoutIds = []; // Array para controlar timeouts
        var isInitialized = false; // Prevenir múltiplas inicializações
        var stopRetrying = false; // Flag global para parar todos os retries
        var currentAnimationFunction = null; // Referência para a função de animação ativa

        // Detecta HLS (.m3u8)
        if (streamUrl && streamUrl.endsWith(".m3u8")) {
            if (window.Hls && window.Hls.isSupported()) {
                hlsInstance = new Hls();
                hlsInstance.loadSource(streamUrl);
                hlsInstance.attachMedia(player);
            } else if (player.canPlayType("application/vnd.apple.mpegurl")) {
                player.src = streamUrl;
            }
        }

        // ===== VISUALIZER COM ÁUDIO REAL =====

        // Performance monitoring
        var performanceMode = false;
        window.lknwpRadioPerformanceMode = false;

        /**
         * Recarrega stream usando acesso direto com cache busting
         */
        function reloadProxyWithNextChunk() {
            if (!proxyElement) return;

            // Para stream direto, simplesmente recarregamos com timestamp novo
            var timestamp = Date.now();
            var newStreamUrl = streamUrl + (streamUrl.includes('?') ? '&' : '?') + '_t=' + timestamp;

            // Criar elemento buffer para transição suave
            var bufferElement = document.createElement('audio');
            bufferElement.volume = 0.01;
            bufferElement.preload = 'auto';

            // Tratamento de erro para fallback
            bufferElement.addEventListener('error', function (e) {
                // Fallback: recarregar elemento atual
                setTimeout(function () {
                    if (proxyElement) {
                        proxyElement.src = newStreamUrl;
                        proxyElement.load();
                        if (isPlaying) {
                            proxyElement.play().catch(function () { });
                        }
                    }
                }, 500);
            });

            // Configurar stream direto no buffer
            bufferElement.src = newStreamUrl;

            // Quando buffer estiver pronto, fazer transição suave
            bufferElement.addEventListener('canplay', function () {
                // Trocar elementos - buffer vira principal
                var oldElement = proxyElement;
                proxyElement = bufferElement;

                // Conectar novo elemento ao analyser
                if (source && audioContext) {
                    try {
                        source.disconnect();
                        source = audioContext.createMediaElementSource(proxyElement);
                        source.connect(analyser);
                        analyser.connect(audioContext.destination);
                    } catch (e) {
                        // Erro silencioso no analyser
                    }
                }

                // Reproduzir novo chunk se necessário
                if (isPlaying) {
                    proxyElement.play().catch(function (e) {
                        // Erro silencioso na reprodução
                    });
                }

                // Reset das flags após transição bem-sucedida
                reloadInProgress = false;

                // Limpar elemento antigo após pequeno delay
                setTimeout(function () {
                    if (oldElement.parentNode) {
                        oldElement.parentNode.removeChild(oldElement);
                    }
                }, 1000);
            });

            // Adicionar buffer ao DOM
            bufferElement.style.display = 'none';
            document.body.appendChild(bufferElement);
            return proxyElement;
        }

        /**
         * Configura acesso direto ao stream usando várias estratégias
         */
        function setupDirectStreamAccess() {
            // Estratégia 1: Teste direto simples
            testDirectAccess();
        }

        /**
         * Teste 1: Acesso direto simples
         */
        function testDirectAccess() {
            proxyElement.crossOrigin = 'anonymous';
            proxyElement.src = streamUrl;

            proxyElement.addEventListener('error', function (e) {
                // Rádio sem CORS: tenta o proxy same-origin antes de cair no no-cors.
                tryProxyStream();
            }, { once: true });
        }

        /**
         * Acesso via proxy same-origin (PHP). Contorna a ausência de CORS da rádio,
         * permitindo ao AnalyserNode ler o áudio e desenhar as waves.
         */
        function tryProxyStream() {
            if (!streamProxyUrl) {
                tryWithoutCORS();
                return;
            }

            proxyElement.removeAttribute('crossOrigin');
            proxyElement.src = streamProxyUrl;

            proxyElement.addEventListener('error', function (e) {
                tryWithoutCORS();
            }, { once: true });
        }

        /**
         * Teste 2: Sem crossOrigin (pode funcionar para captura básica)
         */
        function tryWithoutCORS() {
            proxyElement.removeAttribute('crossOrigin');
            proxyElement.src = streamUrl + '?t=' + Date.now(); // Cache bust

            proxyElement.addEventListener('error', function (e) {
                tryFetchBlob();
            }, { once: true });
        }

        /**
         * Teste 3: Fetch + Blob (para chunks menores)
         */
        function tryFetchBlob() {
            fetch(streamUrl, {
                method: 'GET',
                mode: 'no-cors', // Tenta contornar CORS
                headers: {
                    'Range': 'bytes=0-262143'
                }
            })
                .then(response => {
                    if (response.ok || response.type === 'opaque') {
                        return response.blob();
                    }
                    throw new Error('Fetch failed');
                })
                .then(blob => {
                    const audioUrl = URL.createObjectURL(blob);
                    proxyElement.src = audioUrl;

                    // Limpar URL após uso
                    proxyElement.addEventListener('loadend', function () {
                        URL.revokeObjectURL(audioUrl);
                    });
                })
                .catch(error => {
                    tryMediaSource();
                });
        }

        /**
         * Teste 4: Media Source Extensions (mais avançado)
         */
        function tryMediaSource() {
            if (!window.MediaSource) {
                useFallback();
                return;
            }

            const mediaSource = new MediaSource();
            proxyElement.src = URL.createObjectURL(mediaSource);

            mediaSource.addEventListener('sourceopen', function () {
                try {
                    const sourceBuffer = mediaSource.addSourceBuffer('audio/mpeg');

                    // Tentar fetch com no-cors para alimentar o buffer
                    fetch(streamUrl, {
                        method: 'GET',
                        mode: 'no-cors',
                        headers: { 'Range': 'bytes=0-262143' }
                    })
                        .then(response => response.arrayBuffer())
                        .then(data => {
                            sourceBuffer.appendBuffer(data);
                        })
                        .catch(error => {
                            useFallback();
                        });
                } catch (e) {
                    useFallback();
                }
            });
        }

        /**
         * Fallback: Usar stream direto mesmo com limitações
         */
        function useFallback() {
            // Remove crossOrigin para máxima compatibilidade
            proxyElement.removeAttribute('crossOrigin');
            proxyElement.src = streamUrl;
        }

        /**
         * Cria elemento de áudio usando JavaScript direto (sem proxy REST)
         */
        function createProxyAudioElement() {
            if (proxyElement) return proxyElement;

            proxyElement = document.createElement('audio');
            proxyElement.volume = 0.01;
            proxyElement.preload = 'auto';
            proxyElement.controls = false;

            // Tentar várias abordagens para contornar CORS
            setupDirectStreamAccess();

            return proxyElement;

            return proxyElement;
        }

        /**
         * Inicializa captura de áudio real
         */
        function initializeAudioContext() {
            if (audioContext && audioContext.state !== 'closed') {
                // Se já existe, apenas garante que está rodando
                if (audioContext.state === 'suspended') {
                    audioContext.resume();
                }
                return true;
            }

            try {
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
                analyser = audioContext.createAnalyser();
                analyser.fftSize = 128; // Menor para performance
                analyser.smoothingTimeConstant = 0.8;

                var bufferLength = analyser.frequencyBinCount;
                dataArray = new Uint8Array(bufferLength);

                // Garantir que está rodando (necessário por política de navegador)
                if (audioContext.state === 'suspended') {
                    audioContext.resume();
                }

            } catch (error) {
                return false;
            }

            return true;
        }

        /**
         * Conecta o elemento de áudio ao analyser
         */
        function connectAudioToAnalyser() {
            if (!audioContext || !analyser || !proxyElement) return false;

            // Se já existe conexão, não precisa recriar
            if (source) {
                return true;
            }

            try {
                source = audioContext.createMediaElementSource(proxyElement);
                source.connect(analyser);

                // CRÍTICO: Conectar o analyser ao destination para permitir reprodução
                // analyser.connect(audioContext.destination);

                return true;

            } catch (error) {
                return false;
            }
        }

        /**
         * Captura dados do áudio proxy
         */
        function captureFromProxyElement() {
            // Proteção extra contra estados inconsistentes após reload
            if (!proxyElement || !audioContext || !analyser || audioContext.state === 'closed') {
                return null;
            }

            if (audioContext.state !== 'running') {
                return null;
            }

            // Verificar se o proxy está realmente reproduzindo
            if (proxyElement.paused) {
                return null;
            }

            if (proxyElement.readyState < 3) {
                return null;
            }

            try {
                analyser.getByteFrequencyData(dataArray);

                // Verificar se há realmente áudio
                var sum = 0;
                var maxValue = 0;
                var nonZeroValues = 0;

                for (var i = 0; i < dataArray.length; i++) {
                    var value = dataArray[i];
                    sum += value;
                    if (value > maxValue) maxValue = value;
                    if (value > 0) nonZeroValues++;
                }

                var avgAmplitude = sum / dataArray.length;

                // Critério mais generoso para detectar áudio
                if (avgAmplitude > 0.1 || maxValue > 5 || nonZeroValues > 1) {
                    return dataArray;
                }

                return null;
            } catch (error) {
                return null;
            }
        }    /**
     * Limpa timeouts e recursos - versão robusta para reloads
     */
        function cleanupResources() {

            // Limpar todos os timeouts
            timeoutIds.forEach(function (id) {
                clearTimeout(id);
            });
            timeoutIds = [];

            // Parar animação
            if (visualizerInterval) {
                cancelAnimationFrame(visualizerInterval);
                visualizerInterval = null;
            }

            // Desconectar e fechar Web Audio API
            try {
                if (source) {
                    source.disconnect();
                    source = null;
                }
                if (analyser) {
                    analyser.disconnect();
                    analyser = null;
                }
                if (audioContext && audioContext.state !== 'closed') {
                    audioContext.close();
                    audioContext = null;
                }
            } catch (error) {
                // Erro durante limpeza - ignorar silenciosamente
            }

            // Reset flags incluindo retry
            isInitialized = false;
            isVisualizerActive = false;
            stopRetrying = true; // Parar todos os retries ativos
            currentAnimationFunction = null; // Limpar referência da animação

            // Limpeza robusta concluída
        }

        /**
         * Mostra o visualizador com dados reais
         */
        function showVisualizer() {
            var visualizerContainer = document.getElementById('lknwp-radio-audio-visualizer');
            if (!visualizerContainer) return;

            // Evitar chamadas múltiplas
            if (isVisualizerActive || isInitialized) {
                return;
            }

            // Limpar recursos anteriores (cleanupResources liga stopRetrying = true)
            cleanupResources();

            // Resetar flag de retry DEPOIS da limpeza (senão a limpeza a reativa p/ true)
            stopRetrying = false;

            visualizerContainer.classList.add('lkp-audio-visualizer--active');
            isVisualizerActive = true;
            isInitialized = true;

            // Iniciando visualizador

            // Inicializar captura de áudio real
            if (initializeAudioContext()) {
                createProxyAudioElement();

                // Aguardar AudioContext estar realmente ativo
                var checkContextReady = function () {
                    if (audioContext && audioContext.state === 'running') {
                        if (connectAudioToAnalyser()) {
                            // Aguardar o proxy carregar antes de reproduzir
                            var proxyCanPlay = function () {
                                proxyElement.play().then(function () {
                                    // Aguardar mais tempo para o áudio se estabilizar
                                    var timeoutId = setTimeout(function () {
                                        createRealVisualizer();
                                    }, 2000); // 2 segundos para garantir
                                    timeoutIds.push(timeoutId);

                                }).catch(function (error) {
                                    // Proxy falhou - sistema de retry irá tentar reconectar silenciosamente
                                });
                            };

                            // Se já pode reproduzir
                            if (proxyElement.readyState >= 3) {
                                proxyCanPlay();
                            } else {
                                // Aguardar evento canplay
                                proxyElement.addEventListener('canplay', proxyCanPlay, { once: true });

                                // Forçar carregamento
                                proxyElement.load();
                            }

                            // Sistema de retry inteligente - evita loops e conflitos
                            var retryAttempts = 0;
                            var maxRetries = 5; // Reduzido para 5 tentativas
                            var isRetrying = false; // Flag para evitar múltiplas tentativas simultâneas

                            function attemptReconnect() {
                                // Parar se foi solicitado globalmente
                                if (stopRetrying) {
                                    return;
                                }

                                // Evitar tentativas simultâneas
                                if (isRetrying) {
                                    return;
                                } retryAttempts++;

                                // Verificar se realmente precisa de retry
                                if (proxyElement && !proxyElement.paused && proxyElement.readyState >= 3) {
                                    return;
                                }

                                if (!proxyElement || (proxyElement.paused || proxyElement.readyState < 3)) {

                                    if (retryAttempts <= maxRetries && proxyElement) {
                                        isRetrying = true;

                                        // Aguardar um pouco antes de tentar para evitar conflitos
                                        setTimeout(function () {
                                            try {
                                                if (proxyElement && proxyElement.readyState < 3) {
                                                    proxyElement.load();

                                                    // Aguardar load completar antes do play
                                                    proxyElement.addEventListener('canplay', function () {
                                                        if (proxyElement && proxyElement.paused) {
                                                            proxyElement.play().catch(function (error) {
                                                                // Play falhou silenciosamente
                                                            });
                                                        }
                                                    }, { once: true });
                                                }
                                            } catch (error) {
                                                // Erro durante retry - ignorar silenciosamente
                                            }

                                            isRetrying = false;
                                        }, 500); // Aguardar 500ms entre operações

                                        // Próxima tentativa em 4 segundos (mais tempo)
                                        var retryTimeoutId = setTimeout(attemptReconnect, 4000);
                                        timeoutIds.push(retryTimeoutId);
                                    } else {
                                        // Máximo de tentativas atingido - aguardando nova ação do usuário
                                    }
                                } else {
                                    // Reconnect bem-sucedido
                                }
                            }

                            // Iniciar sistema de retry após 8 segundos (mais tempo para carregar)
                            // Só se o player principal estiver tocando (evita retry quando usuário pausou)
                            var initialRetryId = setTimeout(function () {
                                if (isPlaying && proxyElement && (proxyElement.paused || proxyElement.readyState < 3)) {
                                    attemptReconnect();
                                } else {
                                    // Retry cancelado - player pausado ou proxy OK
                                }
                            }, 8000);
                            timeoutIds.push(initialRetryId);

                        } else {
                            // Erro - não foi possível conectar analyser
                        }
                    } else {
                        var contextTimeoutId = setTimeout(checkContextReady, 100);
                        timeoutIds.push(contextTimeoutId);
                    }
                };

                checkContextReady();

            } else {
                // AudioContext não disponível - navegador não suporta Web Audio API
            }
        }

        /**
         * Oculta o visualizador
         */
        function hideVisualizer() {
            var visualizerContainer = document.getElementById('lknwp-radio-audio-visualizer');
            if (!visualizerContainer) return;

            // Ocultando visualizador e parando retry automático

            visualizerContainer.classList.remove('lkp-audio-visualizer--active');
            isVisualizerActive = false;
            isInitialized = false; // Permitir nova inicialização

            // Limpeza completa incluindo retry flags
            cleanupResources();

            // Para elemento proxy
            if (proxyElement) {
                proxyElement.pause();
                // Remover do DOM para liberar memória
                if (proxyElement.parentNode) {
                    proxyElement.parentNode.removeChild(proxyElement);
                }
                proxyElement = null;
            }

            // Visualizador parado e recursos limpos
        }

        /**
         * Visualizador com dados reais do áudio
         */
        function createRealVisualizer() {
            var topContainer = document.getElementById('lknwp-radio-visualizer-top');
            var bottomContainer = document.getElementById('lknwp-radio-visualizer-bottom');

            if (!topContainer || !bottomContainer) return;

            // Criar estrutura de barras
            topContainer.innerHTML = '<div class="lkp-visualizer-bars"></div>';
            bottomContainer.innerHTML = '<div class="lkp-visualizer-bars"></div>';

            var topBarsContainer = topContainer.querySelector('.lkp-visualizer-bars');
            var bottomBarsContainer = bottomContainer.querySelector('.lkp-visualizer-bars');

            // Quantidade de barras: sempre PAR (a onda fica simétrica em relação
            // ao centro — ver cálculo de `center` no loop de animação).
            var numBars = 20;
            // Altura máxima da barra = altura real da metade do visualizador
            // (assim funciona tanto no layout base 145px quanto no v2 90px).
            var maxBarHeight = topContainer.clientHeight || 145;

            // Criar barras grossas (onda) e reflexo espelhado (embaixo)
            for (var i = 0; i < numBars; i++) {
                var topBar = document.createElement('div');
                topBar.className = 'lkp-visualizer-bar lkp-visualizer-bar--low';
                topBarsContainer.appendChild(topBar);

                var bottomBar = document.createElement('div');
                bottomBar.className = 'lkp-visualizer-bar lkp-visualizer-bar--low';
                bottomBarsContainer.appendChild(bottomBar);
            }

            // Estado por barra (altura atual) para suavizar a onda
            var currentHeights = [];
            for (var j = 0; j < numBars; j++) {
                currentHeights.push(6);
            }

            var noDataCount = 0;
            var maxNoDataAttempts = 100; // ~5 segundos sem dados antes de retry (menos agressivo)
            var frameSkipCounter = 0; // Contador para pular frames e melhorar performance
            function animateWithRealData() {
                if (!isVisualizerActive) return;

                // Performance mode dinâmico
                var skipRate = window.lknwpRadioPerformanceMode ? 3 : 2;

                // Otimização: pular alguns frames para melhorar performance
                frameSkipCounter++;
                if (frameSkipCounter % skipRate !== 0) {
                    visualizerInterval = requestAnimationFrame(animateWithRealData);
                    return;
                }

                var frequencies = captureFromProxyElement();

                if (frequencies && frequencies.length > 0) {
                    noDataCount = 0; // Reset contador quando há dados

                    var topBarsEls = topBarsContainer.querySelectorAll('.lkp-visualizer-bar');
                    var bottomBarsEls = bottomBarsContainer.querySelectorAll('.lkp-visualizer-bar');
                    var count = topBarsEls.length;

                    if (count === 0) {
                        visualizerInterval = requestAnimationFrame(animateWithRealData);
                        return;
                    }

                    // ONDA: graves no centro, agudos nas bordas.
                    // Centro geométrico — ex.: 20 barras → 9.5, então as duas
                    // barras do meio ficam equidistantes e a onda sai simétrica
                    // (com Math.floor, o pico ficava 1 barra fora do centro).
                    var center = Math.max(0.5, (count - 1) / 2);

                    for (var i = 0; i < count; i++) {
                        var distanceFromCenter = Math.abs(i - center);
                        var freqIndex = Math.floor((distanceFromCenter / center) * frequencies.length * 0.75);
                        var amplitude = frequencies[freqIndex] || 0;
                        var normalized = amplitude / 255;
                        // Sensibilidade maior no centro (graves) e curva sqrt (mais natural)
                        var sensitivity = 5.5 - (distanceFromCenter / center) * 1.8;
                        var target = Math.max(6, Math.sqrt(normalized) * sensitivity * (maxBarHeight * 0.9));

                        // Suavização (lerp): sobe rápido, desce mais devagar
                        var ease = target > currentHeights[i] ? 0.5 : 0.18;
                        currentHeights[i] += (target - currentHeights[i]) * ease;
                        var h = Math.max(6, Math.min(maxBarHeight, currentHeights[i]));
                        var hPx = h.toFixed(1) + 'px';

                        topBarsEls[i].style.height = hPx;
                        bottomBarsEls[i].style.height = hPx;

                        // Cor por intensidade
                        var levelClass = h > maxBarHeight * 0.66 ? 'high' : (h > maxBarHeight * 0.33 ? 'medium' : 'low');
                        var newClassName = 'lkp-visualizer-bar lkp-visualizer-bar--' + levelClass;
                        if (topBarsEls[i].className !== newClassName) {
                            topBarsEls[i].className = newClassName;
                            bottomBarsEls[i].className = newClassName;
                        }
                    }

                } else {
                    noDataCount++;

                    // Auto-reparo: se o AudioContext foi suspenso (ex.: segundo plano),
                    // tenta reativá-lo e retomar o proxy para as waves não travarem.
                    if (audioContext && audioContext.state === 'suspended') {
                        audioContext.resume().catch(function () { });
                    }

                    // Se não conseguir dados por muito tempo, tentar reconectar
                    if (noDataCount > maxNoDataAttempts) {

                        // Tentar reconectar proxy somente se o player principal estiver tocando
                        if (isPlaying && proxyElement) {
                            if (proxyElement.ended || proxyElement.error) {
                                // Stream do proxy caiu: recarrega e retoma
                                try { proxyElement.load(); } catch (e) { }
                                proxyElement.play().catch(function (error) { });
                            } else if (proxyElement.paused) {
                                // Tentando reativar proxy pausado
                                proxyElement.play().catch(function (error) { });
                            }
                        } else if (!isPlaying) {
                            // Player pausado - não tentando reconectar proxy
                        }

                        noDataCount = Math.floor(maxNoDataAttempts * 0.7); // Reset parcial para evitar loop
                    } else {
                        // Manter barras baixas enquanto aguarda dados
                        var idleTopBars = topBarsContainer.querySelectorAll('.lkp-visualizer-bar');
                        var idleBottomBars = bottomBarsContainer.querySelectorAll('.lkp-visualizer-bar');
                        for (var k = 0; k < idleTopBars.length; k++) {
                            idleTopBars[k].style.height = '6px';
                            idleBottomBars[k].style.height = '6px';
                            idleTopBars[k].className = 'lkp-visualizer-bar lkp-visualizer-bar--low';
                            idleBottomBars[k].className = 'lkp-visualizer-bar lkp-visualizer-bar--low';
                            if (currentHeights[k] !== undefined) { currentHeights[k] = 6; }
                        }
                    }
                }

                visualizerInterval = requestAnimationFrame(animateWithRealData);
            }

            // Salvar referência global da função de animação
            currentAnimationFunction = animateWithRealData;

            // Iniciar animação com dados reais
            // Iniciando visualizador com dados reais
            visualizerInterval = requestAnimationFrame(animateWithRealData);
        }

        // Função para reativar animação existente sem recriar tudo
        function resumeVisualizer() {
            // Chrome suspende o AudioContext após longos períodos/em segundo plano.
            // Sem isto, captureFromProxyElement() retorna null e as waves "morrem".
            if (audioContext && audioContext.state === 'suspended') {
                audioContext.resume().catch(function () { });
            }

            if (!isVisualizerActive) {
                isVisualizerActive = true;
            }

            if (visualizerInterval) {
                cancelAnimationFrame(visualizerInterval);
            }

            // Reiniciar animação se temos as estruturas
            var topBarsContainer = document.querySelector('#lknwp-radio-visualizer-top .lkp-visualizer-bars');
            if (topBarsContainer && topBarsContainer.children.length > 0 && currentAnimationFunction) {
                // Reativando animação existente
                visualizerInterval = requestAnimationFrame(currentAnimationFunction);
            } else {
                // Estrutura não existe - recriando
                createRealVisualizer();
            }
        }

        /**
         * O proxy/AudioContext estão irrecuperáveis? (stream caiu ou contexto fechado)
         * Não usa readyState aqui: logo após o play o readyState pode ser baixo, e o
         * proxy estava pausado (por causa do pause) - isso NÃO significa morto.
         */
        function isProxyDead() {
            return !proxyElement || !audioContext || !analyser ||
                audioContext.state === 'closed' ||
                !!proxyElement.error ||
                proxyElement.ended;
        }

        // ===== PLAYER CONTROLS =====

        playBtn.addEventListener("click", function () {
            // Adicionar animação de clique
            playBtn.classList.add('lkp-play-btn--clicked');
            playBtn.classList.add('lkp-play-btn--loading');

            setTimeout(function () {
                playBtn.classList.remove('lkp-play-btn--clicked');
            }, 600);

            setTimeout(function () {
                playBtn.classList.remove('lkp-play-btn--loading');
            }, 1000);

            if (isPlaying) {
                player.pause();
                playBtn.classList.remove('lkp-play-btn--playing');
                var imgParent = document.getElementById("lknwp-radio-img-parent");
                if (imgParent) {
                    imgParent.classList.remove("lknwp-radio-shake")
                }
                playIcon.innerHTML = "<svg viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg' aria-hidden='true'><path d='M8 5v14l11-7z' fill='#232b36'/></svg>";
            } else {
                playBtn.classList.add('lkp-play-btn--playing');

                // Visualizador será inicializado no showVisualizer()

                // Sempre coloca no momento mais recente do stream antes de dar play
                let seeked = false;
                if (player.seekable && player.seekable.length > 0) {
                    var latest = player.seekable.end(player.seekable.length - 1);
                    if (isFinite(latest)) {
                        player.currentTime = latest;
                        seeked = true;
                    }
                }
                if (!seeked) {
                    player.load(); // força atualização do buffer
                }
                player.play().catch(function () {
                    playBtn.classList.remove('lkp-play-btn--loading');
                    playBtn.classList.remove('lkp-play-btn--playing');
                    var errorMsg = document.getElementById("lknwp-radio-player-error");
                    if (!errorMsg) {
                        errorMsg = document.createElement("div");
                        errorMsg.id = "lknwp-radio-player-error";
                        errorMsg.className = "lkp-player-error";
                        errorMsg.innerHTML = lknwpRadioTextsPlayer.unableToPlay || "Unable to play this radio station. Please try again later or choose another station.";
                        playBtn.parentNode.appendChild(errorMsg);
                    }
                    // Esconder o componente de compartilhamento
                    var shareSection = document.querySelector('.lkp-share-section');
                    if (shareSection) {
                        shareSection.style.display = 'none';
                    }
                    // Simular clique no botão play/pause para garantir atualização do estado e animações
                    if (playBtn) {
                        playBtn.click();
                    }
                });
                var imgParent = document.getElementById("lknwp-radio-img-parent");
                if (imgParent) {
                    imgParent.classList.add("lknwp-radio-shake")
                }
                playIcon.innerHTML = "<svg viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg' aria-hidden='true'><rect x='7' y='5' width='4' height='14' rx='1.2' fill='#232b36'/><rect x='13' y='5' width='4' height='14' rx='1.2' fill='#232b36'/></svg>";
            }
            isPlaying = !isPlaying;
        });
        function updateVolumeValuePosition() {
            var min = parseFloat(volumeSlider.min);
            var max = parseFloat(volumeSlider.max);
            var val = parseFloat(volumeSlider.value);
            var percent = (val - min) / (max - min);
            var sliderWidth = volumeSlider.offsetWidth;
            var thumbWidth = 20; // Ajustado para melhor posicionamento
            var left = percent * (sliderWidth - thumbWidth) + thumbWidth / 2;
            volumeValue.style.left = left + "px";
            volumeValue.style.transform = "translateX(-50%)";
            // Remove qualquer display inline que possa interferir
            volumeValue.style.display = "";
        }
        var volumeTimeout;
        function showVolumeValue() {
            volumeValue.classList.remove("lkp-volume-display--hidden");
            volumeValue.classList.add("lkp-volume-display--visible");

            clearTimeout(volumeTimeout);
            volumeTimeout = setTimeout(function () {
                volumeValue.classList.remove("lkp-volume-display--visible");
                volumeValue.classList.add("lkp-volume-display--hidden");
            }, 2000);
        }
        volumeSlider.addEventListener('input', function () {
            player.volume = parseFloat(this.value);
            volumeValue.textContent = Math.round(this.value * 100) + "%";
            updateVolumeValuePosition();
            showVolumeValue();
        });

        // ===== BOTÃO DE MUTE =====
        var muteBtn = document.getElementById('lknwp-radio-mute-btn');
        if (muteBtn) {
            // Estado inicial conforme o áudio
            muteBtn.classList.toggle('is-muted', !!player.muted);
            muteBtn.setAttribute('aria-pressed', player.muted ? 'true' : 'false');

            muteBtn.addEventListener('click', function () {
                player.muted = !player.muted;
                this.classList.toggle('is-muted', player.muted);
                this.setAttribute('aria-pressed', player.muted ? 'true' : 'false');
                this.setAttribute('aria-label', player.muted
                    ? (lknwpRadioTextsPlayer.unmute || 'Desmutar')
                    : (lknwpRadioTextsPlayer.mute || 'Mutar'));
            });

            // Reflete mudanças de volume/mute vindas de outros lugares
            player.addEventListener('volumechange', function () {
                muteBtn.classList.toggle('is-muted', !!player.muted);
                muteBtn.setAttribute('aria-pressed', player.muted ? 'true' : 'false');
            });
        }
        volumeSlider.addEventListener("mousedown", showVolumeValue);
        volumeSlider.addEventListener("touchstart", showVolumeValue);
        // Inicializa posição e esconde
        updateVolumeValuePosition();
        volumeValue.classList.add("lkp-volume-display--hidden");
        window.addEventListener("resize", updateVolumeValuePosition);

        // ===== CONFIGURAR BOTÕES DE COMPARTILHAMENTO =====
        setupShareButtons();

        function setupShareButtons() {
            var currentUrl = window.location.href;
            var stationName = document.getElementById('lknwp-radio-station-name').textContent || (lknwpRadioTextsPlayer.onlineRadio || 'Online Radio');
            var shareTextTemplate = lknwpRadioTextsPlayer.listeningTo || '🎵 Listening to {station} - ';
            var shareText = shareTextTemplate.replace('{station}', stationName);

            // Botão Copiar Link
            var copyBtn = document.getElementById('lknwp-share-copy');
            if (copyBtn) {
                copyBtn.addEventListener('click', function () {
                    navigator.clipboard.writeText(currentUrl).then(function () {
                        // Feedback visual
                        copyBtn.style.background = 'rgba(76, 175, 80, 0.3)';
                        setTimeout(function () {
                            copyBtn.style.background = '';
                        }, 1000);
                    }).catch(function (err) {
                    });
                });
            }

            // Instagram Stories
            var instaBtn = document.getElementById('lknwp-share-instagram');
            if (instaBtn) {
                instaBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    // Instagram não tem API oficial de compartilhamento, então abrimos o app
                    var instagramUrl = `instagram://story-camera`;
                    window.open(instagramUrl, '_blank');
                });
            }

            // WhatsApp
            var whatsappBtn = document.getElementById('lknwp-share-whatsapp');
            if (whatsappBtn) {
                whatsappBtn.href = `https://wa.me/?text=${encodeURIComponent(shareText + currentUrl)}`;
                whatsappBtn.target = '_blank';
                whatsappBtn.rel = 'noopener noreferrer';
            }

            // Twitter
            var twitterBtn = document.getElementById('lknwp-share-twitter');
            if (twitterBtn) {
                twitterBtn.href = `https://twitter.com/intent/tweet?text=${encodeURIComponent(shareText)}&url=${encodeURIComponent(currentUrl)}`;
                twitterBtn.target = '_blank';
                twitterBtn.rel = 'noopener noreferrer';
            }
        }

        // ===== VISUALIZER EVENT LISTENERS =====

        player.addEventListener('playing', function () {
            // Continue ouvindo: registra a estação atual como recente (localStorage)
            try {
                var nameEl = document.getElementById('lknwp-radio-station-name');
                var imgEl = document.querySelector('.lkp-station-img');
                var stName = nameEl ? nameEl.textContent.trim() : '';
                var stImg = imgEl ? (imgEl.getAttribute('src') || '') : '';
                if (stName) {
                    var RECENT_KEY = 'lknwp_recent_stations';
                    var list = [];
                    try { list = JSON.parse(localStorage.getItem(RECENT_KEY) || '[]'); } catch (e) { list = []; }
                    if (!Array.isArray(list)) list = [];
                    var id = stName.toLowerCase();
                    var prev = null;
                    list = list.filter(function (s) {
                        if ((s.name || s.url || '').toString().trim().toLowerCase() === id) { prev = s; return false; }
                        return true;
                    });
                    list.unshift({
                        uuid: (prev && prev.uuid) || '',
                        name: stName,
                        img: stImg,
                        url: window.location.href,
                        genre: (prev && prev.genre) || '',
                        country: (prev && prev.country) || '',
                        cc: (prev && prev.cc) || '',
                        song: (prev && prev.song) || '',
                        artist: (prev && prev.artist) || ''
                    });
                    localStorage.setItem(RECENT_KEY, JSON.stringify(list.slice(0, 12)));
                }
            } catch (e) { }

            // Atualiza a música atual no registro de recentes (Continue ouvindo).
            // O player-song.js preenche #lknwp-radio-current-song de forma assíncrona.
            if (!window.__lknwpSongObserver) {
                var songEl = document.getElementById('lknwp-radio-current-song');
                if (songEl && window.MutationObserver) {
                    window.__lknwpSongObserver = new MutationObserver(function () {
                        var title = (songEl.textContent || '').trim();
                        if (!title) return;
                        var artistEl = document.getElementById('lknwp-radio-artist');
                        var artist = artistEl ? (artistEl.textContent || '').trim() : '';
                        try {
                            var RK = 'lknwp_recent_stations';
                            var rl = JSON.parse(localStorage.getItem(RK) || '[]');
                            if (!Array.isArray(rl)) rl = [];
                            var nameEl2 = document.getElementById('lknwp-radio-station-name');
                            var nm = nameEl2 ? nameEl2.textContent.trim().toLowerCase() : '';
                            rl = rl.map(function (s) {
                                if ((s.name || '').toString().trim().toLowerCase() === nm) {
                                    s.song = title; s.artist = artist;
                                }
                                return s;
                            });
                            localStorage.setItem(RK, JSON.stringify(rl.slice(0, 12)));
                        } catch (e) { }
                    });
                    window.__lknwpSongObserver.observe(songEl, { childList: true, characterData: true, subtree: true });
                }
            }

            var playingTimeoutId = setTimeout(function () {
                // Se ainda temos proxy/contexto utilizáveis, apenas reativar
                if (!isProxyDead()) {
                    // Reativar retry se necessário
                    stopRetrying = false;

                    var visualizerContainer = document.getElementById('lknwp-radio-audio-visualizer');
                    if (visualizerContainer) {
                        visualizerContainer.classList.add('lkp-audio-visualizer--active');
                    }

                    // Garantir que o AudioContext voltou a rodar (pode ter sido suspenso)
                    if (audioContext.state === 'suspended') {
                        audioContext.resume().catch(function () { });
                    }

                    // Garantir que o proxy está tocando (pode estar pausado após o pause)
                    if (proxyElement.paused) {
                        proxyElement.play().catch(function () { });
                    }

                    // Reiniciar animação usando função dedicada
                    resumeVisualizer();
                } else {
                    // Proxy morto/caiu ou contexto fechado após muito tempo: reconstruir do zero
                    hideVisualizer();
                    showVisualizer();
                }
            }, 300);
            timeoutIds.push(playingTimeoutId);
        });

        player.addEventListener('pause', function () {
            // Ocultar visualização mas manter estruturas
            var visualizerContainer = document.getElementById('lknwp-radio-audio-visualizer');
            if (visualizerContainer) {
                visualizerContainer.classList.remove('lkp-audio-visualizer--active');
            }

            // Pausar animação mas manter conexões
            if (visualizerInterval) {
                cancelAnimationFrame(visualizerInterval);
                visualizerInterval = null;
            }

            // Pausar proxy mas não destruir (manter para resume)
            if (proxyElement && !proxyElement.paused) {
                proxyElement.pause();
            }

            // Manter isVisualizerActive = true para resume rápido
            // Só parar retry automático temporariamente
            stopRetrying = true;
        });

        player.addEventListener('ended', function () {
            hideVisualizer();
        });

        player.addEventListener('error', function (e) {
            hideVisualizer();
        });

        // Limpeza automática quando o usuário sai da página
        window.addEventListener('beforeunload', function () {
            // Parar player principal
            if (player && !player.paused) {
                player.pause();
            }

            // Limpar HLS se existir
            if (hlsInstance) {
                hlsInstance.destroy();
                hlsInstance = null;
            }

            // Fechar AudioContext completamente
            if (audioContext && audioContext.state !== 'closed') {
                audioContext.close();
                audioContext = null;
            }

            // Limpar proxy element
            if (proxyElement) {
                proxyElement.pause();
                if (proxyElement.parentNode) {
                    proxyElement.parentNode.removeChild(proxyElement);
                }
                proxyElement = null;
            }

            // Parar visualizer e limpar recursos
            hideVisualizer();
            cleanupResources();

            // Resetar flags
            isPlaying = false;
            isVisualizerActive = false;
            isInitialized = false;
        });

        // Limpeza quando a página perde foco (optional - pode ajudar em alguns casos)
        document.addEventListener('visibilitychange', function () {
            if (document.hidden && isPlaying) {
            }
        });

    } // Fim da função initializePlayer

});
/* ==========================================================================
   PLAYER v2 — "Continue ouvindo" (lê as rádios recentes do localStorage).
   Cada card linka para a página do player daquela rádio.
   ========================================================================== */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var section = document.getElementById('lkp_continue');
        var track = document.getElementById('lkp_continue_track');
        if (!section || !track) return;

        var pluginUrl = '';
        var hidden = document.getElementById('lknwp_radio_browser_plugin_url');
        if (hidden && hidden.value) {
            try { pluginUrl = atob(hidden.value); } catch (e) { pluginUrl = ''; }
        }
        var fallbackImg = pluginUrl ? (pluginUrl + 'Includes/assets/images/default-radio.png') : '';

        function esc(s) {
            return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
            });
        }

        var list = [];
        try { list = JSON.parse(localStorage.getItem('lknwp_recent_stations') || '[]'); } catch (e) { list = []; }
        if (!Array.isArray(list) || !list.length) return;

        var html = '';
        list.forEach(function (s) {
            if (!s || (!s.name && !s.url)) return;
            var img = s.img || fallbackImg;
            var name = s.name || '';
            html += '<a class="lkp-v2__card" href="' + esc(s.url) + '" title="' + esc(name) + '">' +
                '<span class="lkp-v2__card-cover"><img src="' + esc(img) + '" alt="' + esc(name) + '" loading="lazy"></span>' +
                '<span class="lkp-v2__card-name">' + esc(name) + '</span>' +
            '</a>';
        });

        if (!html) return;

        track.innerHTML = html;
        track.querySelectorAll('.lkp-v2__card-cover img').forEach(function (el) {
            el.addEventListener('error', function () {
                if (fallbackImg && this.src !== fallbackImg) { this.src = fallbackImg; }
            });
        });

        // Botões de navegação (anterior/próxima) pelas rádios já ouvidas.
        // O loop é circular: na primeira, "anterior" leva para a última.
        var nav = document.getElementById('lkp_continue_nav');
        var prevBtn = document.getElementById('lkp_nav_prev');
        var nextBtn = document.getElementById('lkp_nav_next');

        if (nav && prevBtn && nextBtn && list.length >= 2) {
            // Localiza a rádio atual na lista (pelo nome exibido; cai no índice 0).
            var stationNameEl = document.getElementById('lknwp-radio-station-name');
            var currentName = stationNameEl ? stationNameEl.textContent.trim().toLowerCase() : '';
            var currentIndex = 0;
            for (var i = 0; i < list.length; i++) {
                var nm = (list[i] && list[i].name ? String(list[i].name) : '').trim().toLowerCase();
                if (currentName && nm === currentName) { currentIndex = i; break; }
            }

            var go = function (delta) {
                var n = list.length;
                if (n < 2) { return; }
                var target = list[(currentIndex + delta + n) % n];
                if (target && target.url) { window.location.href = target.url; }
            };

            prevBtn.addEventListener('click', function () { go(-1); });
            nextBtn.addEventListener('click', function () { go(1); });
            nav.removeAttribute('hidden');
        }

        section.removeAttribute('hidden');
    });
})();

/* ==========================================================================
   PLAYER v2 — botão "Favoritar" (persiste em localStorage 'lknwp_favs').
   A chave é o uuid da rádio, ou 'name:<nome>' quando não houver uuid
   (mesmo padrão usado na lista).
   ========================================================================== */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('lkp_fav_btn');
        if (!btn) return;

        var uuid = btn.getAttribute('data-uuid') || '';
        var name = (btn.getAttribute('data-name') || '').toLowerCase();
        var key = uuid || ('name:' + name);

        function readFavs() {
            try { return JSON.parse(localStorage.getItem('lknwp_favs') || '[]'); } catch (e) { return []; }
        }

        function sync() {
            var active = readFavs().indexOf(key) !== -1;
            btn.classList.toggle('is-active', active);
            btn.setAttribute('aria-pressed', active ? 'true' : 'false');
        }

        btn.addEventListener('click', function () {
            var favs = readFavs();
            var idx = favs.indexOf(key);
            if (idx === -1) { favs.push(key); } else { favs.splice(idx, 1); }
            try { localStorage.setItem('lknwp_favs', JSON.stringify(favs)); } catch (e) {}
            sync();
        });

        sync();
    });
})();

/* ==========================================================================
   TEMA (dark/light) — botões com persistência compartilhada em localStorage.
   A aplicação antecipada (evitar flash) é feita por um <script> inline no template.
   Suporta mais de um botão (ex.: lista + player na mesma página) sem duplicar bind.
   ========================================================================== */
(function () {
    function initThemeToggle() {
        var root = document.documentElement;
        var btns = document.querySelectorAll('[data-lknwp-theme-toggle]');
        if (!btns.length) return;

        function current() {
            return root.getAttribute('data-lknwp-theme') === 'light' ? 'light' : 'dark';
        }

        function apply(theme) {
            root.setAttribute('data-lknwp-theme', theme);
            try { localStorage.setItem('lknwp_theme', theme); } catch (e) {}
            for (var i = 0; i < btns.length; i++) {
                btns[i].setAttribute('aria-pressed', theme === 'light' ? 'true' : 'false');
            }
        }

        var saved = 'dark';
        try { saved = localStorage.getItem('lknwp_theme') || 'dark'; } catch (e) {}
        apply(saved === 'light' ? 'light' : 'dark');

        for (var i = 0; i < btns.length; i++) {
            var b = btns[i];
            if (b.getAttribute('data-lknwp-theme-bound')) continue;
            b.setAttribute('data-lknwp-theme-bound', '1');
            b.addEventListener('click', function () {
                apply(current() === 'light' ? 'dark' : 'light');
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initThemeToggle);
    } else {
        initThemeToggle();
    }
})();
