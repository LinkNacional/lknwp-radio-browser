<?php
/**
 * Template for Radio Browser Player Shortcode
 * 
 * Variables available:
 * - $stream: Radio stream URL
 * - $station_name: Station name
 * - $station_img: Station image URL
 * - $default_img_url: Default image URL
 * - $station_homepage, $station_tags, $station_country, $station_cc,
 *   $station_codec, $station_bitrate, $station_votes, $station_clickcount
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<?php if (!$stream): ?>
    <div class="lkp-warning">
        <strong><?php esc_html_e( 'Warning:', 'lknwp-radio-browser' ); ?></strong> <?php esc_html_e( 'No radio stream found. Please select a radio from the list.', 'lknwp-radio-browser' ); ?>
    </div>
<?php else: ?>

<?php
// ---- Helpers de exibição: gênero, ícone, tags e frequência ----
$lknwp_tag_list = array();
if (!empty($station_tags)) {
    $lknwp_tag_list = array_slice(array_values(array_filter(array_map('trim', explode(',', $station_tags)))), 0, 8);
}
$station_genre = !empty($lknwp_tag_list) ? $lknwp_tag_list[0] : '';

// Frequência: a API não tem campo próprio, derivamos de tags/nome (ex.: "100.1 fm").
$station_frequency = '';
if (preg_match('/\b(\d{2,3}[.,]\d)\s*(fm|am|mhz|khz)?\b/i', $station_tags . ' ' . $station_name, $m)) {
    $station_frequency = $m[1] . (!empty($m[2]) ? ' ' . strtoupper($m[2]) : '');
}

// Ícone do gênero (mesmo conjunto dos botões de categoria da lista).
$lknwp_gi = 'radio';
$lknwp_gl = strtolower($station_tags . ' ' . $station_name);
$lknwp_gi_rules = array(
    'electronic' => array('electronic', 'electro', 'eletr', 'dance', 'techno', 'house', 'edm', 'trance', 'club', 'rave'),
    'sertanejo' => array('sertanejo', 'country', 'folk', 'caipira', 'sertao', 'sertão'),
    'mpb' => array('mpb', 'bossa', 'samba', 'choro', 'pagode', 'axe', 'axé'),
    'jazz' => array('jazz', 'soul', 'blues', 'lounge'),
    'rock' => array('rock', 'metal', 'punk', 'grunge', 'indie', 'hardcore', 'emo', 'alternative'),
    'pop' => array('pop', 'hits', 'top'),
    'news' => array('news', 'noticia', 'notícia', 'talk', 'esporte', 'jornal', 'debate'),
);
foreach ($lknwp_gi_rules as $lknwp_key => $lknwp_words) {
    foreach ($lknwp_words as $lknwp_w) {
        if (strpos($lknwp_gl, $lknwp_w) !== false) { $lknwp_gi = $lknwp_key; break 2; }
    }
}
$lknwp_icons = array(
    'radio' => '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 13a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><rect x="2.5" y="12.5" width="4.5" height="8" rx="2" fill="currentColor"/><rect x="17" y="12.5" width="4.5" height="8" rx="2" fill="currentColor"/></svg>',
    'rock' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
    'mpb' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>',
    'electronic' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>',
    'sertanejo' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m11.9 12.1 4.51-4.51"/><path d="M20.1 2.3a1 1 0 0 0-1.4 0l-1.12 1.11A2 2 0 0 0 17 4.83v1.34a2 2 0 0 1-.59 1.42"/><path d="m6 16 2 2"/><path d="M8.23 9.85A3 3 0 0 1 11 11a3 3 0 0 1 2.9 1.7 6 6 0 0 1-1.34 6.19l-1.34 1.35a3 3 0 0 1-1.3.79l-.7.2-3.05.6-.89-.89-.6-3.05.2-.7a3 3 0 0 1 .79-1.3l1.35-1.34A6 6 0 0 1 8.23 9.85Z"/></svg>',
    'pop' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
    'jazz' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M6 12c0-1.7.7-3.2 1.8-4.2"/><circle cx="12" cy="12" r="2"/></svg>',
    'news' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>',
);

// Ícones pequenos dos 4 cards
$lknwp_stat_icons = array(
    'genre'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41 13.42 20.58a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82Z"/><circle cx="7" cy="7" r="1.5"/></svg>',
    'country' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15 15 0 0 1 4 10 15 15 0 0 1-4 10 15 15 0 0 1-4-10 15 15 0 0 1 4-10Z"/></svg>',
    'quality' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20v-4"/><path d="M10 20V10"/><path d="M16 20V4"/></svg>',
    'plays'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><rect x="7" y="11" width="3" height="6"/><rect x="13" y="7" width="3" height="10"/></svg>',
);
?>

<div class="lkp-player-wrapper lkp-v2">
    <div id="lknwp-radio-custom-player" class="lkp-player-container">
        <!-- Hidden fields for JS -->
        <input type="hidden" id="lknwp_radio_browser_plugin_url" value="<?php echo esc_attr(base64_encode(defined('LKNWP_RADIO_BROWSER_PLUGIN_URL') ? LKNWP_RADIO_BROWSER_PLUGIN_URL : '')); ?>">
        <input type="hidden" id="lknwp_radio_homepage" value="<?php echo esc_attr(base64_encode(!empty($station_homepage) ? $station_homepage : '')); ?>">
        <input type="hidden" id="lknwp_station_clickcount" value="<?php echo isset($station_clickcount) ? intval($station_clickcount) : 0; ?>">
        <input type="hidden" id="lknwp_station_votes" value="<?php echo isset($station_votes) ? intval($station_votes) : 0; ?>">

        <!-- Ícone decorativo de fundo (gênero da rádio) — grande, opacidade diagonal -->
        <div class="lkp-v2__bg" aria-hidden="true"><?php echo $lknwp_icons[$lknwp_gi]; ?></div>

        <div class="lkp-v2__content">
            <!-- ===== TOPO: ícone + título / favoritar / "Agora tocando" / tags ===== -->
            <div class="lkp-v2__head">
                <div class="lkp-v2__head-row">
                    <div class="lkp-v2__brand">
                        <div id="lknwp-radio-img-parent" class="lkp-img-parent lkp-v2__logo">
                            <img src="<?php echo esc_attr($station_img); ?>" alt="<?php esc_attr_e( 'Radio logo', 'lknwp-radio-browser' ); ?>" class="lkp-station-img" onerror="this.onerror=null;this.src='<?php echo esc_js($default_img_url); ?>';">
                        </div>
                        <h2 id="lknwp-radio-station-name" class="lkp-v2__name"><?php echo $station_name ? esc_html($station_name) : esc_html__( 'Online Radio', 'lknwp-radio-browser' ); ?></h2>
                    </div>

                    <button type="button" class="lkp-v2__fav" id="lkp_fav_btn"
                        data-uuid="<?php echo esc_attr($station_uuid); ?>"
                        data-name="<?php echo esc_attr($station_name); ?>"
                        aria-pressed="false" aria-label="<?php esc_attr_e( 'Favoritar', 'lknwp-radio-browser' ); ?>" title="<?php esc_attr_e( 'Favoritar', 'lknwp-radio-browser' ); ?>">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 20s-7-4.5-9.3-8.4C1 8.6 2.6 5 6.1 5c2 0 3.3 1.1 3.9 2 .6-.9 1.9-2 3.9-2C17.4 5 19 8.6 21.3 11.6 19 15.5 12 20 12 20Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                        <span><?php esc_html_e( 'Favoritar', 'lknwp-radio-browser' ); ?></span>
                    </button>
                </div>

                <span class="lkp-v2__live">
                    <span class="lkp-v2__dot" aria-hidden="true"></span>
                    <?php esc_html_e( 'Agora tocando', 'lknwp-radio-browser' ); ?>
                </span>

                <div id="lknwp-radio-current-song-block" class="lkp-current-song-block lkp-current-song-block-hidden">
                    <div id="lknwp-radio-album-img" class="lkp-album-img"></div>
                    <div class="lkp-song-info">
                        <div id="lknwp-radio-artist" class="lkp-artist"></div>
                        <div id="lknwp-radio-current-song" class="lkp-current-song"></div>
                        <div id="lknwp-radio-station-stats" class="lkp-station-stats"></div>
                    </div>
                </div>

                <?php if (!empty($lknwp_tag_list)): ?>
                <div class="lkp-v2__tags">
                    <?php foreach ($lknwp_tag_list as $lknwp_tag): ?>
                        <span class="lkp-v2__tag"><?php echo esc_html($lknwp_tag); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- ===== MEIO: 4 cards | play + volume ===== -->
            <div class="lkp-v2__mid">
                <ul class="lkp-v2__cards">
                    <li class="lkp-v2__stat">
                        <span class="lkp-v2__stat-ic"><?php echo $lknwp_stat_icons['genre']; ?></span>
                        <span class="lkp-v2__stat-tx">
                            <span class="lkp-v2__stat-k"><?php esc_html_e( 'Gênero', 'lknwp-radio-browser' ); ?></span>
                            <span class="lkp-v2__stat-v"><?php echo $station_genre !== '' ? esc_html($station_genre) : '—'; ?></span>
                        </span>
                    </li>
                    <li class="lkp-v2__stat">
                        <span class="lkp-v2__stat-ic"><?php echo $lknwp_stat_icons['country']; ?></span>
                        <span class="lkp-v2__stat-tx">
                            <span class="lkp-v2__stat-k"><?php esc_html_e( 'País', 'lknwp-radio-browser' ); ?></span>
                            <span class="lkp-v2__stat-v">
                                <?php if ($station_cc !== ''): ?><span class="lkp-flag" data-cc="<?php echo esc_attr($station_cc); ?>"></span><?php endif; ?>
                                <?php echo esc_html($station_country !== '' ? $station_country : ($station_cc !== '' ? $station_cc : '—')); ?>
                            </span>
                        </span>
                    </li>
                    <li class="lkp-v2__stat">
                        <span class="lkp-v2__stat-ic"><?php echo $lknwp_stat_icons['quality']; ?></span>
                        <span class="lkp-v2__stat-tx">
                            <span class="lkp-v2__stat-k"><?php esc_html_e( 'Qualidade', 'lknwp-radio-browser' ); ?></span>
                            <span class="lkp-v2__stat-v"><?php echo ($station_bitrate !== '' || $station_codec !== '') ? esc_html(trim($station_bitrate . ($station_bitrate && $station_codec ? ' · ' : '') . $station_codec)) : '—'; ?></span>
                        </span>
                    </li>
                    <li class="lkp-v2__stat">
                        <span class="lkp-v2__stat-ic"><?php echo $lknwp_stat_icons['plays']; ?></span>
                        <span class="lkp-v2__stat-tx">
                            <span class="lkp-v2__stat-k"><?php esc_html_e( 'Reproduções', 'lknwp-radio-browser' ); ?></span>
                            <span class="lkp-v2__stat-v"><?php echo esc_html(number_format_i18n((int) $station_clickcount)); ?></span>
                        </span>
                    </li>
                </ul>

                <div class="lkp-v2__play">
                    <div id="lknwp-radio-play-section" class="lkp-play-section">
                        <div id="lknwp-radio-audio-visualizer" class="lkp-audio-visualizer">
                            <div id="lknwp-radio-visualizer-top" class="lkp-visualizer-top"></div>
                            <div id="lknwp-radio-visualizer-bottom" class="lkp-visualizer-bottom"></div>
                        </div>
                        <div id="lknwp-radio-play-btn-wrap" class="lkp-play-btn-wrap">
                            <button id="lknwp-radio-play-btn" class="lkp-play-btn">
                                <span id="lknwp-radio-play-icon" class="lkp-play-icon">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M8 5v14l11-7z" fill="#232b36"/></svg>
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="lkp-volume-section">
                        <div class="lkp-volume-row">
                            <button id="lknwp-radio-mute-btn" class="lkp-mute-btn" type="button" aria-label="<?php esc_attr_e( 'Mutar', 'lknwp-radio-browser' ); ?>" title="<?php esc_attr_e( 'Mutar', 'lknwp-radio-browser' ); ?>">
                                <svg class="lkp-mute-icon-on" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M11 5 6 9H3v6h3l5 4V5Z" fill="currentColor"/><path d="M15.5 9.5a4 4 0 0 1 0 5M18.5 7a7 7 0 0 1 0 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                <svg class="lkp-mute-icon-off" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M11 5 6 9H3v6h3l5 4V5Z" fill="currentColor"/><path d="m16 9 5 6M21 9l-5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                            <div class="lkp-volume-controls">
                                <input type="range" id="lknwp-radio-volume" min="0" max="1" step="0.01" value="0.2" class="lkp-volume-slider">
                                <span id="lknwp-radio-volume-value" class="lkp-volume-display">20%</span>
                            </div>
                        </div>
                    </div>

                    <!-- ===== Site da rádio + compartilhar (abaixo do volume) ===== -->
                    <div class="lkp-v2__bottom">
                        <?php if (!empty($station_homepage)): ?>
                        <a class="lkp-v2__home" href="<?php echo esc_url($station_homepage); ?>" target="_blank" rel="noopener">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M15 3h6v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 14 21 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span><?php esc_html_e( 'Site da rádio', 'lknwp-radio-browser' ); ?></span>
                        </a>
                        <?php endif; ?>

                        <div class="lkp-share-section">
                            <button id="lknwp-share-copy" class="lkp-share-btn" title="<?php esc_attr_e( 'Copy link', 'lknwp-radio-browser' ); ?>">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z" fill="currentColor"/></svg>
                            </button>
                            <a id="lknwp-share-instagram" class="lkp-share-btn" title="<?php esc_attr_e( 'Share on Instagram', 'lknwp-radio-browser' ); ?>">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" stroke="currentColor" stroke-width="2"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" stroke="currentColor" stroke-width="2"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke="currentColor" stroke-width="2"/></svg>
                            </a>
                            <a id="lknwp-share-whatsapp" class="lkp-share-btn" title="<?php esc_attr_e( 'Share on WhatsApp', 'lknwp-radio-browser' ); ?>">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.63" fill="currentColor"/></svg>
                            </a>
                            <a id="lknwp-share-twitter" class="lkp-share-btn" title="<?php esc_attr_e( 'Share on Twitter', 'lknwp-radio-browser' ); ?>">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z" stroke="currentColor" stroke-width="2" fill="none"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <audio id="lknwp-radio-player" src="<?php echo esc_attr($stream); ?>" preload="auto" class="lkp-audio"></audio>

        <!-- ===================== CONTINUE OUVINDO ===================== -->
        <section class="lkp-v2__continue" id="lkp_continue" hidden aria-live="polite">
            <div class="lkp-v2__continue-head">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l3 2"/></svg>
                <span><?php esc_html_e( 'Continue ouvindo', 'lknwp-radio-browser' ); ?></span>
                <div class="lkp-v2__nav" id="lkp_continue_nav" hidden>
                    <button type="button" class="lkp-v2__nav-btn" id="lkp_nav_prev" aria-label="<?php esc_attr_e( 'Rádio anterior', 'lknwp-radio-browser' ); ?>" title="<?php esc_attr_e( 'Rádio anterior', 'lknwp-radio-browser' ); ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    <button type="button" class="lkp-v2__nav-btn" id="lkp_nav_next" aria-label="<?php esc_attr_e( 'Próxima rádio', 'lknwp-radio-browser' ); ?>" title="<?php esc_attr_e( 'Próxima rádio', 'lknwp-radio-browser' ); ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </div>
            </div>
            <div class="lkp-v2__continue-track" id="lkp_continue_track"></div>
        </section>
    </div>
</div>

<?php endif; ?>
