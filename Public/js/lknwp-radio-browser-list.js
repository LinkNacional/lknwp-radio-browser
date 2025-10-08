// Arquivo base para o webpack, qualquer mudança necessário executar o webpack para aplica-la.

import 'select2/dist/js/select2.min.js';
import 'select2/dist/css/select2.min.css';

(function ($) {

    $(document).ready(function () {
        // Anula o envio do formulário ao pressionar Enter
        $('.lrt-radio-form').on('keydown', function (e) {
            // Se Enter for pressionado em um input ou select
            if (e.key === 'Enter') {
                // Opcional: permite submit se for um textarea
                if (e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    return false;
                }
            }
        });
        var $reverseBtn = $('#lrt_reverse_btn');
        var $reverseInput = $('#lrt_reverse');
        var $lrt_player_base_url = $('#lrt_player_base_url');
        let playerBaseUrl = 'player';
        if ($lrt_player_base_url.length) {
            playerBaseUrl = atob($lrt_player_base_url.val());
        }
        $reverseBtn.on('click', function () {
            var val = $reverseInput.val() === "1" ? "0" : "1";
            $reverseInput.val(val);
            $reverseBtn.text(
                val === "1"
                    ? (window.lknwpRadioTextsList && window.lknwpRadioTextsList.descending ? window.lknwpRadioTextsList.descending : 'Descending')
                    : (window.lknwpRadioTextsList && window.lknwpRadioTextsList.ascending ? window.lknwpRadioTextsList.ascending : 'Ascending')
            );
        });

        // Função de consulta automática (debounced)
        function autoQueryRadios() {
            clearTimeout(autoQueryRadios.timeout);
            autoQueryRadios.timeout = setTimeout(function () {
                var $searchInput = $('#lrt_radio_search');
                var query = $searchInput.length ? $searchInput.val().trim() : "";
                var countrycode = $('#lrt_countrycode').val();
                var limit = $('#lrt_limit').val() || 20;
                var sort = $('#lrt_sort').val() || "clickcount";
                var reverse = $('#lrt_reverse').val() || "1";
                var genre = $('#lrt_genre').val() || "";

                var $radioList = $('.lrt-radio-list');
                if (!$radioList.length) return;
                var servers = [
                    "https://de2.api.radio-browser.info",
                    "https://fi1.api.radio-browser.info",
                    "https://fr1.api.radio-browser.info",
                    "https://nl1.api.radio-browser.info"
                ];
                var base_url = servers[Math.floor(Math.random() * servers.length)];
                // Sempre usa /search, incluindo todos os parâmetros
                var api_url = base_url + "/json/stations/search?name=" + encodeURIComponent(query) + "&countrycode=" + encodeURIComponent(countrycode) + "&order=" + encodeURIComponent(sort) + "&limit=" + encodeURIComponent(limit) + "&hidebroken=true";
                if (reverse === "1") {
                    api_url += "&reverse=true";
                }
                // Sempre inclui tagList, mesmo vazio ou 'all'
                if (!genre || genre === 'all') {
                    api_url += "&tagList=";
                } else {
                    api_url += "&tagList=" + encodeURIComponent(genre);
                }

                // Mostrar loading
                $radioList.html('<li class="lrt-radio-loading"><div class="lrt-loading-spinner"></div><span>' + (window.lknwpRadioTextsList ? window.lknwpRadioTextsList.loadingRadios : 'Loading radios...') + '</span></li>');
                if ($searchInput.length) $searchInput.prop('disabled', true);

                fetch(api_url, {
                    headers: { 'User-Agent': 'lknwp-radio-browser/1.0' }
                })
                    .then(function (response) { return response.json(); })
                    .then(function (stations) {
                        if ($searchInput.length) $searchInput.prop('disabled', false);
                        if (!Array.isArray(stations) || stations.length === 0) {
                            $radioList.html('<li class="lrt-radio-error">' + (window.lknwpRadioTextsList ? window.lknwpRadioTextsList.noRadiosFound : 'No radios found.') + '</li>');
                            return;
                        }
                        $radioList.html("");
                        stations.forEach(function (station) {
                            var name = station.name ? station.name : "";
                            var stream = station.url_resolved ? station.url_resolved : "";
                            var img = station.favicon ? station.favicon : "";
                            var default_img_url = (window.lknwpRadioTextsList && window.lknwpRadioTextsList.defaultImgUrl) ? window.lknwpRadioTextsList.defaultImgUrl : "";
                            if (!img) img = default_img_url;
                            var radio_name_clean = name.replace(/[\/\?#&]/g, '');
                            var radio_name_encoded = radio_name_clean.replace(/ /g, '%20');
                            var player_url = playerBaseUrl + radio_name_encoded + "/";

                            var $li = $('<li>').addClass('lrt-radio-station');
                            var $link = $('<a>').addClass('lrt-radio-station__link').attr({
                                href: player_url,
                                'data-player-link': '1',
                                target: '_blank'
                            });
                            var $imgEl = $('<img>').addClass('lrt-radio-station__logo').attr({
                                src: img,
                                alt: 'Logo'
                            }).on('error', function () {
                                if (!$(this).attr('src').endsWith(default_img_url)) {
                                    $(this).attr('src', default_img_url);
                                }
                            });
                            var $div = $('<div>').addClass('lrt-radio-station__content');
                            var $span = $('<span>').addClass('lrt-radio-station__name').text(name);
                            $div.append($span);
                            $link.append($imgEl).append($div);
                            $li.append($link);
                            $radioList.append($li);
                        });
                    })
                    .catch(function (error) {
                        $radioList.html('<li class="lrt-radio-loading"><div class="lrt-loading-spinner"></div><span>' + (window.lknwpRadioTextsList ? window.lknwpRadioTextsList.tryingAlternativeServers : 'Trying alternative servers...') + '</span></li>');
                        var servers = [
                            "https://de2.api.radio-browser.info",
                            "https://fi1.api.radio-browser.info",
                            "https://fr1.api.radio-browser.info",
                            "https://nl1.api.radio-browser.info"
                        ];
                        var found = false;
                        (async function () {
                            for (var i = 0; i < servers.length; i++) {
                                var nextApiUrl = api_url.replace(/https:\/\/[^/]+/, servers[i]);
                                try {
                                    var response = await fetch(nextApiUrl, {
                                        headers: { 'User-Agent': 'lknwp-radio-browser/1.0' }
                                    });
                                    if (!response.ok) continue;
                                    var stations = await response.json();
                                    if ($searchInput.length) $searchInput.prop('disabled', false);
                                    if (!Array.isArray(stations) || stations.length === 0) {
                                        $radioList.html('<li class="lrt-radio-error">' + (window.lknwpRadioTextsList ? window.lknwpRadioTextsList.noRadiosFound : 'No radios found.') + '</li>');
                                        return;
                                    }
                                    $radioList.html("");
                                    stations.forEach(function (station) {
                                        var name = station.name ? station.name : "";
                                        var stream = station.url_resolved ? station.url_resolved : "";
                                        var img = station.favicon ? station.favicon : "";
                                        var default_img_url = (window.lknwpRadioTextsList && window.lknwpRadioTextsList.defaultImgUrl) ? window.lknwpRadioTextsList.defaultImgUrl : "";
                                        if (!img) img = default_img_url;
                                        var radio_name_clean = name.replace(/[\/\?#&]/g, '');
                                        var radio_name_encoded = radio_name_clean.replace(/ /g, '%20');
                                        var player_url = playerBaseUrl + radio_name_encoded + "/";

                                        var $li = $('<li>').addClass('lrt-radio-station');
                                        var $link = $('<a>').addClass('lrt-radio-station__link').attr({
                                            href: player_url,
                                            'data-player-link': '1',
                                            target: '_blank'
                                        });
                                        var $imgEl = $('<img>').addClass('lrt-radio-station__logo').attr({
                                            src: img,
                                            alt: 'Logo'
                                        }).on('error', function () {
                                            if (!$(this).attr('src').endsWith(default_img_url)) {
                                                $(this).attr('src', default_img_url);
                                            }
                                        });
                                        var $div = $('<div>').addClass('lrt-radio-station__content');
                                        var $span = $('<span>').addClass('lrt-radio-station__name').text(name);
                                        $div.append($span);
                                        $link.append($imgEl).append($div);
                                        $li.append($link);
                                        $radioList.append($li);
                                    });
                                    found = true;
                                    break;
                                } catch (e) {
                                    // continua para o próximo servidor
                                }
                            }
                            if (!found) {
                                if ($searchInput.length) $searchInput.prop('disabled', false);
                                $radioList.html('<li class="lrt-radio-error">' + (window.lknwpRadioTextsList ? window.lknwpRadioTextsList.apiError : 'Error querying API.') + '</li>');
                            }
                        })();
                    });
            }, 2000);
        }
        // Eventos para todos os campos
        $('#lrt_radio_search').on('input', autoQueryRadios);
        $('#lrt_countrycode').on('input', autoQueryRadios);
        $('#lrt_limit').on('input', autoQueryRadios);
        $('#lrt_sort').on('change', autoQueryRadios);
        $('#lrt_reverse_btn').on('click', autoQueryRadios);
        $('#lrt_genre').on('change', autoQueryRadios);

        // Inicializa Select2
        $('#lrt_genre').select2({
            placeholder: window.lknwpRadioTextsList,
            allowClear: false,
            width: 'resolve'
        });
    });

})(jQuery);