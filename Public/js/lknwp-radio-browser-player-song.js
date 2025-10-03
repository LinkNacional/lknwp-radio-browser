document.addEventListener('DOMContentLoaded', function () {
    var player = document.getElementById('lknwp-radio-player');
    var streamUrl = player.getAttribute('src');
    var lastSong = '';
    var baseUrl = '';
    var workingMethod = null; // 'icecast-json', 'icecast-html', 'shoutcast-json', 'shoutcast-html'
    var methodUrl = null; // URL específica que funcionou
    var currentRequest = null; // Controla a requisição atual
    var timeoutId = null; // Controla o timeout
    var totalAttempts = 0; // Contador total de tentativas
    var maxTotalAttempts = 2; // Limite máximo de tentativas - apenas 2 rodadas
    var failedMethods = []; // Array para controlar métodos que já falharam nesta descoberta
    var isDiscovering = false; // Controla se já está descobrindo para evitar múltiplas chamadas
    var directMethodFound = false; // Flag para priorizar métodos diretos
    var retryCount = 0; // Contador de tentativas de retry do método atual
    var maxRetries = 1; // Máximo de retries por método

    // Mixed Content warning
    if (window.location.protocol === 'https:' && streamUrl && streamUrl.startsWith('http:')) {
        var warningDiv = document.createElement('div');
        warningDiv.style = 'background:#ffeaea;color:#d00;padding:12px;border-radius:8px;margin-bottom:16px;border:1px solid #d00;text-align:center;font-weight:600;';
        warningDiv.innerHTML = lknwpRadioTextsSong ? lknwpRadioTextsSong.warning : 'Warning: This radio uses insecure streaming (HTTP) and cannot be played on HTTPS pages. Ask the provider to enable HTTPS or access via HTTP.';
        var playerBlock = document.getElementById('lknwp-radio-custom-player');
        if (playerBlock) {
            playerBlock.parentNode.insertBefore(warningDiv, playerBlock);
        } else {
            document.body.insertBefore(warningDiv, document.body.firstChild);
        }
        return;
    }

    // Função para decodificar entidades HTML
    function decodeHtmlEntities(text) {
        if (!text) return text;

        var entityMap = {
            '&apos;': "'",
            '&#39;': "'",
            '&quot;': '"',
            '&#34;': '"',
            '&amp;': '&',
            '&#38;': '&',
            '&lt;': '<',
            '&#60;': '<',
            '&gt;': '>',
            '&#62;': '>',
            '&nbsp;': ' ',
            '&#160;': ' '
        };

        return text.replace(/&[a-zA-Z0-9#]+;/g, function (entity) {
            return entityMap[entity] || entity;
        });
    }

    function setSongInfo(musica, artista) {
        var songDiv = document.getElementById('lknwp-radio-current-song');
        var artistDiv = document.getElementById('lknwp-radio-artist');
        var albumDiv = document.getElementById('lknwp-radio-album-img');
        var songBlockDiv = document.getElementById('lknwp-radio-current-song-block');
        var statsDiv = document.getElementById('lknwp-radio-station-stats');

        // Decodifica entidades HTML
        musica = decodeHtmlEntities(musica);
        artista = decodeHtmlEntities(artista);

        if (musica === lastSong) return;
        lastSong = musica;
        if (musica) {
            songDiv.textContent = musica;

            // Montar nome do artista com estatísticas da rádio
            var artistaComStats = artista;
            if (window.LKNWP_STATION_CLICKCOUNT > 0 || window.LKNWP_STATION_VOTES > 0) {
                var statsText = '';
                if (window.LKNWP_STATION_CLICKCOUNT > 0) {
                    statsText = formatNumber(window.LKNWP_STATION_CLICKCOUNT) + ' ' + (lknwpRadioTextsSong ? lknwpRadioTextsSong.listeners : 'listeners');
                } else if (window.LKNWP_STATION_VOTES > 0) {
                    statsText = formatNumber(window.LKNWP_STATION_VOTES) + ' ' + (lknwpRadioTextsSong ? lknwpRadioTextsSong.likes : 'likes');
                }

                if (artista && artista.trim()) {
                    artistaComStats = artista + ' - ' + statsText;
                } else {
                    artistaComStats = statsText;
                }
            }

            // Definir texto do artista (será sobrescrito pelo iTunes se encontrar)
            artistDiv.textContent = artistaComStats;

            // Ocultar elemento de estatísticas separado (não usado mais)
            if (statsDiv) {
                statsDiv.style.display = 'none';
            }

            // Buscar arte do álbum (passando artista original, não com stats)
            fetchAlbumArt(musica, artista, artistaComStats);

            // Restaurar a classe quando encontrar informações
            if (songBlockDiv && !songBlockDiv.classList.contains('lkp-current-song-block')) {
                songBlockDiv.classList.add('lkp-current-song-block');
            }
        } else {
            songDiv.textContent = '';
            artistDiv.textContent = '';
            // Ocultar estatísticas quando não há música
            if (statsDiv) {
                statsDiv.style.display = 'none';
            }
        }
    }

    // Função auxiliar para formatar números grandes
    function formatNumber(num) {
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        } else if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'k';
        }
        return num.toString();
    }

    // Função para buscar arte do álbum no iTunes
    function fetchAlbumArt(musica, artista, artistaComStats) {
        var albumDiv = document.getElementById('lknwp-radio-album-img');
        var artistDiv = document.getElementById('lknwp-radio-artist');

        if (!albumDiv || !artistDiv) return;

        if (musica) {
            fetch('https://itunes.apple.com/search?term=' + encodeURIComponent(artista + ' ' + musica) + '&entity=song&limit=1')
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.results && data.results[0]) {
                        if (data.results[0].artworkUrl100) {
                            var imgUrl = data.results[0].artworkUrl100.replace(/100x100bb.jpg$/, '600x600bb.jpg');
                            albumDiv.innerHTML = '<img src="' + imgUrl + '" alt="Album" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">';
                            albumDiv.style.display = 'block';
                        } else {
                            albumDiv.innerHTML = '';
                            albumDiv.style.display = 'none';
                        }

                        // Se encontrou dados do iTunes, usa o nome oficial + estatísticas
                        if (data.results[0].artistName) {
                            var iTunesArtist = data.results[0].artistName;

                            // Reconstrói com artista do iTunes + estatísticas
                            var artistaFinal = iTunesArtist;
                            if (window.LKNWP_STATION_CLICKCOUNT > 0 || window.LKNWP_STATION_VOTES > 0) {
                                var statsText = '';
                                if (window.LKNWP_STATION_CLICKCOUNT > 0) {
                                    statsText = formatNumber(window.LKNWP_STATION_CLICKCOUNT) + ' ' + (lknwpRadioTextsSong ? lknwpRadioTextsSong.listeners : 'listeners');
                                } else if (window.LKNWP_STATION_VOTES > 0) {
                                    statsText = formatNumber(window.LKNWP_STATION_VOTES) + ' ' + (lknwpRadioTextsSong ? lknwpRadioTextsSong.likes : 'likes');
                                }
                                artistaFinal = iTunesArtist + ' - ' + statsText;
                            }

                            artistDiv.textContent = artistaFinal;
                        }
                        // Se não encontrou artista no iTunes, mantém o que já estava (artistaComStats)
                    } else {
                        albumDiv.innerHTML = '';
                        albumDiv.style.display = 'none';
                    }
                })
                .catch(function () {
                    albumDiv.innerHTML = '';
                    albumDiv.style.display = 'none';
                });
        } else {
            albumDiv.innerHTML = '';
            albumDiv.style.display = 'none';
        }
    } function fetchWithTimeout(url, timeoutMs) {
        return new Promise(function (resolve, reject) {
            var controller = new AbortController();
            var signal = controller.signal;

            var timeoutId = setTimeout(function () {
                controller.abort();
                reject(new Error('Timeout de ' + timeoutMs + 'ms excedido'));
            }, timeoutMs);

            // Configurações para melhor compatibilidade
            var fetchOptions = {
                signal: signal,
                method: 'GET',
                mode: 'cors',
                cache: 'no-cache',
                headers: {
                    'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,application/json,*/*;q=0.8',
                    'Accept-Language': 'pt-BR,pt;q=0.9,en;q=0.8',
                    'Accept-Encoding': 'gzip, deflate',
                    'User-Agent': 'Mozilla/5.0 (compatible; RadioPlayer/1.0)'
                }
            };



            fetch(url, fetchOptions)
                .then(function (response) {
                    clearTimeout(timeoutId);

                    resolve(response);
                })
                .catch(function (error) {
                    clearTimeout(timeoutId);


                    // Tenta uma abordagem alternativa se o CORS falhar
                    if (error.message.includes('CORS') || error.message.includes('Failed to fetch')) {


                        // Tenta sem CORS (modo no-cors)
                        var noCorsOptions = {
                            signal: signal,
                            method: 'GET',
                            mode: 'no-cors',
                            cache: 'no-cache'
                        };

                        fetch(url, noCorsOptions)
                            .then(function (response) {
                                resolve(response);
                            })
                            .catch(function (altError) {
                                reject(error); // Rejeita com o erro original
                            });
                    } else {
                        reject(error);
                    }
                });
        });
    }

    var discoveryFinished = false; // Flag para garantir que só exibe a mensagem uma vez

    function shouldStopTrying() {
        if (totalAttempts >= maxTotalAttempts) {
            if (!discoveryFinished) {
                discoveryFinished = true;
                var songDiv = document.getElementById('lknwp-radio-current-song');
                var artistDiv = document.getElementById('lknwp-radio-artist');
                var albumDiv = document.getElementById('lknwp-radio-album-img');
                var songBlockDiv = document.getElementById('lknwp-radio-current-song-block');
                // Simplesmente ocultar sem mostrar mensagem de erro
                songDiv.textContent = '';
                artistDiv.textContent = '';
                albumDiv.innerHTML = '';
                albumDiv.style.display = 'none';
                // Remover a classe para eliminar margins
                if (songBlockDiv) {
                    songBlockDiv.classList.remove('lkp-current-song-block');
                }
            }
            return true;
        }
        return false;
    }

    function resetCounters() {
        totalAttempts = 0;
        failedMethods = [];
        isDiscovering = false;
        discoveryFinished = false; // Reset da flag quando encontra música
        retryCount = 0; // Reset do contador de retry
    }

    function getNextMethodToTry() {
        var methods = ['icecast-json', 'icecast-html', 'shoutcast-html', 'shoutcast-json'];
        for (var i = 0; i < methods.length; i++) {
            if (failedMethods.indexOf(methods[i]) === -1) {
                return methods[i];
            }
        }
        return null; // Todos os métodos falharam
    }

    function markMethodAsFailed(method) {
        if (failedMethods.indexOf(method) === -1) {
            failedMethods.push(method);
        }
    }

    function isAudioComponent(response, content) {
        // Verifica se é componente de áudio pelo Content-Type
        var contentType = response.headers.get('content-type') || '';
        if (contentType.includes('audio/') || contentType.includes('application/octet-stream')) {
            return true;
        }

        if (typeof content === 'string') {
            var trimmedContent = content.trim();

            // Verifica se o conteúdo é muito pequeno (indicativo de redirect ou erro)
            if (trimmedContent.length < 100) {
                return true;
            }

            // Se é XML/HTML válido, não é áudio
            if (trimmedContent.startsWith('<?xml') ||
                trimmedContent.startsWith('<!DOCTYPE') ||
                trimmedContent.startsWith('<html')) {
                return false;
            }

            // Verifica se contém caracteres não-textuais (dados binários/hexadecimais)
            var binaryPattern = /[\x00-\x08\x0E-\x1F\x7F-\xFF]/;
            if (binaryPattern.test(trimmedContent)) {
                return true;
            }

            // Verifica se o conteúdo parece ser código hexadecimal
            var hexPattern = /^[0-9A-Fa-f\s]{50,}/;
            if (hexPattern.test(trimmedContent)) {
                return true;
            }

            // Verifica se não contém estrutura HTML básica esperada
            // Mas só considera áudio se também não tiver elementos de dados úteis
            var hasBasicHtml = trimmedContent.includes('<html') ||
                trimmedContent.includes('<td>') ||
                trimmedContent.includes('<table>') ||
                trimmedContent.includes('<!DOCTYPE') ||
                trimmedContent.includes('<body');

            var hasUsefulData = trimmedContent.includes('title') ||
                trimmedContent.includes('song') ||
                trimmedContent.includes('current') ||
                trimmedContent.includes('playing') ||
                trimmedContent.includes('icestats') ||
                trimmedContent.includes('{') ||
                trimmedContent.includes('[');

            if (!hasBasicHtml && !hasUsefulData) {
                return true;
            }

            // Verifica se o conteúdo não é JSON válido quando esperado
            if (!trimmedContent.startsWith('{') && !trimmedContent.startsWith('[') &&
                !trimmedContent.includes('<')) {
                return true;
            }
        }

        return false;
    }

    function isTemporaryError(error) {
        return error.message.includes('Failed to fetch') ||
            error.message.includes('NETWORK_ERROR') ||
            error.message.includes('CORS_BLOCKED') ||
            error.message.includes('timeout') ||
            error.message.includes('Timeout de') ||
            error.message.includes('AUDIO_STREAM') ||
            error.message.includes('TEXT_TIMEOUT') ||
            error.message.includes('aborted');
    }

    function tryOptimizedMethod() {
        // Verificação adicional: garante que todos os elementos DOM estão prontos
        var cardContainer = document.getElementById('lknwp-radio-custom-player');
        var songDiv = document.getElementById('lknwp-radio-current-song');
        var artistDiv = document.getElementById('lknwp-radio-artist');
        var albumDiv = document.getElementById('lknwp-radio-album-img');

        if (!cardContainer || !songDiv || !artistDiv || !albumDiv) {
            return;
        }

        if (workingMethod && methodUrl) {
            // Se é um método que funcionou como HTML mas era JSON, trata como HTML
            if (workingMethod.includes('-as-html')) {

                fetchWithTimeout(methodUrl, 10000)
                    .then(function (response) {
                        return response.text();
                    })
                    .then(function (html) {
                        var result = {
                            response: { headers: { get: function () { return 'text/html'; } } },
                            text: html
                        };
                        var success = tryParseHtmlData(result);

                        if (success.found && success.musica) {
                            resetCounters();
                            setSongInfo(success.musica, success.artista);
                        }
                        // NÃO reseta workingMethod/methodUrl - fica na URL que funcionou antes
                    })
                    .catch(function (error) {
                        // NÃO reseta workingMethod/methodUrl - continua tentando a mesma URL sempre
                    });

                return; // Sai para não executar o código dos outros métodos
            }

            // Se é um método com proxy, usa fetch simples
            if (workingMethod.includes('-proxy')) {
                fetch(methodUrl)
                    .then(function (response) {
                        if (!response.ok) throw new Error('Proxy status: ' + response.status);
                        return response.text();
                    })
                    .then(function (html) {
                        var success = false;

                        // Se é um método marcado como "-as-html-proxy", sempre processa como HTML
                        if (workingMethod.includes('-as-html-proxy')) {
                            var result = {
                                response: { headers: { get: function () { return 'text/html'; } } },
                                text: html
                            };
                            success = tryParseHtmlData(result);
                        } else {
                            var baseMethodType = workingMethod.replace('-proxy', '');

                            // Detecta se o conteúdo é realmente HTML mesmo que o método seja "json"
                            var isHtmlContent = html.trim().startsWith('<!DOCTYPE') || html.trim().startsWith('<html');

                            if (baseMethodType.includes('json') && !isHtmlContent) {
                                var result = {
                                    response: { headers: { get: function () { return 'application/json'; } } },
                                    text: html,
                                    method: { type: 'json', name: baseMethodType }
                                };
                                success = tryParseJsonData(result);
                            } else {
                                var result = {
                                    response: { headers: { get: function () { return 'text/html'; } } },
                                    text: html,
                                    method: { type: 'html', name: baseMethodType }
                                };
                                success = tryParseHtmlData(result);
                            }
                        }


                        if (success.found && success.musica) {
                            resetCounters();
                            setSongInfo(success.musica, success.artista);
                        } else {
                            // NÃO reseta workingMethod/methodUrl - continua na mesma URL
                        }
                    })
                    .catch(function (error) {
                        // NÃO reseta workingMethod/methodUrl - continua tentando a mesma URL sempre
                    });

                return; // Sai da função para não executar o código padrão
            }

            if (workingMethod === 'icecast-json' || workingMethod === 'shoutcast-json') {
                fetchWithTimeout(methodUrl, 10000)
                    .then(function (response) {
                        var contentType = response.headers.get('content-type') || '';
                        if (!contentType.includes('application/json')) {
                            throw new Error(lknwpRadioTextsSong ? lknwpRadioTextsSong.responseNotJson : 'Response is not JSON');
                        }
                        return response.json();
                    })
                    .then(function (data) {
                        var song = '';
                        if (data.icestats && data.icestats.source) {
                            var source = data.icestats.source;
                            if (Array.isArray(source)) {
                                for (var i = 0; i < source.length; i++) {
                                    if (source[i].title && source[i].title.trim()) {
                                        song = source[i].title;
                                        break;
                                    } else if (source[i].yp_currently_playing && source[i].yp_currently_playing.trim()) {
                                        song = source[i].yp_currently_playing;
                                        break;
                                    }
                                }
                            } else {
                                song = source.title || source.yp_currently_playing || '';
                            }
                        }

                        var artista = '';
                        var musica = song;
                        if (song && song.indexOf(' - ') > -1) {
                            var parts = song.split(' - ');
                            artista = parts[0];
                            musica = parts[1];
                        }

                        // Decodifica entidades HTML
                        musica = decodeHtmlEntities(musica);
                        artista = decodeHtmlEntities(artista);

                        if (typeof musica === 'string' && musica.trim().length > 0) {
                            resetCounters();
                            setSongInfo(musica, artista);
                        } else {
                            // NÃO reseta workingMethod/methodUrl - continua na mesma URL
                        }
                    })
                    .catch(function (error) {

                        if (isTemporaryError(error) && retryCount < maxRetries) {
                            retryCount++;
                            // Não reseta workingMethod/methodUrl, apenas tenta novamente
                            if (!shouldStopTrying()) {
                                setTimeout(function () { tryOptimizedMethod(); }, 2000);
                            }
                        } else {
                            // NÃO reseta workingMethod/methodUrl - continua tentando a mesma URL sempre
                        }
                    });
            } else if (workingMethod === 'icecast-html' || workingMethod === 'shoutcast-html') {
                fetchWithTimeout(methodUrl, 10000)
                    .then(function (response) {
                        if (!response.ok) throw new Error('HTML not available');
                        return response.text().then(function (text) {
                            return { response: response, text: text };
                        });
                    })
                    .then(function (result) {
                        var html = result.text;

                        // Verifica se é componente de áudio
                        if (isAudioComponent(result.response, html)) {
                            throw new Error(lknwpRadioTextsSong ? lknwpRadioTextsSong.audioComponent : 'Optimized method failed - response is audio component');
                        }
                        var musicaHtml = '';
                        var artistaHtml = '';
                        var match = null;

                        if (workingMethod === 'icecast-html') {
                            match = html.match(/<td>Current Song:<\/td><td class="streamdata">([^<]+)<\/td>/i)
                                || html.match(/<td>M[úu]sica Atual:<\/td><td class="streamdata">([^<]+)<\/td>/i)
                                || html.match(/<td>T[ií]tulo:<\/td><td class="streamdata">([^<]+)<\/td>/i);
                        } else {
                            match = html.match(/<td>Playing Now:\s*<\/td><td><b><a[^>]*>([^<]+)<\/a><\/b><\/td>/i)
                                || html.match(/<td>Playing Now: <\/td><td><b><a [^>]*>([^<]+)<\/a><\/b><\/td><\/tr>/i)
                                || html.match(/<td>Current Song:<\/td><td class="streamdata">([^<]+)<\/td>/i)
                                || html.match(/<td>M[úu]sica Atual:<\/td><td class="streamdata">([^<]+)<\/td>/i)
                                || html.match(/<td>T[ií]tulo:<\/td><td class="streamdata">([^<]+)<\/td>/i);
                        }

                        if (match && match[1] && match[1].trim()) {
                            musicaHtml = match[1].trim();
                            if (musicaHtml.indexOf(' - ') > -1) {
                                var parts = musicaHtml.split(' - ');
                                artistaHtml = parts[0];
                                musicaHtml = parts[1];
                            }

                            // Decodifica entidades HTML
                            musicaHtml = decodeHtmlEntities(musicaHtml);
                            artistaHtml = decodeHtmlEntities(artistaHtml);

                            resetCounters();
                            setSongInfo(musicaHtml, artistaHtml);
                        } else {
                            // NÃO reseta workingMethod/methodUrl - continua na mesma URL
                        }
                    })
                    .catch(function (error) {

                        if (isTemporaryError(error) && retryCount < maxRetries) {
                            retryCount++;
                            // Não reseta workingMethod/methodUrl, apenas tenta novamente
                            if (!shouldStopTrying()) {
                                setTimeout(function () { tryOptimizedMethod(); }, 2000);
                            }
                        } else {
                            // NÃO reseta workingMethod/methodUrl - continua tentando a mesma URL sempre
                        }
                    });
            }
            return;
        }

        // Se não tem método definido, faz a descoberta
        discoverWorkingMethod();
    }

    function discoverWorkingMethod() {
        if (shouldStopTrying() || discoveryFinished) return;

        // Variáveis de controle da descoberta
        var resolved = false; // Flag para evitar múltiplas resoluções
        var completedRequests = 0;

        // Verificação adicional: garante que todos os elementos DOM estão prontos
        var cardContainer = document.getElementById('lknwp-radio-custom-player');
        var songDiv = document.getElementById('lknwp-radio-current-song');
        var artistDiv = document.getElementById('lknwp-radio-artist');
        var albumDiv = document.getElementById('lknwp-radio-album-img');

        if (!cardContainer || !songDiv || !artistDiv || !albumDiv) {
            return;
        }

        // Evita múltiplas chamadas simultâneas
        if (isDiscovering) {
            return;
        }

        isDiscovering = true;
        totalAttempts++;

        // Reset das variáveis de controle para esta tentativa
        completedRequests = 0;
        resolved = false;


        // Definir função checkAllCompleted no escopo correto
        function checkAllCompleted() {
            // Se já foi resolvido ou descoberta finalizada, não faz nada
            if (resolved || discoveryFinished) {
                return;
            }

            completedRequests++;

            // Se ainda não resolveu e todas as 4 requisições foram completadas
            if (!resolved && completedRequests >= 4 && !discoveryFinished) {
                // Limpa o timeout forçado já que completou naturalmente
                if (forceTimeoutId) {
                    clearTimeout(forceTimeoutId);
                    forceTimeoutId = null;
                }

                // IMPORTANTE: Marca como finalizado IMEDIATAMENTE para parar todos os proxies
                isDiscovering = false;

                // Se ainda não atingiu o limite de 2 tentativas, tenta novamente
                if (totalAttempts < maxTotalAttempts && !discoveryFinished) {
                    // Reseta completedRequests para a próxima tentativa
                    completedRequests = 0;
                    setTimeout(function () {
                        if (!discoveryFinished) { // Verificação extra
                            discoverWorkingMethod();
                        }
                    }, 2000);
                } else {
                    // Finaliza definitivamente após 2 tentativas
                    shouldStopTrying();
                }
            }
        }

        // Timeout forçado: se após 15 segundos não completou 4 requisições, força a conclusão
        var forceTimeoutId = setTimeout(function () {
            if (completedRequests < 4 && !resolved && !discoveryFinished) {
                // Força completar as 4 requisições
                while (completedRequests < 4) {
                    completedRequests++;
                }
                // Força verificação de conclusão
                checkAllCompleted();
            }
        }, 15000);

        // Testa todos os métodos simultaneamente - o primeiro que responder vence!
        testAllMethodsSequentially();

        function testAllMethodsSequentially() {
            var methods = [
                { name: 'icecast-json', url: baseUrl + '/status-json.xsl', type: 'json' },
                { name: 'icecast-html', url: baseUrl + '/status.xsl', type: 'html' },
                { name: 'shoutcast-html', url: baseUrl + '/index.html', type: 'html' },
                { name: 'shoutcast-json', url: baseUrl + '/index.html?sid=1', type: 'html' }
            ];

            // Faz todas as 4 requisições simultaneamente
            methods.forEach(function (method, index) {

                fetchWithTimeout(method.url, 10000)
                    .then(function (response) {

                        // Se a resposta é opaque (CORS bloqueado), tenta uma abordagem alternativa
                        if (response.type === 'opaque') {
                            throw new Error(lknwpRadioTextsSong ? lknwpRadioTextsSong.corsBlocked : 'CORS_BLOCKED: Opaque response, cannot read content');
                        }

                        // Se status é 0 ou não está ok, também pode ser problema de CORS
                        if (!response.ok && response.status === 0) {
                            throw new Error(lknwpRadioTextsSong ? lknwpRadioTextsSong.networkError : 'NETWORK_ERROR: Status 0, possible network or CORS issue');
                        }

                        // Verifica Content-Type para evitar processar streams de áudio
                        var contentType = response.headers.get('content-type') || '';

                        if (contentType.includes('audio/') || contentType.includes('application/octet-stream')) {
                            throw new Error(lknwpRadioTextsSong ? lknwpRadioTextsSong.audioStream : 'AUDIO_STREAM: Response is an audio stream');
                        }

                        // Timeout para conversão de texto (evita travamento com streams grandes)
                        return Promise.race([
                            response.text().then(function (text) {
                                return { response: response, text: text, method: method };
                            }),
                            new Promise(function (_, reject) {
                                setTimeout(function () {
                                    reject(new Error(lknwpRadioTextsSong ? lknwpRadioTextsSong.textTimeout : 'TEXT_TIMEOUT: Text conversion exceeded 5 seconds'));
                                }, 5000);
                            })
                        ]);
                    })
                    .then(function (result) {
                        // Se já foi resolvido por outra requisição, ignora
                        if (resolved) {
                            checkAllCompleted();
                            return;
                        }

                        var success = false;


                        // Prioridade para métodos diretos que funcionam (status 200)
                        var isDirect = result.response.status === 200 && result.response.ok;

                        if (result.method.type === 'json') {
                            success = tryParseJsonData(result);
                        } else {
                            success = tryParseHtmlData(result);
                        }


                        if (success.found) {

                            // Se é método direto, tem prioridade máxima - resolve imediatamente
                            if (isDirect) {
                                resolved = true;
                                directMethodFound = true;
                                workingMethod = result.method.name;
                                methodUrl = result.method.url;
                                resetCounters();
                                isDiscovering = false;
                                setSongInfo(success.musica, success.artista);
                                return;
                            }

                            // Se não é direto, só resolve se ainda não foi resolvido e não há método direto
                            if (!resolved && !directMethodFound) {
                                resolved = true;
                                workingMethod = result.method.name;
                                methodUrl = result.method.url;
                                resetCounters();
                                isDiscovering = false;
                                setSongInfo(success.musica, success.artista);
                            } else {
                                checkAllCompleted();
                            }
                        } else {

                            // Se é método direto que falhou mas tem status 200, pode ser problema de content-type
                            if (isDirect && result.method.type === 'json') {
                                var htmlAttempt = tryParseHtmlData(result);
                                if (htmlAttempt.found) {
                                    resolved = true;
                                    directMethodFound = true;
                                    workingMethod = result.method.name + '-as-html';
                                    methodUrl = result.method.url;
                                    resetCounters();
                                    isDiscovering = false;
                                    setSongInfo(htmlAttempt.musica, htmlAttempt.artista);
                                    return;
                                }
                            }

                            checkAllCompleted();
                        }
                    })
                    .catch(function (error) {

                        if (error.message.includes('Failed to fetch')) {
                        }
                        if (error.message.includes('Timeout')) {
                        }

                        // Tenta proxy CORS se é erro relacionado a CORS, timeout, ou stream de áudio
                        if (error.message.includes('CORS_BLOCKED') ||
                            error.message.includes('Failed to fetch') ||
                            error.message.includes('NETWORK_ERROR') ||
                            error.message.includes('Timeout de') ||
                            error.message.includes('AUDIO_STREAM') ||
                            error.message.includes('TEXT_TIMEOUT')) {

                            // Só tenta proxy se ainda não foi resolvido, não há método direto E não completou 4 requisições
                            if (!resolved && !directMethodFound && completedRequests < 4 && !discoveryFinished) {
                                tryWithCorsProxy(method.url, method);
                            } else {
                                checkAllCompleted();
                            }
                        } else {
                            checkAllCompleted();
                        }
                    });
            });

            function tryWithCorsProxy(originalUrl, method) {
                // Lista de proxies CORS públicos
                var corsProxies = [
                    'https://cors-anywhere.herokuapp.com/',
                    'https://api.allorigins.win/raw?url=',
                    'https://corsproxy.io/?'
                ];

                var proxyIndex = 0;

                function tryNextProxy() {
                    // Se já foi resolvido, descoberta finalizada OU já completou 4 requisições básicas, para imediatamente
                    if (resolved || discoveryFinished || completedRequests >= 4) {
                        return;
                    }

                    if (proxyIndex >= corsProxies.length) {
                        checkAllCompleted();
                        return;
                    }

                    var proxyUrl = corsProxies[proxyIndex] + encodeURIComponent(originalUrl);

                    fetch(proxyUrl, {
                        method: 'GET',
                        mode: 'cors',
                        headers: {
                            'Accept': 'text/html,application/json,*/*',
                            'User-Agent': 'Mozilla/5.0 (compatible; RadioPlayer/1.0)'
                        }
                    })
                        .then(function (response) {
                            // Verifica se foi resolvido durante a requisição ou atingiu limite
                            if (resolved || discoveryFinished) {
                                return;
                            }

                            if (!response.ok) {
                                throw new Error('Proxy status: ' + response.status);
                            }
                            return response.text();
                        })
                        .then(function (text) {
                            // Última verificação antes de processar
                            if (resolved || discoveryFinished) {
                                return;
                            }


                            var result = {
                                response: { headers: { get: function () { return 'text/html'; } } },
                                text: text,
                                method: method
                            };

                            var success = false;
                            if (method.type === 'json') {
                                success = tryParseJsonData(result);
                            } else {
                                success = tryParseHtmlData(result);
                            }

                            if (success.found) {
                                // Só aceita proxy se não há método direto
                                if (!directMethodFound && !resolved) {
                                    resolved = true;

                                    // Detecta se é um método JSON que na verdade retorna HTML
                                    var isHtmlContent = text.trim().startsWith('<!DOCTYPE') || text.trim().startsWith('<html');
                                    if (method.type === 'json' && isHtmlContent) {
                                        workingMethod = method.name + '-as-html-proxy';
                                    } else {
                                        workingMethod = method.name + '-proxy';
                                    }

                                    methodUrl = proxyUrl; // Salva a URL do proxy para uso futuro
                                    resetCounters();
                                    isDiscovering = false;
                                    setSongInfo(success.musica, success.artista);

                                    // Para todas as outras tentativas
                                } else {
                                }
                                return;
                            } else {
                                checkAllCompleted();
                            }
                        })
                        .catch(function (err) {
                            proxyIndex++;

                            // Só tenta o próximo se ainda não foi resolvido e não atingiu limite
                            if (!resolved && !discoveryFinished) {
                                setTimeout(tryNextProxy, 1000); // 1s delay entre proxies
                            } else {
                            }
                        });
                }

                tryNextProxy();
            }
        }


    }

    // Funções de parsing globais (acessíveis por ambos discover e optimized)
    function tryParseJsonData(result) {
        try {
            var contentType = result.response.headers.get('content-type') || '';

            if (!contentType.includes('application/json')) {
                return { found: false, reason: (lknwpRadioTextsSong ? lknwpRadioTextsSong.contentTypeNotJson : 'Content-Type is not JSON: ') + contentType };
            }

            if (isAudioComponent(result.response, result.text)) {
                return { found: false, reason: lknwpRadioTextsSong ? lknwpRadioTextsSong.audioComponent : 'Detected audio component' };
            }

            var data = JSON.parse(result.text);
            var song = '';

            if (data.icestats && data.icestats.source) {
                var source = data.icestats.source;
                if (Array.isArray(source)) {
                    for (var i = 0; i < source.length; i++) {
                        if (source[i].title && source[i].title.trim()) {
                            song = source[i].title;
                            break;
                        } else if (source[i].yp_currently_playing && source[i].yp_currently_playing.trim()) {
                            song = source[i].yp_currently_playing;
                            break;
                        }
                    }
                } else {
                    song = source.title || source.yp_currently_playing || '';
                }
            }

            if (song && song.trim().length > 0) {
                var artista = '';
                var musica = song;
                if (song.indexOf(' - ') > -1) {
                    var parts = song.split(' - ');
                    artista = parts[0];
                    musica = parts[1];
                }

                // Decodifica entidades HTML
                musica = decodeHtmlEntities(musica);
                artista = decodeHtmlEntities(artista);

                return { found: true, musica: musica, artista: artista };
            } else {
                return { found: false, reason: lknwpRadioTextsSong ? lknwpRadioTextsSong.noSongFoundJson : 'No song found in JSON' };
            }
        } catch (e) {
            return { found: false, reason: 'Erro ao parsear JSON: ' + e.message };
        }
    }

    function tryParseHtmlData(result) {
        if (isAudioComponent(result.response, result.text)) {
            return { found: false, reason: lknwpRadioTextsSong ? lknwpRadioTextsSong.audioComponent : 'Detected audio component' };
        }

        var html = result.text;

        // Regex mais específico para o formato Shoutcast que você mostrou
        // Procura por Current Song que não seja vazio
        var matches = html.match(/<td>Current Song:<\/td><td class="streamdata">([^<]*)<\/td>/gi);
        var match = null;

        // Se encontrou matches, procura o primeiro que não esteja vazio
        if (matches) {
            for (var i = 0; i < matches.length; i++) {
                var tempMatch = matches[i].match(/<td>Current Song:<\/td><td class="streamdata">([^<]*)<\/td>/i);
                if (tempMatch && tempMatch[1] && tempMatch[1].trim().length > 0) {
                    match = tempMatch;
                    break;
                }
            }
        }

        // Se não encontrou Current Song válido, tenta outros padrões
        if (!match) {
            match = html.match(/<td>Playing Now:\s*<\/td><td><b><a[^>]*>([^<]+)<\/a><\/b><\/td>/i)
                || html.match(/<td>Playing Now: <\/td><td><b><a [^>]*>([^<]+)<\/a><\/b><\/td><\/tr>/i)
                || html.match(/<td>M[úu]sica Atual:<\/td><td class="streamdata">([^<]+)<\/td>/i)
                || html.match(/<td>T[ií]tulo:<\/td><td class="streamdata">([^<]+)<\/td>/i);
        }

        if (match && match[1] && match[1].trim()) {
            var musicaHtml = match[1].trim();
            var artistaHtml = '';
            if (musicaHtml.indexOf(' - ') > -1) {
                var parts = musicaHtml.split(' - ');
                artistaHtml = parts[0];
                musicaHtml = parts[1];
            }

            // Decodifica entidades HTML
            musicaHtml = decodeHtmlEntities(musicaHtml);
            artistaHtml = decodeHtmlEntities(artistaHtml);

            return { found: true, musica: musicaHtml, artista: artistaHtml };
        } else {
            // Verifica se há Current Song no HTML
            var currentSongPattern = /Current Song/gi;
            var songMatches = html.match(currentSongPattern);
            return { found: false, reason: lknwpRadioTextsSong ? lknwpRadioTextsSong.noSongFoundHtml : 'No song found in HTML' };
        }
    }

    function fetchCurrentSong() {
        if (!baseUrl) {
            try {
                var urlObj = new URL(streamUrl);
                // Remove o último segmento do pathname
                var pathParts = urlObj.pathname.split('/');
                if (pathParts.length > 1) {
                    pathParts.pop();
                }
                var cleanPath = pathParts.join('/');
                baseUrl = urlObj.origin + cleanPath;

            } catch (e) {
                return;
            }
        }

        // Usa o método otimizado que "aprende" qual funciona
        tryOptimizedMethod();
    }

    // Observer para aguardar o carregamento completo do card
    function waitForCardToLoad() {
        var cardContainer = document.getElementById('lknwp-radio-custom-player');
        var songDiv = document.getElementById('lknwp-radio-current-song');
        var artistDiv = document.getElementById('lknwp-radio-artist');
        var albumDiv = document.getElementById('lknwp-radio-album-img');


        // Verifica se todos os elementos necessários estão presentes
        if (cardContainer && songDiv && artistDiv && albumDiv) {

            // Aguarda um pouco mais para garantir que tudo esteja estabilizado
            setTimeout(function () {
                // Primeira tentativa de descoberta
                fetchCurrentSong();

                // Só cria setInterval após encontrar método ou finalizar descoberta
                var checkInterval = setInterval(function () {
                    if (workingMethod || discoveryFinished) {
                        clearInterval(checkInterval);
                        setInterval(fetchCurrentSong, 10000);
                    }
                }, 1000);
            }, 500);

        } else {
            setTimeout(waitForCardToLoad, 250);
        }
    }

    // Inicia a verificação após um pequeno delay
    setTimeout(waitForCardToLoad, 100);
})
