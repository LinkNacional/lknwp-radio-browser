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
                tryWithoutCORS();
            });
        }

        /**
         * Teste 2: Sem crossOrigin (pode funcionar para captura básica)
         */
        function tryWithoutCORS() {
            proxyElement.removeAttribute('crossOrigin');
            proxyElement.src = streamUrl + '?t=' + Date.now(); // Cache bust

            proxyElement.addEventListener('error', function (e) {
                tryFetchBlob();
            });
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

            // Resetar flag de retry quando iniciar novo visualizador
            stopRetrying = false;

            // Limpar recursos anteriores
            cleanupResources();

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

            var topBars = [];
            var bottomBars = [];
            var numBars = 25;

            // Criar barras
            for (var i = 0; i < numBars; i++) {
                var topBar = document.createElement('div');
                topBar.className = 'lkp-visualizer-bar lkp-visualizer-bar--low';
                topBarsContainer.appendChild(topBar);
                topBars.push(topBar);

                var bottomBar = document.createElement('div');
                bottomBar.className = 'lkp-visualizer-bar lkp-visualizer-bar--low';
                bottomBarsContainer.appendChild(bottomBar);
                bottomBars.push(bottomBar);
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

                    var topBars = topBarsContainer.querySelectorAll('.lkp-visualizer-bar');
                    var bottomBars = bottomBarsContainer.querySelectorAll('.lkp-visualizer-bar');
                    var numBars = topBars.length;

                    if (numBars === 0) {
                        visualizerInterval = requestAnimationFrame(animateWithRealData);
                        return;
                    }

                    // EFEITO ONDA SIMÉTRICA - DO CENTRO PARA AS BORDAS
                    var center = Math.floor(numBars / 2);

                    // Cache para heights calculados (evita recálculo e DOM access repetido)
                    var heights = [];

                    for (var i = 0; i < numBars; i++) {
                        var distanceFromCenter = Math.abs(i - center);
                        var freqIndex = Math.floor((distanceFromCenter / center) * frequencies.length * 0.8);
                        var amplitude = frequencies[freqIndex] || 0;
                        var normalizedAmplitude = amplitude / 255;
                        var sensitivity = 6.0 - (distanceFromCenter / center) * 2.0;
                        var height = Math.max(15, Math.sqrt(normalizedAmplitude) * sensitivity * 65 + 15);

                        if (distanceFromCenter <= 2) {
                            height += 8;
                        }

                        var waveEffect = Math.sin((distanceFromCenter / center) * Math.PI) * 3;
                        height += waveEffect;

                        if (height < 15) height = 15;
                        if (height > 80) height = 80;

                        heights[i] = Math.floor(height) + 'px';
                    }

                    // Aplicar todas as mudanças de uma vez (batch DOM updates)
                    for (var i = 0; i < numBars; i++) {
                        topBars[i].style.height = heights[i];
                        bottomBars[i].style.height = heights[i];

                        // Classes baseadas na altura E posição
                        var heightNum = parseInt(heights[i]);
                        var distanceFromCenter = Math.abs(i - center);
                        var levelClass;
                        if (distanceFromCenter <= 3) {
                            levelClass = heightNum > 30 ? 'high' : heightNum > 20 ? 'medium' : 'low';
                        } else {
                            levelClass = heightNum > 35 ? 'high' : heightNum > 25 ? 'medium' : 'low';
                        }
                        var newClassName = 'lkp-visualizer-bar lkp-visualizer-bar--' + levelClass;

                        if (topBars[i].className !== newClassName) {
                            topBars[i].className = newClassName;
                            bottomBars[i].className = newClassName;
                        }
                    }

                    // Log ocasional simplificado
                    if (frameSkipCounter % 600 === 0) {
                        // Onda simétrica desenhada com sucesso
                    }

                } else {
                    noDataCount++;

                    // Se não conseguir dados por muito tempo, tentar reconectar
                    if (noDataCount > maxNoDataAttempts) {

                        // Tentar reconectar proxy somente se o player principal estiver tocando
                        if (isPlaying && proxyElement && proxyElement.paused) {
                            // Tentando reativar proxy pausado
                            proxyElement.play().catch(function (error) {
                                // Reativação do proxy falhou
                            });
                        } else if (!isPlaying) {
                            // Player pausado - não tentando reconectar proxy
                        }

                        noDataCount = Math.floor(maxNoDataAttempts * 0.7); // Reset parcial para evitar loop
                    } else {
                        // Manter barras baixas enquanto aguarda dados
                        var topBars = topBarsContainer.querySelectorAll('.lkp-visualizer-bar');
                        var bottomBars = bottomBarsContainer.querySelectorAll('.lkp-visualizer-bar');
                        for (var k = 0; k < topBars.length; k++) {
                            topBars[k].style.height = '15px';
                            bottomBars[k].style.height = '15px';
                            topBars[k].className = 'lkp-visualizer-bar lkp-visualizer-bar--low';
                            bottomBars[k].className = 'lkp-visualizer-bar lkp-visualizer-bar--low';
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
                playIcon.innerHTML = "<svg width=\'120\' height=\'120\' viewBox=\'0 0 48 48\' fill=\'none\' xmlns=\'http://www.w3.org/2000/svg\'><circle cx=\'24\' cy=\'24\' r=\'24\' fill=\'#fff\'/><polygon points=\'18,15 36,24 18,33\' fill=\'#424242\'/></svg>";
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
                playIcon.innerHTML = "<svg width=\'120\' height=\'120\' viewBox=\'0 0 48 48\' fill=\'none\' xmlns=\'http://www.w3.org/2000/svg\'><circle cx=\'24\' cy=\'24\' r=\'24\' fill=\'#fff\'/><rect x=\'16\' y=\'15\' width=\'6\' height=\'18\' rx=\'2\' fill=\'#424242\'/><rect x=\'26\' y=\'15\' width=\'6\' height=\'18\' rx=\'2\' fill=\'#424242\'/></svg>";
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
        volumeSlider.addEventListener("input", function () {
            player.volume = parseFloat(this.value);
            volumeValue.textContent = Math.round(this.value * 100) + "%";
            updateVolumeValuePosition();
            showVolumeValue();
        });
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
            setTimeout(function () {
                // Se já temos proxy e conexões, apenas reativar
                if (proxyElement && audioContext && analyser) {
                    // Reativar retry se necessário
                    stopRetrying = false;

                    var visualizerContainer = document.getElementById('lknwp-radio-audio-visualizer');
                    if (visualizerContainer) {
                        visualizerContainer.classList.add('lkp-audio-visualizer--active');
                    }

                    // Reativar proxy
                    if (proxyElement.paused) {
                        proxyElement.play().catch(function (error) {
                        });
                    }

                    // Reiniciar animação usando função dedicada
                    resumeVisualizer();
                } else {
                    // Se não temos conexões, criar do zero
                    showVisualizer();
                }
            }, 300); // Reduzido de 800ms para 300ms
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