<?php
/**
 * Template for Radio Browser List Shortcode (premium discovery UI)
 *
 * Variables available:
 * - $atts: Shortcode attributes
 * - $stations: Array of radio stations
 * - $sort_options: Array of sort options
 * - $plugin_url: Plugin URL
 * - $default_img_url: Default image URL
 * - $player_base_url: Base URL of the player page
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="lrt-radio-wrap" id="lknwp-radio-list">
    <div class="lrt-radio-shell">

        <!-- ===================== SIDEBAR ===================== -->
        <aside class="lrt-sidebar">
            <div class="lrt-sidebar__brand">
                <span class="lrt-sidebar__logo" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 13a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><rect x="2.5" y="12.5" width="4.5" height="8" rx="2" fill="currentColor"/><rect x="17" y="12.5" width="4.5" height="8" rx="2" fill="currentColor"/></svg>
                </span>
                <span class="lrt-sidebar__name"><?php esc_html_e( 'Radio', 'lknwp-radio-browser' ); ?></span>
            </div>

            <nav class="lrt-sidebar__menu" aria-label="<?php esc_attr_e( 'Menu', 'lknwp-radio-browser' ); ?>">
                <button type="button" class="lrt-side-item is-active" data-view="discover">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="m15.5 8.5-2 5-5 2 2-5 5-2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                    <span><?php esc_html_e( 'Descobrir', 'lknwp-radio-browser' ); ?></span>
                </button>
                <button type="button" class="lrt-side-item" data-view="favorites">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 20s-7-4.5-9.3-8.4C1 8.6 2.6 5 6.1 5c2 0 3.3 1.1 3.9 2 .6-.9 1.9-2 3.9-2C17.4 5 19 8.6 21.3 11.6 19 15.5 12 20 12 20Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                    <span><?php esc_html_e( 'Favoritos', 'lknwp-radio-browser' ); ?></span>
                </button>
                <button type="button" class="lrt-side-item" data-view="recent">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span><?php esc_html_e( 'Recentes', 'lknwp-radio-browser' ); ?></span>
                </button>
            </nav>

            <hr class="lrt-sidebar__hr">

            <div class="lrt-sidebar__label"><?php esc_html_e( 'Navegar por', 'lknwp-radio-browser' ); ?></div>
            <nav class="lrt-sidebar__nav" aria-label="<?php esc_attr_e( 'Navegar por', 'lknwp-radio-browser' ); ?>">
                <button type="button" class="lrt-side-nav-item" data-nav="genre">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 18V6l10-2v12" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><circle cx="6.5" cy="18" r="2.5" stroke="currentColor" stroke-width="2"/><circle cx="16.5" cy="16" r="2.5" stroke="currentColor" stroke-width="2"/></svg>
                    <span><?php esc_html_e( 'Gêneros', 'lknwp-radio-browser' ); ?></span>
                </button>
                <button type="button" class="lrt-side-nav-item" data-nav="country">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M3 12h18M12 3c2.5 2.5 3.5 6 3.5 9S14.5 18.5 12 21c-2.5-2.5-3.5-6-3.5-9S9.5 5.5 12 3Z" stroke="currentColor" stroke-width="2"/></svg>
                    <span><?php esc_html_e( 'Países', 'lknwp-radio-browser' ); ?></span>
                </button>
                <button type="button" class="lrt-side-nav-item" data-nav="language">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 5h16v11H9l-4 4V5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                    <span><?php esc_html_e( 'Línguas', 'lknwp-radio-browser' ); ?></span>
                </button>
            </nav>

            <div class="lrt-sidebar__footer">
                <div class="lrt-sidebar__wave" aria-hidden="true">
                    <i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i>
                </div>
                <p class="lrt-sidebar__tagline"><?php esc_html_e( 'Milhares de rádios,', 'lknwp-radio-browser' ); ?><br><?php esc_html_e( 'um só lugar.', 'lknwp-radio-browser' ); ?></p>
            </div>
        </aside>

        <!-- ===================== MAIN ===================== -->
        <main class="lrt-main">

            <header class="lrt-radio-head">
                <?php if ($atts['hide_all_filters'] !== 'yes'): ?>
                <form method="get" class="lrt-radio-form" action="#lknwp-radio-list">
                    <?php wp_nonce_field('lknwp_radio_list_action', 'lknwp_radio_list_nonce'); ?>

                    <!-- Busca + país + Filtros (mesma linha) -->
                    <div class="lrt-toolbar">
                        <?php if ($atts['hide_search'] !== 'yes'): ?>
                        <div class="lrt-search">
                            <span class="lrt-search__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </span>
                            <input type="text" id="lrt_radio_search" name="lrt_radio_search" value="<?php echo esc_attr($atts['search']); ?>" placeholder="<?php esc_attr_e( 'Buscar estações, gêneros, artistas ou cidades...', 'lknwp-radio-browser' ); ?>" class="lrt-search__input" autocomplete="off">
                        </div>
                        <?php endif; ?>

                        <div class="lrt-toolbar__right">
                            <?php if ($atts['hide_country'] !== 'yes'): ?>
                            <div class="lrt-pill-select lrt-pill-select--country">
                                <select id="lrt_countrycode" name="lrt_countrycode" class="lrt-radio-select">
                                    <?php
                                    $countries = array_merge(
                                        array('all' => '🌍 ' . __( 'All Countries', 'lknwp-radio-browser' )),
                                        array(
                                            'BR' => '🇧🇷 BR', 'US' => '🇺🇸 US', 'AR' => '🇦🇷 AR', 'CA' => '🇨🇦 CA',
                                            'GB' => '🇬🇧 GB', 'FR' => '🇫🇷 FR', 'DE' => '🇩🇪 DE', 'ES' => '🇪🇸 ES',
                                            'IT' => '🇮🇹 IT', 'PT' => '🇵🇹 PT', 'MX' => '🇲🇽 MX', 'CL' => '🇨🇱 CL',
                                            'CO' => '🇨🇴 CO', 'PE' => '🇵🇪 PE', 'UY' => '🇺🇾 UY', 'PY' => '🇵🇾 PY',
                                            'BO' => '🇧🇴 BO', 'EC' => '🇪🇨 EC', 'VE' => '🇻🇪 VE', 'AU' => '🇦🇺 AU',
                                            'JP' => '🇯🇵 JP', 'KR' => '🇰🇷 KR', 'CN' => '🇨🇳 CN', 'IN' => '🇮🇳 IN',
                                            'RU' => '🇷🇺 RU', 'NL' => '🇳🇱 NL', 'BE' => '🇧🇪 BE', 'CH' => '🇨🇭 CH',
                                            'AT' => '🇦🇹 AT', 'SE' => '🇸🇪 SE', 'NO' => '🇳🇴 NO', 'DK' => '🇩🇰 DK',
                                            'FI' => '🇫🇮 FI'
                                        )
                                    );
                                    $selected_country = $atts['countrycode'];
                                    if (empty($selected_country)) {
                                        $selected_country = 'BR';
                                    }
                                    ?>
                                    <?php foreach ($countries as $code => $name): ?>
                                        <option value="<?php echo esc_attr($code); ?>" <?php echo $selected_country === $code ? esc_attr('selected') : ''; ?>>
                                            <?php echo esc_html($name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>

                            <button type="button" id="lrt_filters_btn" class="lrt-btn lrt-btn--filters" aria-expanded="false" aria-controls="lrt_advanced_filters">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 6h16M7 12h10M10 18h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                <span><?php esc_html_e( 'Filtros', 'lknwp-radio-browser' ); ?></span>
                                <svg class="lrt-btn__chevron" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><polyline points="6,9 12,15 18,9" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Painel avançado (aberto pelo botão Filtros) -->
                    <div class="lrt-advanced" id="lrt_advanced_filters" hidden>
                        <?php if ($atts['hide_genre'] !== 'yes'): ?>
                        <div class="lrt-pill-select lrt-pill-select--genre">
                            <select id="lrt_genre" name="lrt_genre" class="lrt-radio-select">
                                <?php
                                $active_genre = '';
                                if (!empty($atts['genre']) && $atts['genre'] !== 'all') {
                                    $active_genre = $atts['genre'];
                                }
                                // phpcs:ignore WordPress.Security.NonceVerification.Recommended --
                                if (isset($_GET['lrt_genre']) && $_GET['lrt_genre'] !== 'all') {
                                    $active_genre = sanitize_text_field(wp_unslash($_GET['lrt_genre']));
                                }
                                ?>
                                <option value="all" <?php echo $active_genre === '' ? esc_attr('selected') : ''; ?>><?php esc_html_e( 'All Genres', 'lknwp-radio-browser' ); ?></option>
                                <?php
                                $tags = get_transient('lknwp_radio_tags');
                                if ($tags === false) {
                                    $response = wp_remote_get('https://de2.api.radio-browser.info/json/tags', array('timeout' => 10));
                                    if (!is_wp_error($response)) {
                                        $tags = json_decode(wp_remote_retrieve_body($response));
                                        if (is_array($tags)) {
                                            set_transient('lknwp_radio_tags', $tags, 12 * HOUR_IN_SECONDS);
                                        }
                                    }
                                }
                                if (is_array($tags)) {
                                    foreach ($tags as $tag) {
                                        if (!empty($tag->name)) {
                                            $selected = ($active_genre === $tag->name) ? 'selected' : '';
                                            echo '<option value="' . esc_attr($tag->name) . '" ' . esc_attr($selected) . '>' . esc_html($tag->name) . ' (' . esc_html($tag->stationcount) . ')</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div class="lrt-pill-select lrt-pill-select--language">
                            <select id="lrt_language" name="lrt_language" class="lrt-radio-select">
                                <option value=""><?php esc_html_e( 'All Languages', 'lknwp-radio-browser' ); ?></option>
                                <option value="portuguese">Português</option>
                                <option value="english">English</option>
                                <option value="spanish">Español</option>
                                <option value="french">Français</option>
                                <option value="german">Deutsch</option>
                                <option value="italian">Italiano</option>
                                <option value="japanese">日本語</option>
                            </select>
                        </div>

                        <?php if ($atts['hide_sort'] !== 'yes'): ?>
                        <div class="lrt-pill-select lrt-pill-select--sort">
                            <select id="lrt_sort" name="lrt_sort" class="lrt-radio-select">
                                <?php foreach ($sort_options as $key => $label): ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php echo $atts['sort'] === $key ? esc_attr('selected') : ''; ?>>
                                        <?php echo esc_html($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <?php if ($atts['hide_order'] !== 'yes'): ?>
                        <button type="button" id="lrt_reverse_btn" class="lrt-btn lrt-btn--ghost">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 4v16M7 4 3 8m4-4 4 4M17 20V4m0 16 4-4m-4 4-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span class="lrt-reverse-label"><?php echo $atts['reverse'] === '1'
                                ? esc_html__('Maior', 'lknwp-radio-browser')
                                : esc_html__('Menor', 'lknwp-radio-browser'); ?></span>
                        </button>
                        <?php endif; ?>
                    </div>

                    <?php if ($atts['hide_limit'] !== 'yes'): ?>
                    <input type="hidden" id="lrt_limit" name="lrt_limit" value="<?php echo esc_attr($atts['limit']); ?>">
                    <?php endif; ?>

                    <!-- Tocando agora (detalhes da estação selecionada) -->
                    <section class="lrt-now" id="lrt_now_playing" hidden aria-live="polite">
                        <div class="lrt-now__card">
                            <!-- Ícone decorativo de fundo (gênero da rádio) -->
                            <div class="lrt-now__bg" id="lrt_now_bg" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 13a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><rect x="2.5" y="12.5" width="4.5" height="8" rx="2" fill="currentColor"/><rect x="17" y="12.5" width="4.5" height="8" rx="2" fill="currentColor"/></svg>
                            </div>
                            <div class="lrt-now__body">
                                <span class="lrt-now__live">
                                    <span class="lrt-now__dot" aria-hidden="true"></span>
                                    <svg class="lrt-now__live-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4.9 19.1a10 10 0 0 1 0-14.2"/><path d="M7.8 16.2a6 6 0 0 1 0-8.4"/><circle cx="12" cy="12" r="2"/><path d="M16.2 7.8a6 6 0 0 1 0 8.4"/><path d="M19.1 4.9a10 10 0 0 1 0 14.2"/></svg>
                                    <span><?php esc_html_e( 'Agora tocando', 'lknwp-radio-browser' ); ?></span>
                                </span>

                                <!-- Bloco da imagem + título + pílulas -->
                                <div class="lrt-now__main">
                                    <div class="lrt-now__cover">
                                        <img id="lrt_now_img" class="lrt-now__logo" src="" alt="" loading="lazy">
                                    </div>
                                    <div class="lrt-now__info">
                                        <span class="lrt-now__name" id="lrt_now_name"></span>
                                        <div class="lrt-now__meta">
                                            <span class="lrt-now__pill lrt-now__genre" id="lrt_now_genre" hidden></span>
                                            <span class="lrt-now__pill lrt-now__country" id="lrt_now_country" hidden>
                                                <span class="lrt-flag" id="lrt_now_flag" data-cc=""></span>
                                                <span id="lrt_now_country_text"></span>
                                            </span>
                                            <span class="lrt-now__pill lrt-now__song" id="lrt_now_song" hidden>
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                                                <span id="lrt_now_song_text"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Onda de som (estática, subindo de forma gradual) -->
                                <div class="lrt-now__wave" aria-hidden="true">
                                    <?php
                                    $lknwp_wave = array(12, 20, 16, 27, 23, 34, 30, 41, 37, 48, 44, 55, 51, 62, 58, 69, 65, 76, 72, 83, 79, 90, 86, 97, 93, 100, 88, 95, 82, 91);
                                    foreach ($lknwp_wave as $lknwp_h) {
                                        echo '<i style="height:' . intval($lknwp_h) . '%"></i>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="lrt-now__actions">
                                <button type="button" class="lrt-now__fav" id="lrt_now_fav" aria-label="<?php esc_attr_e( 'Favoritar', 'lknwp-radio-browser' ); ?>" title="<?php esc_attr_e( 'Favoritar', 'lknwp-radio-browser' ); ?>">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 20s-7-4.5-9.3-8.4C1 8.6 2.6 5 6.1 5c2 0 3.3 1.1 3.9 2 .6-.9 1.9-2 3.9-2C17.4 5 19 8.6 21.3 11.6 19 15.5 12 20 12 20Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                                    <span><?php esc_html_e( 'Favoritar', 'lknwp-radio-browser' ); ?></span>
                                </button>
                                <a class="lrt-now__open" id="lrt_now_open" href="#" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Abrir player em nova aba', 'lknwp-radio-browser' ); ?>" title="<?php esc_attr_e( 'Abrir player em nova aba', 'lknwp-radio-browser' ); ?>">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M15 3h6v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 14 21 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                            </div>

                            <!-- Overlay de carregamento (aparece quando a lista está atualizando) -->
                            <div class="lrt-now__loading" aria-hidden="true">
                                <span class="lrt-loader" role="status"><i></i><i></i><i></i><i></i><i></i></span>
                            </div>
                        </div>
                    </section>

                    <!-- Continue ouvindo (rádios recentes do usuário, via localStorage) -->
                    <section class="lrt-continue" id="lrt_continue" hidden aria-live="polite">
                        <div class="lrt-continue__head">
                            <svg class="lrt-continue__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l3 2"/></svg>
                            <span><?php esc_html_e( 'Continue ouvindo', 'lknwp-radio-browser' ); ?></span>
                        </div>
                        <div class="lrt-continue__track" id="lrt_continue_track"></div>
                    </section>

                    <!-- Pills de categoria (recursos da API: tags) -->
                    <div class="lrt-pills" id="lrt_tag_pills">
                        <button type="button" class="lrt-chip is-active" data-tag="">
                            <svg class="lrt-chip__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                            <span><?php esc_html_e( 'Todos', 'lknwp-radio-browser' ); ?></span>
                        </button>
                        <button type="button" class="lrt-chip" data-tag="rock">
                            <svg class="lrt-chip__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                            <span>Rock</span>
                        </button>
                        <button type="button" class="lrt-chip" data-tag="mpb">
                            <svg class="lrt-chip__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                            <span>MPB</span>
                        </button>
                        <button type="button" class="lrt-chip" data-tag="electronic">
                            <svg class="lrt-chip__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                            <span>Eletrônica</span>
                        </button>
                        <button type="button" class="lrt-chip" data-tag="sertanejo">
                            <svg class="lrt-chip__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m11.9 12.1 4.51-4.51"/><path d="M20.1 2.3a1 1 0 0 0-1.4 0l-1.12 1.11A2 2 0 0 0 17 4.83v1.34a2 2 0 0 1-.59 1.42"/><path d="m6 16 2 2"/><path d="M8.23 9.85A3 3 0 0 1 11 11a3 3 0 0 1 2.9 1.7 6 6 0 0 1-1.34 6.19l-1.34 1.35a3 3 0 0 1-1.3.79l-.7.2-3.05.6-.89-.89-.6-3.05.2-.7a3 3 0 0 1 .79-1.3l1.35-1.34A6 6 0 0 1 8.23 9.85Z"/></svg>
                            <span>Sertanejo</span>
                        </button>
                        <button type="button" class="lrt-chip" data-tag="pop">
                            <svg class="lrt-chip__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            <span>Pop</span>
                        </button>
                        <button type="button" class="lrt-chip" data-tag="jazz">
                            <svg class="lrt-chip__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M6 12c0-1.7.7-3.2 1.8-4.2"/><circle cx="12" cy="12" r="2"/></svg>
                            <span>Jazz</span>
                        </button>
                        <button type="button" class="lrt-chip" data-tag="news">
                            <svg class="lrt-chip__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
                            <span>Notícias</span>
                        </button>
                    </div>

                    <input type="hidden" id="lrt_tag" name="lrt_tag" value="">
                    <input type="hidden" id="lrt_reverse" name="lrt_reverse" value="<?php echo esc_attr($atts['reverse']); ?>">
                    <input type="hidden" id="lrt_player_base_url" name="lrt_player_base_url" value="<?php echo esc_attr(base64_encode($player_base_url)); ?>">
                </form>
                <?php endif; ?>
            </header>

            <!-- Radio Stations List (com scroll próprio para não crescer demais) -->
            <div class="lrt-list-scroll">
                <ul class="lrt-radio-list" id="lknwp-radio-list-components">
                <?php if (!$stations || !is_array($stations) || count($stations) === 0): ?>
                    <li class="lrt-radio-error"><?php esc_html_e('No radios found.', 'lknwp-radio-browser'); ?></li>
                <?php else: ?>
                    <?php
                    $count = 0;
                    foreach ($stations as $station) {
                        if ($count >= $atts['limit']) break;
                        $name = isset($station->name) ? $station->name : '';
                        $img = !empty($station->favicon) ? $station->favicon : $default_img_url;
                        $radio_name_clean = str_replace(['/', '?', '#', '&'], '', $name);
                        $radio_name_encoded = str_replace(' ', '%20', $radio_name_clean);
                        $player_url = trailingslashit($player_base_url) . $radio_name_encoded . '/';

                        $station_genre = '';
                        if (!empty($station->tags)) {
                            $tags_arr = explode(',', $station->tags);
                            $station_genre = trim($tags_arr[0]);
                        }
                        $cc_raw = isset($station->countrycode) ? (string) $station->countrycode : '';
                        $station_cc = ($cc_raw !== '') ? strtoupper($cc_raw) : '';
                        $station_country = !empty($station->country) ? $station->country : $station_cc;
                        $station_stream = '';
                        if (!empty($station->url_resolved)) {
                            $station_stream = $station->url_resolved;
                        } elseif (!empty($station->url)) {
                            $station_stream = $station->url;
                        }
                        $station_uuid = isset($station->stationuuid) ? $station->stationuuid : '';
                        $count++;
                    ?>
                        <li class="lrt-radio-station" data-uuid="<?php echo esc_attr($station_uuid); ?>" data-stream="<?php echo esc_url($station_stream); ?>">
                            <a href="<?php echo esc_url($player_url); ?>" data-player-link="1" target="_blank" rel="noopener" class="lrt-radio-station__link">
                                <span class="lrt-radio-station__cover">
                                    <img src="<?php echo esc_url($img); ?>" alt="<?php esc_attr_e( 'Radio logo', 'lknwp-radio-browser' ); ?>" class="lrt-radio-station__logo" loading="lazy" onerror="this.onerror=null;this.src='<?php echo esc_url($default_img_url); ?>';">
                                </span>
                                <span class="lrt-radio-station__body">
                                    <span class="lrt-radio-station__name"><?php echo esc_html($name); ?></span>
                                    <?php if ($station_genre !== ''): ?>
                                        <span class="lrt-radio-station__genre"><?php echo esc_html($station_genre); ?></span>
                                    <?php endif; ?>
                                    <span class="lrt-radio-station__loc">
                                        <?php if ($station_cc !== ''): ?><span class="lrt-flag" data-cc="<?php echo esc_attr($station_cc); ?>"></span><?php endif; ?>
                                        <?php echo esc_html($station_country); ?>
                                    </span>
                                </span>
                            </a>

                            <button type="button" class="lrt-radio-station__fav" title="<?php esc_attr_e( 'Favoritar', 'lknwp-radio-browser' ); ?>" aria-label="<?php esc_attr_e( 'Favoritar', 'lknwp-radio-browser' ); ?>">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 20s-7-4.5-9.3-8.4C1 8.6 2.6 5 6.1 5c2 0 3.3 1.1 3.9 2 .6-.9 1.9-2 3.9-2C17.4 5 19 8.6 21.3 11.6 19 15.5 12 20 12 20Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                            </button>
                        </li>
                    <?php
                    }
                    endif; ?>
                </ul>
            </div>

            <div class="lrt-empty" id="lrt-empty" hidden>
                <span class="lrt-empty__icon" aria-hidden="true">🎧</span>
                <p class="lrt-empty__text"></p>
            </div>
        </main>
    </div>

    <!-- Barra de play (rodapé do componente) -->
    <div class="lrt-player-dock" id="lrt_player_dock" hidden>
                <div class="lrt-player-dock__inner">
                    <!-- Esquerda: capa + textos -->
                    <div class="lrt-player-dock__meta">
                        <div class="lrt-player-dock__cover">
                            <img id="lrt_dock_img" class="lrt-player-dock__logo" src="" alt="" loading="lazy">
                        </div>
                        <div class="lrt-player-dock__texts">
                            <span class="lrt-player-dock__name" id="lrt_dock_name"></span>
                            <span class="lrt-player-dock__sub" id="lrt_dock_sub"></span>
                        </div>
                    </div>

                    <!-- Coração (favoritar) -->
                    <button type="button" class="lrt-player-dock__fav" id="lrt_dock_fav" aria-label="<?php esc_attr_e( 'Favoritar', 'lknwp-radio-browser' ); ?>" title="<?php esc_attr_e( 'Favoritar', 'lknwp-radio-browser' ); ?>">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 20s-7-4.5-9.3-8.4C1 8.6 2.6 5 6.1 5c2 0 3.3 1.1 3.9 2 .6-.9 1.9-2 3.9-2C17.4 5 19 8.6 21.3 11.6 19 15.5 12 20 12 20Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                    </button>

                    <!-- Controles: voltar / play-pause / avançar -->
                    <div class="lrt-player-dock__controls">
                        <button type="button" class="lrt-player-dock__ctrl" id="lrt_dock_prev" aria-label="<?php esc_attr_e( 'Rádio anterior', 'lknwp-radio-browser' ); ?>" title="<?php esc_attr_e( 'Rádio anterior', 'lknwp-radio-browser' ); ?>">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 6h2v12H6zM19 6v12L9.5 12 19 6Z" fill="currentColor"/></svg>
                        </button>
                        <button type="button" class="lrt-player-dock__play" id="lrt_dock_play" aria-label="<?php esc_attr_e( 'Reproduzir', 'lknwp-radio-browser' ); ?>">
                            <svg class="lrt-dock-icon-play" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M8 5v14l11-7z" fill="currentColor"/></svg>
                            <svg class="lrt-dock-icon-pause" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="7" y="5" width="4" height="14" rx="1.2" fill="currentColor"/><rect x="13" y="5" width="4" height="14" rx="1.2" fill="currentColor"/></svg>
                        </button>
                        <button type="button" class="lrt-player-dock__ctrl" id="lrt_dock_next" aria-label="<?php esc_attr_e( 'Próxima rádio', 'lknwp-radio-browser' ); ?>" title="<?php esc_attr_e( 'Próxima rádio', 'lknwp-radio-browser' ); ?>">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 6h2v12h-2zM5 6v12l9.5-6L5 6Z" fill="currentColor"/></svg>
                        </button>
                    </div>

                    <!-- Áudio + volume -->
                    <div class="lrt-player-dock__audio">
                        <button type="button" class="lrt-player-dock__mute" id="lrt_dock_mute" aria-label="<?php esc_attr_e( 'Silenciar', 'lknwp-radio-browser' ); ?>">
                            <svg class="lrt-dock-vol-on" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11 5 6 9H3v6h3l5 4V5Z" fill="currentColor"/><path d="M15.5 9.5a4 4 0 0 1 0 5M18 7a7 7 0 0 1 0 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            <svg class="lrt-dock-vol-off" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11 5 6 9H3v6h3l5 4V5Z" fill="currentColor"/><path d="m16 9 5 6M21 9l-5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        </button>
                        <input type="range" class="lrt-player-dock__volume" id="lrt_dock_volume" min="0" max="1" step="0.01" value="0.8" aria-label="<?php esc_attr_e( 'Volume', 'lknwp-radio-browser' ); ?>">
                    </div>

                    <!-- Fixar no rodapé da tela -->
                    <button type="button" class="lrt-player-dock__pin" id="lrt_dock_pin" aria-pressed="false" aria-label="<?php esc_attr_e( 'Fixar na tela', 'lknwp-radio-browser' ); ?>" title="<?php esc_attr_e( 'Fixar na tela', 'lknwp-radio-browser' ); ?>">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 3h6l-1 7 4 3v2H6v-2l4-3-1-7Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M12 15v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                </div>
                <audio id="lrt_dock_audio" preload="none"></audio>
    </div>
</div>
