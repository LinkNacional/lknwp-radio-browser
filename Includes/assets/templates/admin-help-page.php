<?php
/**
 * Template for Admin Help Page
 *
 * Variables available:
 * - $plugin_name: Plugin name
 * - $version: Plugin version
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Ícones SVG reutilizáveis dos botões de copiar (herdam currentColor).
$lknwp_copy_icon_svg = '<svg class="lknwp-radio-copy-btn__ic-copy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>';
$lknwp_check_icon_svg = '<svg class="lknwp-radio-copy-btn__ic-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>';
?>

<div class="wrap lknwp-radio-help">

    <!-- Hero -->
    <div class="lknwp-radio-hero">
        <div class="lknwp-radio-hero__icon">📻</div>
        <div class="lknwp-radio-hero__text">
            <h1><?php esc_html_e( 'LKN Radio Browser', 'lknwp-radio-browser' ); ?></h1>
            <p><?php esc_html_e( 'Add live online radios to your site with two shortcodes. Use the menu on the left to navigate the docs.', 'lknwp-radio-browser' ); ?></p>
        </div>
    </div>

    <?php // Moves WordPress admin notices (TGMPA/theme notices) here instead of inside the hero. ?>
    <hr class="wp-header-end" />

    <div class="lknwp-radio-help-layout">

        <!-- Sidebar -->
        <aside class="lknwp-radio-sidebar">
            <div class="lknwp-radio-search">
                <input type="search" id="lknwp-radio-help-search" class="lknwp-radio-search__input"
                    placeholder="<?php esc_attr_e( 'Search the docs…', 'lknwp-radio-browser' ); ?>"
                    aria-label="<?php esc_attr_e( 'Search the docs', 'lknwp-radio-browser' ); ?>" />
                <span class="lknwp-radio-search__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg></span>
                <button type="button" id="lknwp-radio-help-search-clear" class="lknwp-radio-search__clear"
                    aria-label="<?php esc_attr_e( 'Clear search', 'lknwp-radio-browser' ); ?>" hidden>✕</button>
            </div>

            <nav class="lknwp-radio-nav-list" aria-label="<?php esc_attr_e( 'Help sections', 'lknwp-radio-browser' ); ?>">
                <button type="button" id="nav-getting-started" class="lknwp-radio-nav-item is-active" data-target="panel-getting-started">
                    <span class="lknwp-radio-nav-item__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></svg></span>
                    <span class="lknwp-radio-nav-item__label"><?php esc_html_e( 'Getting Started', 'lknwp-radio-browser' ); ?></span>
                </button>
                <button type="button" id="nav-player" class="lknwp-radio-nav-item" data-target="panel-player">
                    <span class="lknwp-radio-nav-item__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg></span>
                    <span class="lknwp-radio-nav-item__label"><?php esc_html_e( 'Radio Player', 'lknwp-radio-browser' ); ?></span>
                </button>
                <button type="button" id="nav-list" class="lknwp-radio-nav-item" data-target="panel-list">
                    <span class="lknwp-radio-nav-item__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 13a8 8 0 0 1 16 0"/><rect x="2.5" y="12.5" width="4.5" height="8" rx="2" fill="currentColor"/><rect x="17" y="12.5" width="4.5" height="8" rx="2" fill="currentColor"/></svg></span>
                    <span class="lknwp-radio-nav-item__label"><?php esc_html_e( 'Radio List', 'lknwp-radio-browser' ); ?></span>
                </button>
                <button type="button" id="nav-parameters" class="lknwp-radio-nav-item" data-target="panel-parameters">
                    <span class="lknwp-radio-nav-item__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg></span>
                    <span class="lknwp-radio-nav-item__label"><?php esc_html_e( 'Parameters', 'lknwp-radio-browser' ); ?></span>
                </button>
                <button type="button" id="nav-hide" class="lknwp-radio-nav-item" data-target="panel-hide">
                    <span class="lknwp-radio-nav-item__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="21" x2="14" y1="4" y2="4"/><line x1="10" x2="3" y1="4" y2="4"/><line x1="21" x2="12" y1="12" y2="12"/><line x1="8" x2="3" y1="12" y2="12"/><line x1="21" x2="16" y1="20" y2="20"/><line x1="12" x2="3" y1="20" y2="20"/><line x1="14" x2="14" y1="2" y2="6"/><line x1="8" x2="8" y1="10" y2="14"/><line x1="16" x2="16" y1="18" y2="22"/></svg></span>
                    <span class="lknwp-radio-nav-item__label"><?php esc_html_e( 'Hide Filters', 'lknwp-radio-browser' ); ?></span>
                </button>
                <button type="button" id="nav-examples" class="lknwp-radio-nav-item" data-target="panel-examples">
                    <span class="lknwp-radio-nav-item__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg></span>
                    <span class="lknwp-radio-nav-item__label"><?php esc_html_e( 'Examples', 'lknwp-radio-browser' ); ?></span>
                </button>
                <button type="button" id="nav-faq" class="lknwp-radio-nav-item" data-target="panel-faq">
                    <span class="lknwp-radio-nav-item__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg></span>
                    <span class="lknwp-radio-nav-item__label"><?php esc_html_e( 'FAQ', 'lknwp-radio-browser' ); ?></span>
                </button>
            </nav>
        </aside>

        <!-- Content -->
        <main class="lknwp-radio-content" id="lknwp-radio-content">

            <div class="lknwp-radio-no-results" id="lknwp-radio-no-results" hidden>
                <span class="lknwp-radio-no-results__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg></span>
                <p><?php esc_html_e( 'No matches found. Try another term.', 'lknwp-radio-browser' ); ?></p>
            </div>

            <!-- ================= GETTING STARTED ================= -->
            <section class="lknwp-radio-panel is-active" id="panel-getting-started" aria-labelledby="nav-getting-started">
                <header class="lknwp-radio-panel__head">
                    <h2><span class="lknwp-radio-panel__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></svg></span><?php esc_html_e( 'Getting Started', 'lknwp-radio-browser' ); ?></h2>
                    <p><?php esc_html_e( 'The plugin is split into two shortcodes. You put the player on one page and the list on another. Add layout="modern" to use the new layout; without it, the previous (legacy) layout is used.', 'lknwp-radio-browser' ); ?></p>
                </header>

                <div class="lknwp-radio-info" data-sf>
                    <h3><?php esc_html_e( 'Two shortcodes, one goal', 'lknwp-radio-browser' ); ?></h3>
                    <ul>
                        <li><strong><?php esc_html_e( 'Radio Player', 'lknwp-radio-browser' ); ?></strong> — <?php esc_html_e( 'displays the player for a single station.', 'lknwp-radio-browser' ); ?></li>
                        <li><strong><?php esc_html_e( 'Radio List', 'lknwp-radio-browser' ); ?></strong> — <?php esc_html_e( 'displays a filterable list of stations that link to the player.', 'lknwp-radio-browser' ); ?></li>
                    </ul>
                </div>

                <div class="lknwp-radio-info" data-sf>
                    <h4 class="lknwp-radio-step"><span class="lknwp-radio-step__num" aria-hidden="true">1</span><?php esc_html_e( 'Create the Player page', 'lknwp-radio-browser' ); ?></h4>
                    <ol>
                        <li><?php esc_html_e( 'Go to Pages › Add New.', 'lknwp-radio-browser' ); ?></li>
                        <li><?php esc_html_e( 'Create a page (e.g. "Player" with slug "player").', 'lknwp-radio-browser' ); ?></li>
                        <li><?php esc_html_e( 'Add the player shortcode and save:', 'lknwp-radio-browser' ); ?></li>
                    </ol>
                    <div class="lknwp-radio-code-block">
                        <code>[radio_browser_player layout="modern"]</code>
                        <button type="button" class="lknwp-radio-copy-btn" data-copy="<?php echo esc_attr( '[radio_browser_player layout="modern"]' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?><span class="lknwp-radio-copy-btn__label"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></span></button>
                    </div>
                </div>

                <div class="lknwp-radio-info" data-sf>
                    <h4 class="lknwp-radio-step"><span class="lknwp-radio-step__num" aria-hidden="true">2</span><?php esc_html_e( 'Create the List page', 'lknwp-radio-browser' ); ?></h4>
                    <ol>
                        <li><?php esc_html_e( 'Go to Pages › Add New.', 'lknwp-radio-browser' ); ?></li>
                        <li><?php esc_html_e( 'Add the list shortcode, replacing "player" with your player page slug:', 'lknwp-radio-browser' ); ?></li>
                    </ol>
                    <div class="lknwp-radio-code-block">
                        <code>[radio_browser_list player_page="player" layout="modern"]</code>
                        <button type="button" class="lknwp-radio-copy-btn" data-copy="<?php echo esc_attr( '[radio_browser_list player_page="player" layout="modern"]' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?><span class="lknwp-radio-copy-btn__label"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></span></button>
                    </div>
                    <p class="lknwp-radio-muted"><?php esc_html_e( 'The list links to URLs like:', 'lknwp-radio-browser' ); ?> <code>https://your-site.com/radio-list/<strong>player</strong></code></p>
                </div>
            </section>

            <!-- ================= RADIO PLAYER ================= -->
            <section class="lknwp-radio-panel" id="panel-player" aria-labelledby="nav-player">
                <header class="lknwp-radio-panel__head">
                    <h2><span class="lknwp-radio-panel__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg></span><?php esc_html_e( 'Radio Player Shortcode', 'lknwp-radio-browser' ); ?></h2>
                    <p><?php esc_html_e( 'Displays the audio player for a specific station.', 'lknwp-radio-browser' ); ?></p>
                </header>

                <div class="lknwp-radio-code-block" data-sf>
                    <code>[radio_browser_player layout="modern"]</code>
                    <button type="button" class="lknwp-radio-copy-btn" data-copy="<?php echo esc_attr( '[radio_browser_player layout="modern"]' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?><span class="lknwp-radio-copy-btn__label"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></span></button>
                </div>

                <div class="lknwp-radio-info" data-sf>
                    <h4><span class="lknwp-radio-panel__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/></svg></span><?php esc_html_e( 'How it works', 'lknwp-radio-browser' ); ?></h4>
                    <ul>
                        <li><?php esc_html_e( 'Create a page (e.g. "Player" with slug "player").', 'lknwp-radio-browser' ); ?></li>
                        <li><?php esc_html_e( 'Add the shortcode', 'lknwp-radio-browser' ); ?> <code>[radio_browser_player layout="modern"]</code>.</li>
                        <li><?php esc_html_e( 'The player automatically receives the station from the URL.', 'lknwp-radio-browser' ); ?></li>
                        <li><?php esc_html_e( 'It works with links coming from the Radio List.', 'lknwp-radio-browser' ); ?></li>
                    </ul>
                </div>

                <div class="lknwp-radio-info" data-sf>
                    <h4><span class="lknwp-radio-panel__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></span><?php esc_html_e( 'SEO-friendly URLs', 'lknwp-radio-browser' ); ?></h4>
                    <p><?php esc_html_e( 'Each station gets its own clean URL under the player page, e.g.', 'lknwp-radio-browser' ); ?></p>
                    <p><code>/player/Radio%20Name/</code></p>
                    <p class="lknwp-radio-muted"><?php esc_html_e( 'The page title is set to the station name automatically.', 'lknwp-radio-browser' ); ?></p>
                </div>

                <div class="lknwp-radio-note" data-sf>
                    <strong><?php esc_html_e( 'Layout:', 'lknwp-radio-browser' ); ?></strong>
                    <?php esc_html_e( 'without a layout attribute the shortcode renders the previous (legacy) layout, so existing pages keep working. Use', 'lknwp-radio-browser' ); ?> <code>layout="modern"</code> <?php esc_html_e( 'for the new layout (or', 'lknwp-radio-browser' ); ?> <code>layout="legacy"</code> <?php esc_html_e( 'to be explicit).', 'lknwp-radio-browser' ); ?>
                </div>
            </section>

            <!-- ================= RADIO LIST ================= -->
            <section class="lknwp-radio-panel" id="panel-list" aria-labelledby="nav-list">
                <header class="lknwp-radio-panel__head">
                    <h2><span class="lknwp-radio-panel__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 13a8 8 0 0 1 16 0"/><rect x="2.5" y="12.5" width="4.5" height="8" rx="2" fill="currentColor"/><rect x="17" y="12.5" width="4.5" height="8" rx="2" fill="currentColor"/></svg></span><?php esc_html_e( 'Radio List Shortcode', 'lknwp-radio-browser' ); ?></h2>
                    <p><?php esc_html_e( 'Displays a grid of stations with filters and a search box.', 'lknwp-radio-browser' ); ?></p>
                </header>

                <div class="lknwp-radio-code-block" data-sf>
                    <code>[radio_browser_list player_page="player" layout="modern"]</code>
                    <button type="button" class="lknwp-radio-copy-btn" data-copy="<?php echo esc_attr( '[radio_browser_list player_page="player" layout="modern"]' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?><span class="lknwp-radio-copy-btn__label"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></span></button>
                </div>

                <div class="lknwp-radio-info" data-sf>
                    <h4><span class="lknwp-radio-panel__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/></svg></span><?php esc_html_e( 'How it works', 'lknwp-radio-browser' ); ?></h4>
                    <ul>
                        <li><code>player_page</code> <?php esc_html_e( 'is required — the slug of the page that contains the player shortcode. All list links point there.', 'lknwp-radio-browser' ); ?></li>
                        <li><?php esc_html_e( 'Stations come from the Radio-Browser.info database (30,000+ stations).', 'lknwp-radio-browser' ); ?></li>
                        <li><?php esc_html_e( 'Visitors can filter by country, genre, sort order and search by name.', 'lknwp-radio-browser' ); ?></li>
                    </ul>
                </div>

                <div class="lknwp-radio-note" data-sf>
                    <strong><?php esc_html_e( 'Tip:', 'lknwp-radio-browser' ); ?></strong>
                    <?php esc_html_e( 'you can nest the list page under another page and pass the player slug the same way — nested URLs are supported.', 'lknwp-radio-browser' ); ?>
                </div>

                <div class="lknwp-radio-note" data-sf>
                    <strong><?php esc_html_e( 'Layout:', 'lknwp-radio-browser' ); ?></strong>
                    <?php esc_html_e( 'without a layout attribute the shortcode renders the previous (legacy) layout, so existing pages keep working. Use', 'lknwp-radio-browser' ); ?> <code>layout="modern"</code> <?php esc_html_e( 'for the new layout.', 'lknwp-radio-browser' ); ?>
                </div>
            </section>

            <!-- ================= PARAMETERS ================= -->
            <section class="lknwp-radio-panel" id="panel-parameters" aria-labelledby="nav-parameters">
                <header class="lknwp-radio-panel__head">
                    <h2><span class="lknwp-radio-panel__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg></span><?php esc_html_e( 'List Parameters', 'lknwp-radio-browser' ); ?></h2>
                    <p><?php esc_html_e( 'Optional attributes for [radio_browser_list]. Click the copy icon to grab a parameter name.', 'lknwp-radio-browser' ); ?></p>
                </header>

                <div class="lknwp-radio-info lknwp-sf-container">
                    <table class="lknwp-radio-params-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Parameter', 'lknwp-radio-browser' ); ?></th>
                                <th><?php esc_html_e( 'Description', 'lknwp-radio-browser' ); ?></th>
                                <th><?php esc_html_e( 'Default', 'lknwp-radio-browser' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr data-sf>
                                <td><code>layout</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="layout" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></button></td>
                                <td><?php esc_html_e( 'Layout to render: "modern" = new layout; absent or "legacy" = previous layout', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"legacy"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>player_page</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="player_page" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></button></td>
                                <td><?php esc_html_e( 'Player page slug (required)', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"player"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>countrycode</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="countrycode" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></button></td>
                                <td><?php esc_html_e( 'Country code (BR, US, FR, etc.)', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"BR"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>limit</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="limit" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></button></td>
                                <td><?php esc_html_e( 'Number of radios to display (1–100)', 'lknwp-radio-browser' ); ?></td>
                                <td><code>20</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>sort</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="sort" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></button></td>
                                <td><?php esc_html_e( 'Sort order (clickcount, name, random, bitrate)', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"clickcount"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>reverse</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="reverse" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></button></td>
                                <td><?php esc_html_e( 'Reverse order (1 or 0)', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"1"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>search</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="search" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></button></td>
                                <td><?php esc_html_e( 'Pre-filled search term', 'lknwp-radio-browser' ); ?></td>
                                <td><code>""</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>genre</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="genre" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></button></td>
                                <td><?php esc_html_e( 'Pre-selected genre/tag filter', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"all"</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- ================= HIDE FILTERS ================= -->
            <section class="lknwp-radio-panel" id="panel-hide" aria-labelledby="nav-hide">
                <header class="lknwp-radio-panel__head">
                    <h2><span class="lknwp-radio-panel__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="21" x2="14" y1="4" y2="4"/><line x1="10" x2="3" y1="4" y2="4"/><line x1="21" x2="12" y1="12" y2="12"/><line x1="8" x2="3" y1="12" y2="12"/><line x1="21" x2="16" y1="20" y2="20"/><line x1="12" x2="3" y1="20" y2="20"/><line x1="14" x2="14" y1="2" y2="6"/><line x1="8" x2="8" y1="10" y2="14"/><line x1="16" x2="16" y1="18" y2="22"/></svg></span><?php esc_html_e( 'Hide Filters', 'lknwp-radio-browser' ); ?></h2>
                    <p><?php esc_html_e( 'Use these to hide any filter field. Each accepts "yes" or "no".', 'lknwp-radio-browser' ); ?></p>
                </header>

                <div class="lknwp-radio-info lknwp-sf-container">
                    <table class="lknwp-radio-params-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Parameter', 'lknwp-radio-browser' ); ?></th>
                                <th><?php esc_html_e( 'Description', 'lknwp-radio-browser' ); ?></th>
                                <th><?php esc_html_e( 'Values', 'lknwp-radio-browser' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr data-sf>
                                <td><code>hide_country</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="hide_country" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></button></td>
                                <td><?php esc_html_e( 'Hide the Country field', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"yes"</code>/<code>"no"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>hide_limit</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="hide_limit" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></button></td>
                                <td><?php esc_html_e( 'Hide the Limit field', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"yes"</code>/<code>"no"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>hide_sort</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="hide_sort" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></button></td>
                                <td><?php esc_html_e( 'Hide the Sort field', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"yes"</code>/<code>"no"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>hide_order</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="hide_order" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></button></td>
                                <td><?php esc_html_e( 'Hide the Order button', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"yes"</code>/<code>"no"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>hide_genre</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="hide_genre" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></button></td>
                                <td><?php esc_html_e( 'Hide the Genre field', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"yes"</code>/<code>"no"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>hide_search</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="hide_search" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></button></td>
                                <td><?php esc_html_e( 'Hide the Search field', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"yes"</code>/<code>"no"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>hide_button</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="hide_button" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></button></td>
                                <td><?php esc_html_e( 'Hide the Search button', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"yes"</code>/<code>"no"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>hide_all_filters</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="hide_all_filters" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></button></td>
                                <td><?php esc_html_e( 'Hide ALL filters at once', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"yes"</code>/<code>"no"</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- ================= EXAMPLES ================= -->
            <section class="lknwp-radio-panel" id="panel-examples" aria-labelledby="nav-examples">
                <header class="lknwp-radio-panel__head">
                    <h2><span class="lknwp-radio-panel__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg></span><?php esc_html_e( 'Usage Examples', 'lknwp-radio-browser' ); ?></h2>
                    <p><?php esc_html_e( 'Copy-and-paste ready shortcodes for common setups.', 'lknwp-radio-browser' ); ?></p>
                </header>

                <div class="lknwp-radio-example" data-sf>
                    <h4><?php esc_html_e( 'Complete list with filters (default)', 'lknwp-radio-browser' ); ?></h4>
                    <div class="lknwp-radio-code-block">
                        <code>[radio_browser_list player_page="player" layout="modern"]</code>
                        <button type="button" class="lknwp-radio-copy-btn" data-copy="<?php echo esc_attr( '[radio_browser_list player_page="player" layout="modern"]' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?><span class="lknwp-radio-copy-btn__label"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></span></button>
                    </div>
                </div>

                <div class="lknwp-radio-example" data-sf>
                    <h4><?php esc_html_e( 'Clean list without filters', 'lknwp-radio-browser' ); ?></h4>
                    <div class="lknwp-radio-code-block">
                        <code>[radio_browser_list player_page="player" layout="modern" hide_all_filters="yes"]</code>
                        <button type="button" class="lknwp-radio-copy-btn" data-copy="<?php echo esc_attr( '[radio_browser_list player_page="player" layout="modern" hide_all_filters="yes"]' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?><span class="lknwp-radio-copy-btn__label"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></span></button>
                    </div>
                </div>

                <div class="lknwp-radio-example" data-sf>
                    <h4><?php esc_html_e( 'Text search only', 'lknwp-radio-browser' ); ?></h4>
                    <div class="lknwp-radio-code-block">
                        <code>[radio_browser_list player_page="player" layout="modern" hide_country="yes" hide_limit="yes" hide_sort="yes" hide_order="yes"]</code>
                        <button type="button" class="lknwp-radio-copy-btn" data-copy="<?php echo esc_attr( '[radio_browser_list player_page="player" layout="modern" hide_country="yes" hide_limit="yes" hide_sort="yes" hide_order="yes"]' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?><span class="lknwp-radio-copy-btn__label"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></span></button>
                    </div>
                </div>

                <div class="lknwp-radio-example" data-sf>
                    <h4><?php esc_html_e( 'Fixed configuration (US radios, 10 stations, no filters)', 'lknwp-radio-browser' ); ?></h4>
                    <div class="lknwp-radio-code-block">
                        <code>[radio_browser_list player_page="player" layout="modern" countrycode="US" limit="10" hide_all_filters="yes"]</code>
                        <button type="button" class="lknwp-radio-copy-btn" data-copy="<?php echo esc_attr( '[radio_browser_list player_page="player" layout="modern" countrycode="US" limit="10" hide_all_filters="yes"]' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?><span class="lknwp-radio-copy-btn__label"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></span></button>
                    </div>
                </div>

                <div class="lknwp-radio-example" data-sf>
                    <h4><?php esc_html_e( 'List sorted by name (alphabetical)', 'lknwp-radio-browser' ); ?></h4>
                    <div class="lknwp-radio-code-block">
                        <code>[radio_browser_list player_page="player" layout="modern" sort="name"]</code>
                        <button type="button" class="lknwp-radio-copy-btn" data-copy="<?php echo esc_attr( '[radio_browser_list player_page="player" layout="modern" sort="name"]' ); ?>"><?php echo $lknwp_copy_icon_svg . $lknwp_check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?><span class="lknwp-radio-copy-btn__label"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></span></button>
                    </div>
                </div>
            </section>

            <!-- ================= FAQ ================= -->
            <section class="lknwp-radio-panel" id="panel-faq" aria-labelledby="nav-faq">
                <header class="lknwp-radio-panel__head">
                    <h2><span class="lknwp-radio-panel__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg></span><?php esc_html_e( 'Frequently Asked Questions', 'lknwp-radio-browser' ); ?></h2>
                    <p><?php esc_html_e( 'Quick answers to the most common issues.', 'lknwp-radio-browser' ); ?></p>
                </header>

                <details class="lknwp-radio-faq" data-sf>
                    <summary><?php esc_html_e( 'A station does not play and keeps loading. What now?', 'lknwp-radio-browser' ); ?></summary>
                    <div class="lknwp-radio-faq__body">
                        <p><?php esc_html_e( 'The station may be offline, or it may stream over insecure HTTP (see the next item). Try another station to confirm the player itself works.', 'lknwp-radio-browser' ); ?></p>
                    </div>
                </details>

                <details class="lknwp-radio-faq" data-sf>
                    <summary><?php esc_html_e( 'My page is HTTPS and the player warns about an insecure (HTTP) stream.', 'lknwp-radio-browser' ); ?></summary>
                    <div class="lknwp-radio-faq__body">
                        <p><?php esc_html_e( 'Browsers block HTTP audio on HTTPS pages (mixed content). Ask the station provider to enable HTTPS, or access the page over HTTP.', 'lknwp-radio-browser' ); ?></p>
                    </div>
                </details>

                <details class="lknwp-radio-faq" data-sf>
                    <summary><?php esc_html_e( 'I cannot find my radio in the list.', 'lknwp-radio-browser' ); ?></summary>
                    <div class="lknwp-radio-faq__body">
                        <p><?php esc_html_e( 'Use the "Search Radio" field or the genre filter. The data comes from Radio-Browser.info — if a station is not registered there, it will not appear.', 'lknwp-radio-browser' ); ?></p>
                    </div>
                </details>

                <details class="lknwp-radio-faq" data-sf>
                    <summary><?php esc_html_e( 'How do I find the slug for player_page?', 'lknwp-radio-browser' ); ?></summary>
                    <div class="lknwp-radio-faq__body">
                        <p><?php esc_html_e( 'Go to Pages, hover over the page that contains [radio_browser_player layout="modern"] and read its slug in the URL. Use that value in player_page="…".', 'lknwp-radio-browser' ); ?></p>
                    </div>
                </details>

                <details class="lknwp-radio-faq" data-sf>
                    <summary><?php esc_html_e( 'Can I have more than one list on the same page?', 'lknwp-radio-browser' ); ?></summary>
                    <div class="lknwp-radio-faq__body">
                        <p><?php esc_html_e( 'Yes. Just keep the same player_page on all of them so every card links to the same player.', 'lknwp-radio-browser' ); ?></p>
                    </div>
                </details>

                <details class="lknwp-radio-faq" data-sf>
                    <summary><?php esc_html_e( 'What do sort and reverse do?', 'lknwp-radio-browser' ); ?></summary>
                    <div class="lknwp-radio-faq__body">
                        <p><?php esc_html_e( 'sort chooses the ordering (clickcount = most popular, name, random, bitrate). reverse="1" flips the order.', 'lknwp-radio-browser' ); ?></p>
                    </div>
                </details>
            </section>

        </main>
    </div>
</div>
