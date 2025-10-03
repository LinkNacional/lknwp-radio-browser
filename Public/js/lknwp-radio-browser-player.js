document.addEventListener("DOMContentLoaded", function () {
    // Detectar se é um reload/refresh da página
    var isPageReload = (performance.navigation && performance.navigation.type === 1) ||
        (performance.getEntriesByType("navigation")[0] &&
            performance.getEntriesByType("navigation")[0].type === "reload");

    if (isPageReload) {
        console.log('LKNWP Radio: Reload detectado, aguardando limpeza...');
        // Pequeno delay para garantir limpeza completa
        setTimeout(function () {
            initializePlayer();
        }, 300);
    } else {
        console.log('LKNWP Radio: Primeira carga, inicializando imediatamente');
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
        var performanceMode = false; // Pode ser ativado via console: lknwpRadioPerformanceMode = true
        window.lknwpRadioPerformanceMode = false;

        /**
         * Cria elemento de áudio proxy para captura de dados
         */
        function createProxyAudioElement() {
            if (proxyElement) return proxyElement;

            proxyElement = document.createElement('audio');
            proxyElement.crossOrigin = 'anonymous';
            proxyElement.volume = 0.01; // Volume muito baixo, mas não zero para permitir captura
            proxyElement.preload = 'auto'; // Mudança: forçar carregamento
            proxyElement.controls = false;

            // URL do proxy para contornar CORS
            var proxyUrl = '/wp-json/lknwp-radio/v1/proxy-stream?url=' + encodeURIComponent(streamUrl);
            proxyElement.src = proxyUrl;

            console.log('LKNWP Radio: Elemento proxy criado com URL:', proxyUrl);

            // Adicionar listeners para debug
            proxyElement.addEventListener('loadstart', function () {
                console.log('LKNWP Radio: Proxy iniciou carregamento');
            });

            proxyElement.addEventListener('loadedmetadata', function () {
                console.log('LKNWP Radio: Proxy carregou metadados');
            });

            proxyElement.addEventListener('canplay', function () {
                console.log('LKNWP Radio: Proxy pode reproduzir');
            });

            proxyElement.addEventListener('error', function (e) {
                console.error('LKNWP Radio: Erro no proxy:', e);
            });

            // Adicionar ao DOM para garantir funcionamento
            proxyElement.style.display = 'none';
            document.body.appendChild(proxyElement);

            return proxyElement;
        }

        /**
         * Inicializa captura de áudio real
         */
        function initializeAudioContext() {
            if (audioContext && audioContext.state !== 'closed') {
                // Se já existe, apenas garante que está rodando
                if (audioContext.state === 'suspended') {
                    audioContext.resume().then(function () {
                        console.log('LKNWP Radio: AudioContext resumido');
                    });
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

                console.log('LKNWP Radio: AudioContext inicializado, state:', audioContext.state, 'bufferLength:', bufferLength);

                // Garantir que está rodando (necessário por política de navegador)
                if (audioContext.state === 'suspended') {
                    audioContext.resume().then(function () {
                        console.log('LKNWP Radio: AudioContext activated after user interaction');
                    });
                }

            } catch (error) {
                console.warn('LKNWP Radio: Erro ao criar AudioContext:', error);
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
                console.log('LKNWP Radio: Áudio já conectado ao analyser');
                return true;
            }

            try {
                source = audioContext.createMediaElementSource(proxyElement);
                source.connect(analyser);

                // CRÍTICO: Conectar o analyser ao destination para permitir reprodução
                // analyser.connect(audioContext.destination);

                console.log('LKNWP Radio: Áudio conectado ao analyser');
                return true;

            } catch (error) {
                console.error('LKNWP Radio: Erro ao conectar áudio:', error);
                return false;
            }
        }

        /**
         * Captura dados do áudio proxy
         */
        function captureFromProxyElement() {
            // Proteção extra contra estados inconsistentes após reload
            if (!proxyElement || !audioContext || !analyser || audioContext.state === 'closed') {
                if (debugCount < 3) {
                    console.log('LKNWP Radio: Elementos não disponíveis - proxy:', !!proxyElement,
                        'context:', !!audioContext, 'context.state:', audioContext ? audioContext.state : 'N/A',
                        'analyser:', !!analyser);
                    debugCount++;
                }
                return null;
            }

            if (audioContext.state !== 'running') {
                if (debugCount < 3) {
                    console.log('LKNWP Radio: AudioContext não está running:', audioContext.state);
                    debugCount++;
                }
                return null;
            }

            // Verificar se o proxy está realmente reproduzindo
            if (proxyElement.paused) {
                if (debugCount < 3) {
                    console.log('LKNWP Radio: Proxy está pausado');
                    debugCount++;
                }
                return null;
            }

            if (proxyElement.readyState < 3) {
                if (debugCount < 3) {
                    console.log('LKNWP Radio: Proxy readyState insuficiente:', proxyElement.readyState);
                    debugCount++;
                }
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

                // Log ocasional para debug
                if (Math.random() < 0.02) { // 2% das vezes
                    console.log('LKNWP Radio: Análise de dados - avg:', avgAmplitude.toFixed(2),
                        'max:', maxValue, 'nonZero:', nonZeroValues, 'total:', dataArray.length);
                }

                // Critério mais generoso para detectar áudio
                if (avgAmplitude > 0.1 || maxValue > 5 || nonZeroValues > 1) {
                    return dataArray;
                }

                return null;
            } catch (error) {
                console.warn('LKNWP Radio: Erro na captura de dados:', error);
                return null;
            }
        }    /**
     * Limpa timeouts e recursos - versão robusta para reloads
     */
        function cleanupResources() {
            console.log('LKNWP Radio: Iniciando limpeza robusta de recursos...');

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
                    audioContext.close().then(function () {
                        console.log('LKNWP Radio: AudioContext fechado com sucesso');
                    }).catch(function (error) {
                        console.log('LKNWP Radio: Erro ao fechar AudioContext:', error);
                    });
                    audioContext = null;
                }
            } catch (error) {
                console.log('LKNWP Radio: Erro durante limpeza de Web Audio:', error);
            }

            // Reset flags incluindo retry
            isInitialized = false;
            isVisualizerActive = false;
            stopRetrying = true; // Parar todos os retries ativos
            currentAnimationFunction = null; // Limpar referência da animação

            console.log('LKNWP Radio: Limpeza robusta concluída');
        }

        /**
         * Mostra o visualizador com dados reais
         */
        function showVisualizer() {
            var visualizerContainer = document.getElementById('lknwp-radio-audio-visualizer');
            if (!visualizerContainer) return;

            // Evitar chamadas múltiplas
            if (isVisualizerActive || isInitialized) {
                console.log('LKNWP Radio: Visualizador já ativo, ignorando chamada');
                return;
            }

            // Resetar flag de retry quando iniciar novo visualizador
            stopRetrying = false;

            // Limpar recursos anteriores
            cleanupResources();

            visualizerContainer.classList.add('lkp-audio-visualizer--active');
            isVisualizerActive = true;
            isInitialized = true;

            console.log('LKNWP Radio: Iniciando visualizador...');

            // Inicializar captura de áudio real
            if (initializeAudioContext()) {
                createProxyAudioElement();

                // Aguardar AudioContext estar realmente ativo
                var checkContextReady = function () {
                    if (audioContext && audioContext.state === 'running') {
                        console.log('LKNWP Radio: AudioContext está rodando, conectando áudio...');

                        if (connectAudioToAnalyser()) {
                            // Aguardar o proxy carregar antes de reproduzir
                            var proxyCanPlay = function () {
                                console.log('LKNWP Radio: Proxy carregado, iniciando reprodução para captura');

                                proxyElement.play().then(function () {
                                    console.log('LKNWP Radio: Proxy reproduzindo, iniciando visualizador real');

                                    // Aguardar mais tempo para o áudio se estabilizar
                                    var timeoutId = setTimeout(function () {
                                        createRealVisualizer();
                                    }, 2000); // 2 segundos para garantir
                                    timeoutIds.push(timeoutId);

                                }).catch(function (error) {
                                    console.error('LKNWP Radio: ERRO CRÍTICO - Proxy falhou:', error);
                                    console.log('LKNWP Radio: Sistema de retry automático irá tentar reconectar...');
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
                                    console.log('LKNWP Radio: Retry cancelado por solicitação global');
                                    return;
                                }

                                // Evitar tentativas simultâneas
                                if (isRetrying) {
                                    console.log('LKNWP Radio: Retry já em andamento, ignorando...');
                                    return;
                                } retryAttempts++;

                                // Verificar se realmente precisa de retry
                                if (proxyElement && !proxyElement.paused && proxyElement.readyState >= 3) {
                                    console.log('LKNWP Radio: Proxy carregou com sucesso, cancelando retry');
                                    return;
                                }

                                if (!proxyElement || (proxyElement.paused || proxyElement.readyState < 3)) {
                                    console.warn('LKNWP Radio: Tentativa', retryAttempts, 'de', maxRetries, '- Tentando reconectar proxy...');

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
                                                                console.log('LKNWP Radio: Play após load falhou:', error.name);
                                                            });
                                                        }
                                                    }, { once: true });
                                                }
                                            } catch (error) {
                                                console.log('LKNWP Radio: Erro durante retry seguro:', error.name);
                                            }

                                            isRetrying = false;
                                        }, 500); // Aguardar 500ms entre operações

                                        // Próxima tentativa em 4 segundos (mais tempo)
                                        var retryTimeoutId = setTimeout(attemptReconnect, 4000);
                                        timeoutIds.push(retryTimeoutId);
                                    } else {
                                        console.log('LKNWP Radio: Máximo de tentativas atingido, aguardando nova ação do usuário');
                                    }
                                } else {
                                    console.log('LKNWP Radio: Reconnect bem-sucedido na tentativa', retryAttempts);
                                }
                            }

                            // Iniciar sistema de retry após 8 segundos (mais tempo para carregar)
                            // Só se o player principal estiver tocando (evita retry quando usuário pausou)
                            var initialRetryId = setTimeout(function () {
                                if (isPlaying && proxyElement && (proxyElement.paused || proxyElement.readyState < 3)) {
                                    console.log('LKNWP Radio: Iniciando sistema de retry (player ativo)');
                                    attemptReconnect();
                                } else {
                                    console.log('LKNWP Radio: Retry cancelado - player pausado ou proxy OK');
                                }
                            }, 8000);
                            timeoutIds.push(initialRetryId);

                        } else {
                            console.error('LKNWP Radio: ERRO - Não foi possível conectar analyser');
                            console.log('LKNWP Radio: Sistema tentará reconectar automaticamente...');
                        }
                    } else {
                        console.log('LKNWP Radio: Aguardando AudioContext ativar...');
                        var contextTimeoutId = setTimeout(checkContextReady, 100);
                        timeoutIds.push(contextTimeoutId);
                    }
                };

                checkContextReady();

            } else {
                console.error('LKNWP Radio: ERRO - AudioContext não disponível');
                console.log('LKNWP Radio: Navegador não suporta Web Audio API ou está bloqueado');
            }
        }

        /**
         * Oculta o visualizador
         */
        function hideVisualizer() {
            var visualizerContainer = document.getElementById('lknwp-radio-audio-visualizer');
            if (!visualizerContainer) return;

            console.log('LKNWP Radio: Ocultando visualizador e parando retry automático');

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

            console.log('LKNWP Radio: Visualizador parado e recursos limpos');
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
                        var sum = Array.from(frequencies).reduce((a, b) => a + b, 0);
                        var avgAmplitude = sum / frequencies.length;
                        var fps = window.lknwpRadioPerformanceMode ? '20fps (performance mode)' : '30fps (normal)';
                        console.log('LKNWP Radio: Onda simétrica - amplitude média:', avgAmplitude.toFixed(2), fps);

                        if (performance.memory) {
                            var memMB = Math.round(performance.memory.usedJSHeapSize / 1048576);
                            console.log('LKNWP Radio: Memória JS:', memMB, 'MB');
                        }
                    }

                } else {
                    noDataCount++;

                    // Se não conseguir dados por muito tempo, tentar reconectar
                    if (noDataCount > maxNoDataAttempts) {
                        console.warn('LKNWP Radio: Sem dados por muito tempo, tentando reconectar...');
                        console.log('LKNWP Radio: Proxy status - paused:', proxyElement ? proxyElement.paused : 'N/A',
                            'readyState:', proxyElement ? proxyElement.readyState : 'N/A');

                        // Tentar reconectar proxy somente se o player principal estiver tocando
                        if (isPlaying && proxyElement && proxyElement.paused) {
                            console.log('LKNWP Radio: Tentando reativar proxy pausado...');
                            proxyElement.play().catch(function (error) {
                                console.log('LKNWP Radio: Reativação do proxy falhou:', error.name);
                            });
                        } else if (!isPlaying) {
                            console.log('LKNWP Radio: Player pausado, não tentando reconectar proxy');
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
            console.log('LKNWP Radio: Iniciando visualizador com dados reais');
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
                console.log('LKNWP Radio: Reativando animação existente');
                visualizerInterval = requestAnimationFrame(currentAnimationFunction);
            } else {
                console.log('LKNWP Radio: Estrutura não existe, recriando...');
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
                        errorMsg.innerHTML = "Não foi possível reproduzir esta rádio. Tente novamente mais tarde ou escolha outra estação.";
                        playBtn.parentNode.appendChild(errorMsg);
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
            console.log('LKNWP Volume: Mostrando tooltip');
            volumeValue.classList.remove("lkp-volume-display--hidden");
            volumeValue.classList.add("lkp-volume-display--visible");
            console.log('LKNWP Volume: Classes após mostrar:', volumeValue.classList.toString());

            clearTimeout(volumeTimeout);
            volumeTimeout = setTimeout(function () {
                console.log('LKNWP Volume: Escondendo tooltip após 20 segundos');
                volumeValue.classList.remove("lkp-volume-display--visible");
                volumeValue.classList.add("lkp-volume-display--hidden");
                console.log('LKNWP Volume: Classes após esconder:', volumeValue.classList.toString());
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
            var stationName = document.getElementById('lknwp-radio-station-name').textContent || 'Rádio Online';
            var shareText = `🎵 Escutando ${stationName} - `;

            // Botão Copiar Link
            var copyBtn = document.getElementById('lknwp-share-copy');
            if (copyBtn) {
                copyBtn.addEventListener('click', function () {
                    navigator.clipboard.writeText(currentUrl).then(function () {
                        console.log('LKNWP Radio: URL copiada para a área de transferência');
                        // Feedback visual
                        copyBtn.style.background = 'rgba(76, 175, 80, 0.3)';
                        setTimeout(function () {
                            copyBtn.style.background = '';
                        }, 1000);
                    }).catch(function (err) {
                        console.error('LKNWP Radio: Erro ao copiar URL:', err);
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

            console.log('LKNWP Radio: Botões de compartilhamento configurados');
        }

        // ===== VISUALIZER EVENT LISTENERS =====

        player.addEventListener('playing', function () {
            console.log('LKNWP Radio: Player começou a tocar, reativando visualizador...');

            setTimeout(function () {
                // Se já temos proxy e conexões, apenas reativar
                if (proxyElement && audioContext && analyser) {
                    console.log('LKNWP Radio: RESUME RÁPIDO - Reativando conexões existentes');

                    // Reativar retry se necessário
                    stopRetrying = false;

                    var visualizerContainer = document.getElementById('lknwp-radio-audio-visualizer');
                    if (visualizerContainer) {
                        visualizerContainer.classList.add('lkp-audio-visualizer--active');
                    }

                    // Reativar proxy
                    if (proxyElement.paused) {
                        proxyElement.play().catch(function (error) {
                            console.log('LKNWP Radio: Erro ao reativar proxy:', error.name);
                        });
                    }

                    // Reiniciar animação usando função dedicada
                    resumeVisualizer();
                } else {
                    // Se não temos conexões, criar do zero
                    console.log('LKNWP Radio: Criando novo visualizador');
                    showVisualizer();
                }
            }, 300); // Reduzido de 800ms para 300ms
        });

        player.addEventListener('pause', function () {
            console.log('LKNWP Radio: Player pausado, mantendo conexões para resume rápido...');

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
            console.log('LKNWP Radio: Limpeza completa antes de sair...');

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
                console.log('LKNWP Radio: Página oculta, reduzindo atividade...');
                // Não para completamente, mas pode ser útil para debugging
            }
        });

    } // Fim da função initializePlayer

});