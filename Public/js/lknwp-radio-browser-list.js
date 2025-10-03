document.addEventListener("DOMContentLoaded", function () {
    var reverseBtn = document.getElementById("lrt_reverse_btn");
    var reverseInput = document.getElementById("lrt_reverse");
    reverseBtn.addEventListener("click", function () {
        reverseInput.value = reverseInput.value === "1" ? "0" : "1";
        reverseBtn.textContent = reverseInput.value === "1" ? (lknwpRadioTextsList ? lknwpRadioTextsList.reverseActive : 'Reverse active') : (lknwpRadioTextsList ? lknwpRadioTextsList.reverseInactive : 'Reverse inactive');
    });

    // Função de consulta automática (debounced)
    function autoQueryRadios() {
        clearTimeout(autoQueryRadios.timeout);
        autoQueryRadios.timeout = setTimeout(function () {
            var searchInput = document.getElementById("lrt_radio_search");
            var query = searchInput ? searchInput.value.trim() : "";
            var countrycode = document.getElementById("lrt_countrycode").value;
            var limit = document.getElementById("lrt_limit").value || 20;
            var sort = document.getElementById("lrt_sort").value || "clickcount";
            var reverse = document.getElementById("lrt_reverse").value || "1";
            var radioList = document.querySelector(".lrt-radio-list");
            if (!radioList) return;
            var servers = [
                "https://de2.api.radio-browser.info",
                "https://fi1.api.radio-browser.info",
                "https://fr1.api.radio-browser.info",
                "https://nl1.api.radio-browser.info"
            ];
            var base_url = servers[Math.floor(Math.random() * servers.length)];
            var api_url;
            if (!query) {
                api_url = base_url + "/json/stations/bycountrycodeexact/" + encodeURIComponent(countrycode) + "?order=" + encodeURIComponent(sort);
                if (reverse === "1") {
                    api_url += "&orderDirection=desc";
                } else {
                    api_url += "&orderDirection=asc";
                }
                api_url += "&limit=" + encodeURIComponent(limit);
            } else {
                api_url = base_url + "/json/stations/search?name=" + encodeURIComponent(query) + "&countrycode=" + encodeURIComponent(countrycode) + "&order=" + encodeURIComponent(sort) + "&limit=" + encodeURIComponent(limit);
                if (reverse === "1") {
                    api_url += "&orderDirection=desc";
                } else {
                    api_url += "&orderDirection=asc";
                }
            }

            // Mostrar loading
            radioList.innerHTML = '<li class="lrt-radio-loading"><div class="lrt-loading-spinner"></div><span>' + (lknwpRadioTextsList ? lknwpRadioTextsList.loadingRadios : 'Loading radios...') + '</span></li>';
            if (searchInput) searchInput.disabled = true;

            fetch(api_url, {
                headers: { 'User-Agent': 'lknwp-radio-browser/1.0' }
            })
                .then(function (response) { return response.json(); })
                .then(function (stations) {

                    if (searchInput) searchInput.disabled = false;
                    if (!Array.isArray(stations) || stations.length === 0) {
                        radioList.innerHTML = '<li class="lrt-radio-error">' + (lknwpRadioTextsList ? lknwpRadioTextsList.noRadiosFound : 'No radios found.') + '</li>';
                        return;
                    }
                    radioList.innerHTML = "";
                    stations.forEach(function (station) {
                        var name = station.name ? station.name : "";
                        var stream = station.url_resolved ? station.url_resolved : "";
                        var img = station.favicon ? station.favicon : "";
                        var default_img_url = window.LKNWP_RADIO_BROWSER_PLUGIN_URL + "Includes/assets/images/default-radio.png";
                        if (!img) img = default_img_url;
                        var player_page = window.LKNWP_PLAYER_PAGE_SLUG || "player";
                        // Gerar URL amigável - deixa o navegador formatar
                        var radio_name_clean = name.replace(/[\/\?#&]/g, ''); // Remove apenas caracteres problemáticos
                        var radio_name_encoded = radio_name_clean.replace(/ /g, '%20');
                        var player_url = window.location.origin + "/" + player_page + "/" + radio_name_encoded + "/";

                        var li = document.createElement("li");
                        li.className = "lrt-radio-station";

                        var link = document.createElement("a");
                        link.className = "lrt-radio-station__link";
                        link.href = player_url;
                        link.setAttribute("data-player-link", "1");
                        link.target = "_blank";

                        var imgEl = document.createElement("img");
                        imgEl.className = "lrt-radio-station__logo";
                        imgEl.src = img;
                        imgEl.alt = "Logo";
                        imgEl.onerror = function () {
                            this.onerror = null;
                            if (!this.src.endsWith(default_img_url)) {
                                this.src = default_img_url;
                            }
                        };

                        var div = document.createElement("div");
                        div.className = "lrt-radio-station__content";

                        var span = document.createElement("span");
                        span.className = "lrt-radio-station__name";
                        span.textContent = name;

                        div.appendChild(span);
                        link.appendChild(imgEl);
                        link.appendChild(div);
                        li.appendChild(link);
                        radioList.appendChild(li);
                    });
                })
                .catch(function (error) {
                    // Mostrar loading para tentativa de fallback
                    radioList.innerHTML = '<li class="lrt-radio-loading"><div class="lrt-loading-spinner"></div><span>' + (lknwpRadioTextsList ? lknwpRadioTextsList.tryingAlternativeServers : 'Trying alternative servers...') + '</span></li>';

                    (async function () {
                        var servers = [
                            "https://de2.api.radio-browser.info",
                            "https://fi1.api.radio-browser.info",
                            "https://fr1.api.radio-browser.info",
                            "https://nl1.api.radio-browser.info"
                        ];
                        let found = false;
                        for (let i = 0; i < servers.length; i++) {
                            let nextApiUrl = api_url.replace(/https:\/\/[^/]+/, servers[i]);
                            try {
                                let response = await fetch(nextApiUrl, {
                                    headers: { 'User-Agent': 'lknwp-radio-browser/1.0' }
                                });
                                if (!response.ok) continue;
                                let stations = await response.json();
                                if (searchInput) searchInput.disabled = false;
                                if (!Array.isArray(stations) || stations.length === 0) {
                                    radioList.innerHTML = '<li class="lrt-radio-error">' + (lknwpRadioTextsList ? lknwpRadioTextsList.noRadiosFound : 'No radios found.') + '</li>';
                                    return;
                                }
                                radioList.innerHTML = "";
                                stations.forEach(function (station) {
                                    var name = station.name ? station.name : "";
                                    var stream = station.url_resolved ? station.url_resolved : "";
                                    var img = station.favicon ? station.favicon : "";
                                    var default_img_url = window.LKNWP_RADIO_BROWSER_PLUGIN_URL + "Includes/assets/images/default-radio.png";
                                    if (!img) img = default_img_url;
                                    var player_page = window.LKNWP_PLAYER_PAGE_SLUG || "player";
                                    // Gerar URL amigável - deixa o navegador formatar
                                    var radio_name_clean = name.replace(/[\/\?#&]/g, ''); // Remove apenas caracteres problemáticos
                                    var radio_name_encoded = radio_name_clean.replace(/ /g, '%20');
                                    var player_url = window.location.origin + "/" + player_page + "/" + radio_name_encoded + "/";

                                    var li = document.createElement("li");
                                    li.className = "lrt-radio-station";

                                    var link = document.createElement("a");
                                    link.className = "lrt-radio-station__link";
                                    link.href = player_url;
                                    link.setAttribute("data-player-link", "1");
                                    link.target = "_blank";

                                    var imgEl = document.createElement("img");
                                    imgEl.className = "lrt-radio-station__logo";
                                    imgEl.src = img;
                                    imgEl.alt = "Logo";
                                    imgEl.onerror = function () {
                                        this.onerror = null;
                                        if (!this.src.endsWith(default_img_url)) {
                                            this.src = default_img_url;
                                        }
                                    };

                                    var div = document.createElement("div");
                                    div.className = "lrt-radio-station__content";

                                    var span = document.createElement("span");
                                    span.className = "lrt-radio-station__name";
                                    span.textContent = name;

                                    div.appendChild(span);
                                    link.appendChild(imgEl);
                                    link.appendChild(div);
                                    li.appendChild(link);
                                    radioList.appendChild(li);
                                });
                                found = true;
                                break;
                            } catch (e) {
                                // continua para o próximo servidor
                            }
                        }
                        if (!found) {
                            if (searchInput) searchInput.disabled = false;
                            radioList.innerHTML = '<li class="lrt-radio-error">' + (lknwpRadioTextsList ? lknwpRadioTextsList.apiError : 'Error querying API.') + '</li>';
                        }
                    })();
                });
        }, 2000);
    }
    // Eventos para todos os campos
    var searchInput = document.getElementById("lrt_radio_search");
    var countryInput = document.getElementById("lrt_countrycode");
    var limitInput = document.getElementById("lrt_limit");
    var sortSelect = document.getElementById("lrt_sort");
    var reverseBtn = document.getElementById("lrt_reverse_btn");
    if (searchInput) searchInput.addEventListener("input", autoQueryRadios);
    if (countryInput) countryInput.addEventListener("input", autoQueryRadios);
    if (limitInput) limitInput.addEventListener("input", autoQueryRadios);
    if (sortSelect) sortSelect.addEventListener("change", autoQueryRadios);
    if (reverseBtn) reverseBtn.addEventListener("click", autoQueryRadios);
});