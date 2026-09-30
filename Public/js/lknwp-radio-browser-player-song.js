/**
 * LKN Radio Browser - Metadados da música atual (música/artista/capa/ouvintes).
 *
 * A consulta é feita via endpoint AJAX do WordPress (proxy no servidor), o que
 * evita os erros de CORS do navegador ao acessar o host do stream diretamente.
 */
(function () {
    'use strict';

    var cfg = window.lknwpRadioTextsSong || {};
    var ajaxUrl = cfg.ajaxUrl || '';
    var nonce = cfg.metadataNonce || '';
    var defaultAlbumUrl = cfg.defaultAlbumUrl || '';

    var player = document.getElementById('lknwp-radio-player');
    if (!player) {
        return;
    }

    var streamUrl = player.getAttribute('src');
    var lastSong = '';
    var pollTimer = null;

    // Aviso de conteúdo misto (página HTTPS + stream HTTP)
    if (window.location.protocol === 'https:' && streamUrl && streamUrl.indexOf('http:') === 0) {
        var warningDiv = document.createElement('div');
        warningDiv.style = 'background:#ffeaea;color:#d00;padding:12px;border-radius:8px;margin-bottom:16px;border:1px solid #d00;text-align:center;font-weight:600;';
        warningDiv.innerHTML = cfg.warning || 'Warning: This radio uses insecure streaming (HTTP) and cannot be played on HTTPS pages. Ask the provider to enable HTTPS or access via HTTP.';
        var playerBlock = document.getElementById('lknwp-radio-custom-player');
        if (playerBlock) {
            playerBlock.parentNode.insertBefore(warningDiv, playerBlock);
        } else {
            document.body.insertBefore(warningDiv, document.body.firstChild);
        }
        return;
    }

    function el(id) {
        return document.getElementById(id);
    }

    function formatNumber(num) {
        num = parseInt(num, 10) || 0;
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        } else if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'k';
        }
        return num.toString();
    }

    function setAlbumImage(url) {
        var albumDiv = el('lknwp-radio-album-img');
        if (!albumDiv) {
            return;
        }
        var src = url || defaultAlbumUrl;
        if (src && /^https?:\/\//i.test(src)) {
            var img = document.createElement('img');
            img.setAttribute('src', src);
            img.setAttribute('alt', 'Album');
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'cover';
            while (albumDiv.firstChild) {
                albumDiv.removeChild(albumDiv.firstChild);
            }
            albumDiv.appendChild(img);
            albumDiv.style.display = 'block';
        } else {
            albumDiv.textContent = '';
            albumDiv.style.display = 'none';
        }
    }

    function showBlock(show) {
        var block = el('lknwp-radio-current-song-block');
        if (!block) {
            return;
        }
        if (show) {
            block.classList.remove('lkp-current-song-block-hidden');
            block.classList.add('lkp-current-song-block');
        } else {
            block.classList.add('lkp-current-song-block-hidden');
            block.classList.remove('lkp-current-song-block');
        }
    }

    function renderSong(data) {
        var songDiv = el('lknwp-radio-current-song');
        var artistDiv = el('lknwp-radio-artist');
        var statsDiv = el('lknwp-radio-station-stats');

        var title = data.title || '';
        var artist = data.artist || '';
        var listeners = parseInt(data.listeners, 10) || 0;

        var isNewSong = (title !== lastSong && title !== '');

        if (isNewSong) {
            lastSong = title;
            if (songDiv) {
                songDiv.textContent = title;
            }
            if (artistDiv) {
                artistDiv.textContent = artist;
                artistDiv.style.display = artist ? '' : 'none';
            }
            setAlbumImage(data.album_art);
        }

        // O público atual é atualizado sempre, mesmo na mesma música
        if (statsDiv) {
            if (listeners > 0) {
                statsDiv.textContent = formatNumber(listeners) + ' ' + (cfg.listeners || 'listeners');
                statsDiv.style.display = 'block';
            } else {
                statsDiv.textContent = '';
                statsDiv.style.display = 'none';
            }
        }

        if (title !== '') {
            showBlock(true);
        }
    }

    function fetchMetadata() {
        if (!ajaxUrl || !streamUrl) {
            return;
        }

        var url = ajaxUrl + '?action=lknwp_radio_metadata'
            + '&nonce=' + encodeURIComponent(nonce)
            + '&stream=' + encodeURIComponent(streamUrl);

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (!res || !res.success || !res.data) {
                    return;
                }
                if (res.data.found) {
                    renderSong(res.data);
                } else if (lastSong === '') {
                    // Nada encontrado ainda: mantém o bloco oculto
                    showBlock(false);
                }
            })
            .catch(function () {
                // Silencioso: falha de rede não deve poluir o console
            });
    }

    function startPolling() {
        if (pollTimer) {
            return;
        }
        fetchMetadata();
        pollTimer = setInterval(fetchMetadata, 12000);
    }

    function stopPolling() {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
    }

    // Atualiza quando o player toca/pausa
    player.addEventListener('play', startPolling);
    player.addEventListener('pause', stopPolling);

    // Primeira consulta ao carregar a página
    setTimeout(fetchMetadata, 800);
})();
