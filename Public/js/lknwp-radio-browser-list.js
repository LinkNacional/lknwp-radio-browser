// Arquivo base para o webpack, qualquer mudança necessário executar o webpack para aplica-la.

import 'select2/dist/js/select2.min.js';
import 'select2/dist/css/select2.min.css';

(function ($) {

    $(document).ready(function () {
        var texts = window.lknwpRadioTextsList || {};
        var defaultImg = texts.defaultImgUrl || '';
        var reqToken = 0;
        var currentView = 'discover';

        // Não envia o form com Enter (aplicação é reativa)
        $('.lrt-radio-form').on('keydown', function (e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                return false;
            }
        });

        /* ============================================================
         * Helpers
         * ============================================================ */
        // Escapa para uso seguro em texto E atributos (evita XSS vindo da API)
        function esc(s) {
            return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
            });
        }

        function flagEmoji(cc) {
            if (!cc || cc.length !== 2) return '';
            return cc.toUpperCase().replace(/./g, function (c) {
                return String.fromCodePoint(127397 + c.charCodeAt(0));
            });
        }

        function fillFlags($scope) {
            ($scope || $(document)).find('.lrt-flag[data-cc]').each(function () {
                var cc = this.getAttribute('data-cc');
                if (cc && !this.textContent) {
                    this.textContent = flagEmoji(cc);
                }
            });
        }

        function readFavs() {
            try { return JSON.parse(localStorage.getItem('lknwp_favs') || '[]'); } catch (e) { return []; }
        }

        function toggleFav(uuid) {
            var favs = readFavs();
            var idx = favs.indexOf(uuid);
            if (idx === -1) { favs.push(uuid); } else { favs.splice(idx, 1); }
            try { localStorage.setItem('lknwp_favs', JSON.stringify(favs)); } catch (e) {}
            return idx === -1;
        }

        function nowPlayingUuid() {
            try { return localStorage.getItem('lknwp_now_playing') || ''; } catch (e) { return ''; }
        }

        /* ============================================================
         * Continue ouvindo — rádios recentes (localStorage)
         * Guarda {uuid, name, img, url} das estações que o usuário abriu.
         * ============================================================ */
        var RECENT_KEY = 'lknwp_recent_stations';
        var RECENT_MAX = 12;
        // Estação "selecionada" atual do card "Tocando agora" (separada dos
        // recentes para que a auto-seleção não polua o "Continue ouvindo").
        var NOW_KEY = 'lknwp_now_station';

        function readRecentStations() {
            try {
                var list = JSON.parse(localStorage.getItem(RECENT_KEY) || '[]');
                return Array.isArray(list) ? list : [];
            } catch (e) { return []; }
        }

        function readNowStation() {
            try {
                var s = JSON.parse(localStorage.getItem(NOW_KEY) || 'null');
                return (s && typeof s === 'object') ? s : null;
            } catch (e) { return null; }
        }

        function writeNowStation(st) {
            try { localStorage.setItem(NOW_KEY, JSON.stringify(st || {})); } catch (e) {}
        }

        function stationId(s) {
            return (s && (s.name || s.url) || '').toString().trim().toLowerCase();
        }

        // Mescla a estação recebida com os dados já guardados (por nome),
        // preservando campos não informados (ex.: música vinda do player).
        function rememberStation(station) {
            if (!station || (!station.name && !station.url)) return null;
            var list = readRecentStations();
            var id = stationId(station);
            var prev = null;
            list = list.filter(function (s) {
                if (stationId(s) === id) { prev = s; return false; }
                return true;
            });

            var merged = { uuid: '', name: '', img: '', url: '', genre: '', country: '', cc: '', song: '', artist: '', stream: '' };
            if (prev) { $.extend(merged, prev); }
            ['uuid', 'name', 'img', 'url', 'genre', 'country', 'cc', 'song', 'artist', 'stream'].forEach(function (k) {
                if (station[k]) { merged[k] = station[k]; }
            });

            list.unshift(merged);
            try { localStorage.setItem(RECENT_KEY, JSON.stringify(list.slice(0, RECENT_MAX))); } catch (e) {}
            return merged;
        }

        // Estação atual do "Tocando agora": a selecionada; se não houver,
        // cai no topo dos recentes (compatibilidade).
        function currentStation() {
            var now = readNowStation();
            if (now && (now.name || now.url)) return now;
            var list = readRecentStations();
            return list.length ? list[0] : null;
        }

        // Extrai os dados de uma estação a partir de um card da lista.
        function stationFromCard($card) {
            var $loc = $card.find('.lrt-radio-station__loc').first().clone();
            $loc.find('.lrt-flag').remove();
            return {
                uuid: $card.attr('data-uuid') || '',
                name: $.trim($card.find('.lrt-radio-station__name').first().text()),
                img: $card.find('.lrt-radio-station__logo').first().attr('src') || '',
                url: $card.find('.lrt-radio-station__link').first().attr('href') || '',
                genre: $.trim($card.find('.lrt-radio-station__genre').first().text()),
                cc: ($card.find('.lrt-flag').first().attr('data-cc') || '').toUpperCase(),
                country: $.trim($loc.text()),
                stream: $card.attr('data-stream') || ''
            };
        }

        function markNowPlaying(uuid) {
            uuid = uuid || '';
            try {
                if (uuid) { localStorage.setItem('lknwp_now_playing', uuid); }
            } catch (e) {}
            $('.lrt-radio-station').removeClass('is-playing');
            if (uuid) {
                $('.lrt-radio-station[data-uuid="' + uuid + '"]').addClass('is-playing');
            }
        }

        function renderContinueListening() {
            var $section = $('#lrt_continue');
            var $track = $('#lrt_continue_track');
            if (!$section.length || !$track.length) return;

            var list = readRecentStations();
            var html = '';
            list.forEach(function (s) {
                var img = s.img || defaultImg;
                var name = s.name || '';
                html += '<a class="lrt-continue-card" href="' + esc(s.url) + '" data-player-link="1" target="_blank" rel="noopener" title="' + esc(name) + '">' +
                    '<span class="lrt-continue-card__cover">' +
                        '<img class="lrt-continue-card__logo" src="' + esc(img) + '" alt="' + esc(name) + '" loading="lazy">' +
                        '<span class="lrt-continue-card__play" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 5v14l11-7z" fill="currentColor"/></svg></span>' +
                    '</span>' +
                    '<span class="lrt-continue-card__name">' + esc(name) + '</span>' +
                '</a>';
            });
            $track.html(html);

            $track.find('.lrt-continue-card__logo').on('error', function () {
                if (defaultImg && this.src !== defaultImg) {
                    this.src = defaultImg;
                }
            });

            updateContinueVisibility();
        }

        // Mostra o bloco apenas na visão "Descobrir" e quando há itens
        function updateContinueVisibility() {
            var $section = $('#lrt_continue');
            if (!$section.length) return;
            var show = currentView === 'discover' && readRecentStations().length > 0;
            if (show) {
                $section.removeAttr('hidden');
                $section.toggle(true);
            } else {
                $section.attr('hidden', true);
                $section.toggle(false);
            }
        }

        /* ============================================================
         * "Tocando agora" — card de detalhes da estação selecionada
         * ============================================================ */

        // Ícones (mesmos dos chips de categoria) usados como decoração de
        // fundo do card — refletem o botão de categoria ativo.
        var LRT_ICONS = {
            all: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>',
            radio: '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 13a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><rect x="2.5" y="12.5" width="4.5" height="8" rx="2" fill="currentColor"/><rect x="17" y="12.5" width="4.5" height="8" rx="2" fill="currentColor"/></svg>',
            rock: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
            mpb: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>',
            electronic: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>',
            sertanejo: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m11.9 12.1 4.51-4.51"/><path d="M20.1 2.3a1 1 0 0 0-1.4 0l-1.12 1.11A2 2 0 0 0 17 4.83v1.34a2 2 0 0 1-.59 1.42"/><path d="m6 16 2 2"/><path d="M8.23 9.85A3 3 0 0 1 11 11a3 3 0 0 1 2.9 1.7 6 6 0 0 1-1.34 6.19l-1.34 1.35a3 3 0 0 1-1.3.79l-.7.2-3.05.6-.89-.89-.6-3.05.2-.7a3 3 0 0 1 .79-1.3l1.35-1.34A6 6 0 0 1 8.23 9.85Z"/></svg>',
            pop: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
            jazz: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M6 12c0-1.7.7-3.2 1.8-4.2"/><circle cx="12" cy="12" r="2"/></svg>',
            news: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>'
        };

        // Define o ícone de fundo a partir do data-tag do chip ativo.
        // Sem tag (botão "Todos") → ícone de grade.
        function setNowBgFromTag(tag) {
            tag = (tag || '').toString().toLowerCase().trim();
            var icon = LRT_ICONS[tag] || LRT_ICONS.all;
            var $bg = $('#lrt_now_bg');
            if (!$bg.length) return;
            $bg.html(icon);
            // Pequeno fade para a troca do ícone acompanhar a troca da rádio.
            $bg.removeClass('is-swap');
            void $bg[0].offsetWidth; // reinicia a animação
            $bg.addClass('is-swap');
        }

        // Tag do chip de categoria ativo no momento.
        function activeChipTag() {
            return $('#lrt_tag_pills .lrt-chip.is-active').data('tag') || '';
        }

        function renderNowPlaying(flash) {
            var $section = $('#lrt_now_playing');
            if (!$section.length) return;

            var st = currentStation();
            if (!st) {
                $section.attr('hidden', true);
                renderDock();
                return;
            }

            $('#lrt_now_name').text(st.name || '');

            // Imagem da rádio
            var $img = $('#lrt_now_img');
            $img.off('error.lrtnow').on('error.lrtnow', function () {
                if (defaultImg && this.src !== defaultImg) {
                    this.src = defaultImg;
                }
            });
            $img.attr('src', st.img || defaultImg).attr('alt', st.name || '');

            // Estilo (gênero)
            if (st.genre) {
                $('#lrt_now_genre').text(st.genre).removeAttr('hidden');
            } else {
                $('#lrt_now_genre').attr('hidden', true).text('');
            }

            // País (+ bandeira)
            var cc = (st.cc || '').toUpperCase();
            if (st.country || cc) {
                var flagEl = document.getElementById('lrt_now_flag');
                if (flagEl) {
                    flagEl.setAttribute('data-cc', cc);
                    flagEl.textContent = flagEmoji(cc);
                }
                $('#lrt_now_country_text').text(st.country || cc);
                $('#lrt_now_country').removeAttr('hidden');
            } else {
                $('#lrt_now_country').attr('hidden', true);
            }

            // Música atual (se conhecida)
            var songText = st.song ? (st.artist ? (st.artist + ' — ' + st.song) : st.song) : '';
            if (songText) {
                $('#lrt_now_song_text').text(songText);
                $('#lrt_now_song').removeAttr('hidden');
            } else {
                $('#lrt_now_song').attr('hidden', true).find('#lrt_now_song_text').text('');
            }

            // Link "abrir player em nova aba"
            $('#lrt_now_open').attr('href', st.url || '#');

            // Estado do coração (favorito)
            var favKey = st.uuid || ('name:' + (st.name || '').toLowerCase());
            $('#lrt_now_fav').toggleClass('is-active', readFavs().indexOf(favKey) !== -1);

            $section.removeAttr('hidden');

            if (flash) {
                $section.removeClass('is-updated');
                void $section[0].offsetWidth; // reinicia a animação
                $section.addClass('is-updated');
            }

            renderDock();
        }

        /* ============================================================
         * Barra de play (rodapé do componente)
         * ============================================================ */
        function dockAudioEl() {
            return document.getElementById('lrt_dock_audio');
        }

        // Atualiza a barra a partir da estação selecionada (currentStation).
        function renderDock() {
            var $dock = $('#lrt_player_dock');
            if (!$dock.length) return;

            var st = currentStation();
            if (!st) {
                $dock.attr('hidden', true);
                var a0 = dockAudioEl();
                if (a0) { try { a0.pause(); } catch (e) {} }
                return;
            }

            $dock.removeAttr('hidden');

            var $img = $('#lrt_dock_img');
            $img.off('error.lrtdock').on('error.lrtdock', function () {
                if (defaultImg && this.src !== defaultImg) { this.src = defaultImg; }
            });
            $img.attr('src', st.img || defaultImg).attr('alt', st.name || '');

            $('#lrt_dock_name').text(st.name || '');

            // Subtítulo: gênero · [bandeira] país
            var subHtml = '';
            if (st.genre) { subHtml += esc(st.genre); }
            if (st.country || st.cc) {
                var cc = (st.cc || '').toUpperCase();
                var flag = flagEmoji(cc);
                var countryTxt = st.country || cc;
                if (subHtml) { subHtml += ' · '; }
                subHtml += (flag ? '<span class="lrt-flag">' + esc(flag) + '</span> ' : '') + esc(countryTxt);
            }
            $('#lrt_dock_sub').html(subHtml);

            var favKey = st.uuid || ('name:' + (st.name || '').toLowerCase());
            $('#lrt_dock_fav').toggleClass('is-active', readFavs().indexOf(favKey) !== -1);

            // Troca a fonte do áudio só quando o stream muda
            var a = dockAudioEl();
            if (a) {
                var stream = st.stream || '';
                if (a.getAttribute('data-src') !== stream) {
                    a.setAttribute('data-src', stream);
                    if (stream) { a.src = stream; a.load(); } else { a.removeAttribute('src'); }
                }
            }
            updateDockPlayState();
        }

        function dockIsPlaying() {
            var a = dockAudioEl();
            return !!a && !a.paused && !a.ended && a.currentSrc !== '' && a.readyState >= 2;
        }

        function updateDockPlayState() {
            $('#lrt_player_dock').toggleClass('is-playing', dockIsPlaying());
            syncDockStacking();
        }

        /* ============================================================
         * Stacking: quando a barra está flutuando (fixa), movemos o
         * elemento para o <body> para escapar do stacking context do
         * tema (ex.: Woodmart), que senão a mantém atrás do header.
         * ============================================================ */
        var dockHomeParent = null;
        var dockHomeNext = null;

        function dockIsMobile() {
            return !!(window.matchMedia && window.matchMedia('(max-width: 782px)').matches);
        }

        function dockShouldFloat($dock) {
            if ($dock.hasClass('is-fixed')) return true;
            return dockIsMobile() && $dock.hasClass('is-playing');
        }

        function syncDockStacking() {
            var dock = document.getElementById('lrt_player_dock');
            if (!dock) return;
            var $dock = $(dock);
            var shouldFloat = dockShouldFloat($dock);
            var inBody = dock.parentNode === document.body;

            if (shouldFloat && !inBody) {
                dockHomeParent = dock.parentNode;
                dockHomeNext = dock.nextSibling;
                document.body.appendChild(dock);
            } else if (!shouldFloat && inBody && dockHomeParent) {
                if (dockHomeNext && dockHomeNext.parentNode === dockHomeParent) {
                    dockHomeParent.insertBefore(dock, dockHomeNext);
                } else {
                    dockHomeParent.appendChild(dock);
                }
            }
        }

        function updateDockVolumeIcon() {
            var a = dockAudioEl();
            $('#lrt_player_dock').toggleClass('is-muted', !!a && a.muted);
        }

        function playDock() {
            var a = dockAudioEl();
            if (!a || !a.getAttribute('src')) return;
            var p = a.play();
            if (p && p.catch) { p.catch(function () {}); }
        }

        // Pular para a rádio anterior (-1) / próxima (+1) da lista visível.
        function dockSkip(dir) {
            var $cards = $('.lrt-radio-list .lrt-radio-station').not('.is-hidden');
            if (!$cards.length) return;
            var cur = currentStation();
            var curUuid = cur ? (cur.uuid || '') : '';
            var curName = cur ? (cur.name || '').toLowerCase() : '';
            var idx = -1;
            $cards.each(function (i) {
                var u = $(this).attr('data-uuid') || '';
                var n = $.trim($(this).find('.lrt-radio-station__name').first().text()).toLowerCase();
                if ((curUuid && u === curUuid) || (!curUuid && n && n === curName)) { idx = i; return false; }
            });
            var next = (idx === -1) ? 0 : (idx + dir + $cards.length) % $cards.length;
            var $target = $cards.eq(next);
            if ($target.length) { selectCard($target, { autoplay: true }); }
        }

        // Seleciona um card: preenche "Tocando agora" e a barra de play.
        function selectCard($card, opts) {
            opts = opts || {};
            var st = stationFromCard($card);
            if (!st.name && !st.url) return;
            writeNowStation(st);
            if (opts.remember) { rememberStation(st); }
            markNowPlaying(st.uuid);
            renderNowPlaying(true);
            if (opts.remember) { renderContinueListening(); }
            if (opts.autoplay) { playDock(); }
        }

        // Auto-seleciona a primeira rádio visível da lista → preenche o
        // "Tocando agora" e o ícone de fundo. NÃO entra no "Continue ouvindo".
        function autoSelectFirstStation(flash) {
            var $first = $('.lrt-radio-list .lrt-radio-station').not('.is-hidden').first();
            if (!$first.length) return;
            var st = stationFromCard($first);
            if (!st.name && !st.url) return;
            writeNowStation(st);
            markNowPlaying(st.uuid);
            renderNowPlaying(flash);
            // O ícone troca junto com a rádio (não no clique do chip).
            setNowBgFromTag(activeChipTag());
        }

        // Aplica favoritos/estado "no ar" aos cards já renderizados (server-side)
        function applyCardStates($scope) {
            var favs = readFavs();
            var playing = nowPlayingUuid();
            ($scope || $(document)).find('.lrt-radio-station').each(function () {
                var $card = $(this);
                var uuid = $card.attr('data-uuid');
                if (!uuid) return;
                $card.toggleClass('is-playing', uuid === playing);
                $card.find('.lrt-radio-station__fav').toggleClass('is-active', favs.indexOf(uuid) !== -1);
            });
        }

        /* ============================================================
         * Monta o card de uma estação
         * ============================================================ */
        function buildStationCard(station, playerBaseUrl) {
            var name = station.name || '';
            var img = station.favicon || defaultImg;
            var uuid = station.stationuuid || '';

            var slug = name.replace(/[\/\?#&]/g, '').replace(/ /g, '%20');
            var playerUrl = playerBaseUrl + slug + '/';

            var genre = station.tags ? String(station.tags).split(',')[0].trim() : '';
            var cc = station.countrycode ? String(station.countrycode).toUpperCase() : '';
            var country = station.country || cc;
            var stream = station.url_resolved || station.url || '';

            var flag = cc ? '<span class="lrt-flag" data-cc="' + esc(cc) + '"></span>' : '';

            var html = '' +
                '<a href="' + esc(playerUrl) + '" data-player-link="1" target="_blank" rel="noopener" class="lrt-radio-station__link">' +
                    '<span class="lrt-radio-station__cover">' +
                        '<img src="' + esc(img) + '" alt="' + esc(texts.logoAlt || 'Logo') + '" class="lrt-radio-station__logo" loading="lazy">' +
                    '</span>' +
                    '<span class="lrt-radio-station__body">' +
                        '<span class="lrt-radio-station__name">' + esc(name) + '</span>' +
                        (genre ? '<span class="lrt-radio-station__genre">' + esc(genre) + '</span>' : '') +
                        '<span class="lrt-radio-station__loc">' + flag + esc(country) + '</span>' +
                    '</span>' +
                '</a>' +
                '<button type="button" class="lrt-radio-station__fav" aria-label="' + esc(texts.favorite || 'Favoritar') + '">' +
                    '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 20s-7-4.5-9.3-8.4C1 8.6 2.6 5 6.1 5c2 0 3.3 1.1 3.9 2 .6-.9 1.9-2 3.9-2C17.4 5 19 8.6 21.3 11.6 19 15.5 12 20 12 20Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>' +
                '</button>';

            var $li = $('<li>').addClass('lrt-radio-station').attr('data-uuid', uuid).attr('data-stream', stream).html(html);

            // Fallback da imagem (client-side)
            $li.find('.lrt-radio-station__logo').on('error', function () {
                if (defaultImg && this.src !== defaultImg) {
                    this.src = defaultImg;
                }
            });

            return $li;
        }

        function renderStations($list, stations, playerBaseUrl) {
            $list.empty();
            var $frag = $(document.createDocumentFragment());
            stations.forEach(function (station) {
                $frag.append(buildStationCard(station, playerBaseUrl));
            });
            $list.append($frag);
            fillFlags($list);
            applyCardStates($list);
            autoSelectFirstStation(false);
            if (typeof applyView === 'function') {
                applyView();
            }
        }

        function getPlayerBaseUrl() {
            var $hidden = $('#lrt_player_base_url');
            if ($hidden.length) {
                try { return atob($hidden.val()); } catch (e) {}
            }
            return 'player';
        }

        /* ============================================================
         * Consulta à API (debounced)
         * ============================================================ */
        var API_SERVERS = [
            "https://de2.api.radio-browser.info",
            "https://fi1.api.radio-browser.info",
            "https://fr1.api.radio-browser.info",
            "https://nl1.api.radio-browser.info"
        ];

        function buildApiUrl() {
            var query = $('#lrt_radio_search').val() ? $('#lrt_radio_search').val().trim() : '';
            var countrycode = $('#lrt_countrycode').val();
            var limit = $('#lrt_limit').val() || 20;
            var sort = $('#lrt_sort').val() || 'clickcount';
            var reverse = $('#lrt_reverse').val() || '1';
            var tag = $('#lrt_tag').val() || '';
            var language = $('#lrt_language').val() || '';
            var genre = tag || $('#lrt_genre').val() || '';
            if (genre === 'all') genre = '';

            var base = API_SERVERS[Math.floor(Math.random() * API_SERVERS.length)];
            var params = [];
            if (query) params.push('name=' + encodeURIComponent(query));
            if (countrycode && countrycode !== 'all') params.push('countrycode=' + encodeURIComponent(countrycode));
            if (sort) params.push('order=' + encodeURIComponent(sort));
            if (limit) params.push('limit=' + encodeURIComponent(limit));
            params.push('hidebroken=true');
            if (reverse === '1') params.push('reverse=true');
            if (genre) params.push('tagList=' + encodeURIComponent(genre));
            if (language) params.push('language=' + encodeURIComponent(language));

            return base + '/json/stations/search?' + params.join('&');
        }

        /* ============================================================
         * Loader personalizado (equalizador) — usado de forma
         * consistente em toda a interface durante o carregamento.
         * ============================================================ */
        function lrtLoaderHTML() {
            return '<span class="lrt-loader" role="status" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></span>';
        }

        // Liga/desliga o estado de carregamento em TODO o componente,
        // para que o card "Tocando agora" e a lista mudem juntos.
        function setLoading(on) {
            $('.lrt-radio-wrap').toggleClass('is-loading', !!on);
        }

        function autoQueryRadios() {
            clearTimeout(autoQueryRadios.timeout);
            autoQueryRadios.timeout = setTimeout(function () {
                var $list = $('.lrt-radio-list');
                if (!$list.length) return;
                var playerBaseUrl = getPlayerBaseUrl();
                var myToken = ++reqToken;

                setLoading(true);
                $list.addClass('is-loading').html('<li class="lrt-radio-loading">' + lrtLoaderHTML() + '<span>' + esc(texts.loadingRadios || 'Loading radios...') + '</span></li>');

                var apiUrl = buildApiUrl();

                function handleStations(stations) {
                    if (myToken !== reqToken) return; // resposta antiga: ignora
                    setLoading(false);
                    $list.removeClass('is-loading');
                    if (!Array.isArray(stations) || stations.length === 0) {
                        $list.html('<li class="lrt-radio-error">' + esc(texts.noRadiosFound || 'No radios found.') + '</li>');
                        return;
                    }
                    renderStations($list, stations, playerBaseUrl);
                }

                fetch(apiUrl, { headers: { 'User-Agent': 'lknwp-radio-browser/1.0' } })
                    .then(function (r) { return r.json(); })
                    .then(handleStations)
                    .catch(function () {
                        var found = false;
                        (async function () {
                            for (var i = 0; i < API_SERVERS.length; i++) {
                                var altUrl = apiUrl.replace(/https:\/\/[^/]+/, API_SERVERS[i]);
                                try {
                                    var response = await fetch(altUrl, { headers: { 'User-Agent': 'lknwp-radio-browser/1.0' } });
                                    if (!response.ok) continue;
                                    var stations = await response.json();
                                    handleStations(stations);
                                    found = true;
                                    break;
                                } catch (e) { /* próximo servidor */ }
                            }
                            if (!found && myToken === reqToken) {
                                setLoading(false);
                                $list.removeClass('is-loading');
                                $list.html('<li class="lrt-radio-error">' + esc(texts.apiError || 'Error querying API.') + '</li>');
                            }
                        })();
                    });
            }, 700);
        }

        /* ============================================================
         * Filtros / pills
         * ============================================================ */
        $('#lrt_reverse_btn').on('click', function () {
            var $input = $('#lrt_reverse');
            var val = $input.val() === '1' ? '0' : '1';
            $input.val(val);
            $('.lrt-reverse-label').text(val === '1' ? (texts.descending || 'Maior') : (texts.ascending || 'Menor'));
            autoQueryRadios();
        });

        $('#lrt_radio_search').on('input', autoQueryRadios);
        $('#lrt_countrycode').on('change', autoQueryRadios);
        $('#lrt_sort').on('change', autoQueryRadios);

        // Pills de categoria
        $('#lrt_tag_pills').on('click', '.lrt-chip', function () {
            var tag = $(this).data('tag') || '';
            $('#lrt_tag').val(tag);
            $('#lrt_tag_pills .lrt-chip').removeClass('is-active');
            $(this).addClass('is-active');
            // O ícone de fundo só troca junto com a rádio (quando a lista chega).
            if (tag) {
                $('#lrt_genre').val('all').trigger('change.select2');
            }
            autoQueryRadios();
        });

        // Select de gênero: limpa as pills
        $('#lrt_genre').on('change', function () {
            $('#lrt_tag').val('');
            $('#lrt_tag_pills .lrt-chip').removeClass('is-active');
            $('#lrt_tag_pills .lrt-chip[data-tag=""]').addClass('is-active');
            autoQueryRadios();
        });

        // Inicializa Select2
        // dropdownCssClass: o dropdown é anexado ao <body> (dropdownParent padrão),
        // então precisamos de uma classe própria para conseguir estilizá-lo.
        $('#lrt_genre').select2({
            placeholder: texts.placeholder || '',
            allowClear: true,
            width: 'resolve',
            dropdownCssClass: 'lrt-select2-dropdown'
        });

        /* ============================================================
         * Favoritos + estado "no ar"
         * ============================================================ */
        $(document).on('click', '.lrt-radio-station__fav', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var $card = $(this).closest('.lrt-radio-station');
            var uuid = $card.attr('data-uuid');
            if (!uuid) return;
            var isFav = toggleFav(uuid);
            $(this).toggleClass('is-active', isFav);
            if (currentView === 'favorites') {
                applyView();
            }
        });

        // Clicar num card (lista de resultados): NÃO navega — carrega os detalhes
        // no card "Tocando agora" e na barra de play.
        $(document).on('click', '.lrt-radio-station [data-player-link]', function (e) {
            e.preventDefault();

            var $card = $(this).closest('.lrt-radio-station');
            var uuid = $card.attr('data-uuid') || '';

            if (uuid) {
                try {
                    var recents = JSON.parse(localStorage.getItem('lknwp_recents') || '[]');
                    recents = recents.filter(function (u) { return u !== uuid; });
                    recents.unshift(uuid);
                    localStorage.setItem('lknwp_recents', JSON.stringify(recents.slice(0, 30)));
                } catch (err) {}
            }

            selectCard($card, { remember: true });
        });

        // Clicar num card de "Continue ouvindo": também NÃO navega — carrega os
        // detalhes no "Tocando agora" (preservando gênero/país/música já salvos).
        $(document).on('click', '.lrt-continue-card', function (e) {
            e.preventDefault();
            var url = $(this).attr('href') || '';
            var name = $.trim($(this).find('.lrt-continue-card__name').first().text());
            var img = $(this).find('.lrt-continue-card__logo').first().attr('src') || '';
            var merged = rememberStation({ name: name, img: img, url: url });
            if (merged) { writeNowStation(merged); }
            markNowPlaying(merged ? merged.uuid : '');
            renderNowPlaying(true);
            renderContinueListening();
        });

        // Botão coração do card "Tocando agora"
        $(document).on('click', '#lrt_now_fav', function () {
            var st = currentStation();
            if (!st) return;
            var favKey = st.uuid || ('name:' + (st.name || '').toLowerCase());
            var isFav = toggleFav(favKey);
            $(this).toggleClass('is-active', isFav);
            $('#lrt_dock_fav').toggleClass('is-active', isFav);
            if (st.uuid) {
                $('.lrt-radio-station[data-uuid="' + st.uuid + '"] .lrt-radio-station__fav').toggleClass('is-active', isFav);
            }
        });

        /* ============================================================
         * Eventos da barra de play (dock)
         * ============================================================ */
        $(document).on('click', '#lrt_dock_play', function () {
            var a = dockAudioEl();
            if (!a) return;
            if (a.paused) {
                playDock();
            } else {
                a.pause();
            }
        });

        $(document).on('click', '#lrt_dock_prev', function () { dockSkip(-1); });
        $(document).on('click', '#lrt_dock_next', function () { dockSkip(1); });

        $(document).on('click', '#lrt_dock_fav', function () {
            var st = currentStation();
            if (!st) return;
            var favKey = st.uuid || ('name:' + (st.name || '').toLowerCase());
            var isFav = toggleFav(favKey);
            $(this).toggleClass('is-active', isFav);
            $('#lrt_now_fav').toggleClass('is-active', isFav);
            if (st.uuid) {
                $('.lrt-radio-station[data-uuid="' + st.uuid + '"] .lrt-radio-station__fav').toggleClass('is-active', isFav);
            }
        });

        $(document).on('input', '#lrt_dock_volume', function () {
            var a = dockAudioEl();
            if (a) { a.volume = parseFloat(this.value); a.muted = false; }
            updateDockVolumeIcon();
        });

        $(document).on('click', '#lrt_dock_mute', function () {
            var a = dockAudioEl();
            if (!a) return;
            a.muted = !a.muted;
            updateDockVolumeIcon();
        });

        $(document).on('click', '#lrt_dock_pin', function () {
            var $dock = $('#lrt_player_dock');
            $dock.toggleClass('is-fixed');
            var fixed = $dock.hasClass('is-fixed');
            $(this).attr('aria-pressed', fixed ? 'true' : 'false');
            $(this).toggleClass('is-active', fixed);
            syncDockStacking();
        });

        // Reposiciona o stacking quando muda mobile <-> desktop
        $(window).on('resize', function () { syncDockStacking(); });

        // Eventos do elemento <audio> (estado do play + ícone de volume)
        (function wireDockAudio() {
            var a = dockAudioEl();
            if (!a) return;
            a.volume = 0.8;
            ['play', 'pause', 'ended', 'playing', 'waiting'].forEach(function (ev) {
                a.addEventListener(ev, updateDockPlayState);
            });
            a.addEventListener('volumechange', updateDockVolumeIcon);
            a.addEventListener('error', function () { updateDockPlayState(); });
        })();

        /* ============================================================
         * Sidebar (menu + navegação)
         * ============================================================ */
        function readRecents() {
            try { return JSON.parse(localStorage.getItem('lknwp_recents') || '[]'); } catch (e) { return []; }
        }

        function showEmpty(msg) {
            var $empty = $('#lrt-empty');
            if (msg) {
                $empty.find('.lrt-empty__text').text(msg);
                $empty.removeAttr('hidden');
            } else {
                $empty.attr('hidden', true);
            }
        }

        function applyView() {
            var $cards = $('.lrt-radio-station');
            $('#lrt-empty').attr('hidden', true);

            if (currentView === 'discover') {
                $cards.removeClass('is-hidden');
                return;
            }

            var list = currentView === 'favorites' ? readFavs() : readRecents();
            var visible = 0;
            $cards.each(function () {
                var uuid = $(this).attr('data-uuid');
                var show = uuid && list.indexOf(uuid) !== -1;
                $(this).toggleClass('is-hidden', !show);
                if (show) visible++;
            });

            if (visible === 0) {
                showEmpty(currentView === 'favorites'
                    ? (texts.noFavorites || 'Você ainda não favoritou nenhuma rádio.')
                    : (texts.noRecents || 'Você ainda não ouviu nenhuma rádio.'));
            }
        }

        function setView(view) {
            currentView = view;
            $('.lrt-side-item').removeClass('is-active');
            $('.lrt-side-item[data-view="' + view + '"]').addClass('is-active');
            $('#lrt_tag_pills, .lrt-search, .lrt-toolbar').toggle(view === 'discover');
            updateContinueVisibility();
            applyView();
        }

        $('.lrt-side-item').on('click', function () {
            setView($(this).data('view'));
        });

        // Botão "Filtros" (abre/fecha o painel avançado)
        function setFiltersOpen(open) {
            var $panel = $('#lrt_advanced_filters');
            var $btn = $('#lrt_filters_btn');
            $btn.attr('aria-expanded', open ? 'true' : 'false');
            if (open) {
                $panel.removeAttr('hidden');
            } else {
                $panel.attr('hidden', true);
            }
        }

        $('#lrt_filters_btn').on('click', function () {
            setFiltersOpen($(this).attr('aria-expanded') !== 'true');
        });

        $('.lrt-side-nav-item').on('click', function () {
            var nav = $(this).data('nav');
            if (nav === 'genre') {
                setFiltersOpen(true);
                if ($('#lrt_genre').data('select2')) {
                    $('#lrt_genre').select2('open');
                } else {
                    $('#lrt_genre').trigger('focus');
                }
            } else if (nav === 'country') {
                $('#lrt_countrycode').trigger('focus');
            } else if (nav === 'language') {
                setFiltersOpen(true);
                $('#lrt_language').trigger('focus');
            }
        });

        $('#lrt_language').on('change', autoQueryRadios);

        /* ============================================================
         * Inicialização
         * ============================================================ */
        fillFlags($(document));
        applyCardStates($(document));
        renderContinueListening();

        // Se o gênero já vem preenchido, marca a pill correspondente
        var currentGenre = $('#lrt_genre').val();
        if (currentGenre && currentGenre !== 'all') {
            var $matchPill = $('#lrt_tag_pills .lrt-chip[data-tag="' + currentGenre + '"]');
            if ($matchPill.length) {
                $('#lrt_tag_pills .lrt-chip').removeClass('is-active');
                $matchPill.addClass('is-active');
            }
        }

        // Auto-seleciona a primeira rádio já renderizada (server-side) e preenche
        // o "Tocando agora" + ícone de fundo. Sem rádios, esconde o card.
        if ($('.lrt-radio-list .lrt-radio-station').length) {
            autoSelectFirstStation(false);
        } else {
            renderNowPlaying();
            setNowBgFromTag(activeChipTag());
        }
    });

})(jQuery);
