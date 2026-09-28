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
                <span class="lknwp-radio-search__icon" aria-hidden="true">🔎</span>
                <button type="button" id="lknwp-radio-help-search-clear" class="lknwp-radio-search__clear"
                    aria-label="<?php esc_attr_e( 'Clear search', 'lknwp-radio-browser' ); ?>" hidden>✕</button>
            </div>

            <nav class="lknwp-radio-nav-list" aria-label="<?php esc_attr_e( 'Help sections', 'lknwp-radio-browser' ); ?>">
                <button type="button" id="nav-getting-started" class="lknwp-radio-nav-item is-active" data-target="panel-getting-started">
                    <span class="lknwp-radio-nav-item__icon">🚀</span>
                    <span class="lknwp-radio-nav-item__label"><?php esc_html_e( 'Getting Started', 'lknwp-radio-browser' ); ?></span>
                </button>
                <button type="button" id="nav-player" class="lknwp-radio-nav-item" data-target="panel-player">
                    <span class="lknwp-radio-nav-item__icon">🎵</span>
                    <span class="lknwp-radio-nav-item__label"><?php esc_html_e( 'Radio Player', 'lknwp-radio-browser' ); ?></span>
                </button>
                <button type="button" id="nav-list" class="lknwp-radio-nav-item" data-target="panel-list">
                    <span class="lknwp-radio-nav-item__icon">📻</span>
                    <span class="lknwp-radio-nav-item__label"><?php esc_html_e( 'Radio List', 'lknwp-radio-browser' ); ?></span>
                </button>
                <button type="button" id="nav-parameters" class="lknwp-radio-nav-item" data-target="panel-parameters">
                    <span class="lknwp-radio-nav-item__icon">⚙️</span>
                    <span class="lknwp-radio-nav-item__label"><?php esc_html_e( 'Parameters', 'lknwp-radio-browser' ); ?></span>
                </button>
                <button type="button" id="nav-hide" class="lknwp-radio-nav-item" data-target="panel-hide">
                    <span class="lknwp-radio-nav-item__icon">🎛️</span>
                    <span class="lknwp-radio-nav-item__label"><?php esc_html_e( 'Hide Filters', 'lknwp-radio-browser' ); ?></span>
                </button>
                <button type="button" id="nav-examples" class="lknwp-radio-nav-item" data-target="panel-examples">
                    <span class="lknwp-radio-nav-item__icon">💡</span>
                    <span class="lknwp-radio-nav-item__label"><?php esc_html_e( 'Examples', 'lknwp-radio-browser' ); ?></span>
                </button>
                <button type="button" id="nav-faq" class="lknwp-radio-nav-item" data-target="panel-faq">
                    <span class="lknwp-radio-nav-item__icon">❓</span>
                    <span class="lknwp-radio-nav-item__label"><?php esc_html_e( 'FAQ', 'lknwp-radio-browser' ); ?></span>
                </button>
            </nav>
        </aside>

        <!-- Content -->
        <main class="lknwp-radio-content" id="lknwp-radio-content">

            <div class="lknwp-radio-no-results" id="lknwp-radio-no-results" hidden>
                <span class="lknwp-radio-no-results__icon">🔍</span>
                <p><?php esc_html_e( 'No matches found. Try another term.', 'lknwp-radio-browser' ); ?></p>
            </div>

            <!-- ================= GETTING STARTED ================= -->
            <section class="lknwp-radio-panel is-active" id="panel-getting-started" aria-labelledby="nav-getting-started">
                <header class="lknwp-radio-panel__head">
                    <h2>🚀 <?php esc_html_e( 'Getting Started', 'lknwp-radio-browser' ); ?></h2>
                    <p><?php esc_html_e( 'The plugin is split into two shortcodes. You put the player on one page and the list on another.', 'lknwp-radio-browser' ); ?></p>
                </header>

                <div class="lknwp-radio-info" data-sf>
                    <h3><?php esc_html_e( 'Two shortcodes, one goal', 'lknwp-radio-browser' ); ?></h3>
                    <ul>
                        <li><strong><?php esc_html_e( 'Radio Player', 'lknwp-radio-browser' ); ?></strong> — <?php esc_html_e( 'displays the player for a single station.', 'lknwp-radio-browser' ); ?></li>
                        <li><strong><?php esc_html_e( 'Radio List', 'lknwp-radio-browser' ); ?></strong> — <?php esc_html_e( 'displays a filterable list of stations that link to the player.', 'lknwp-radio-browser' ); ?></li>
                    </ul>
                </div>

                <div class="lknwp-radio-info" data-sf>
                    <h4><?php esc_html_e( 'Step 1 — Create the Player page', 'lknwp-radio-browser' ); ?></h4>
                    <ol>
                        <li><?php esc_html_e( 'Go to Pages › Add New.', 'lknwp-radio-browser' ); ?></li>
                        <li><?php esc_html_e( 'Create a page (e.g. "Player" with slug "player").', 'lknwp-radio-browser' ); ?></li>
                        <li><?php esc_html_e( 'Add the player shortcode and save:', 'lknwp-radio-browser' ); ?></li>
                    </ol>
                    <div class="lknwp-radio-code-block">
                        <code>[radio_browser_player]</code>
                        <button type="button" class="lknwp-radio-copy-btn" data-copy="<?php echo esc_attr( '[radio_browser_player]' ); ?>"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></button>
                    </div>
                </div>

                <div class="lknwp-radio-info" data-sf>
                    <h4><?php esc_html_e( 'Step 2 — Create the List page', 'lknwp-radio-browser' ); ?></h4>
                    <ol>
                        <li><?php esc_html_e( 'Go to Pages › Add New.', 'lknwp-radio-browser' ); ?></li>
                        <li><?php esc_html_e( 'Add the list shortcode, replacing "player" with your player page slug:', 'lknwp-radio-browser' ); ?></li>
                    </ol>
                    <div class="lknwp-radio-code-block">
                        <code>[radio_browser_list player_page="player"]</code>
                        <button type="button" class="lknwp-radio-copy-btn" data-copy="<?php echo esc_attr( '[radio_browser_list player_page="player"]' ); ?>"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></button>
                    </div>
                    <p class="lknwp-radio-muted"><?php esc_html_e( 'The list links to URLs like:', 'lknwp-radio-browser' ); ?> <code>https://your-site.com/radio-list/<strong>player</strong></code></p>
                </div>
            </section>

            <!-- ================= RADIO PLAYER ================= -->
            <section class="lknwp-radio-panel" id="panel-player" aria-labelledby="nav-player">
                <header class="lknwp-radio-panel__head">
                    <h2>🎵 <?php esc_html_e( 'Radio Player Shortcode', 'lknwp-radio-browser' ); ?></h2>
                    <p><?php esc_html_e( 'Displays the audio player for a specific station.', 'lknwp-radio-browser' ); ?></p>
                </header>

                <div class="lknwp-radio-code-block" data-sf>
                    <code>[radio_browser_player]</code>
                    <button type="button" class="lknwp-radio-copy-btn" data-copy="<?php echo esc_attr( '[radio_browser_player]' ); ?>"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></button>
                </div>

                <div class="lknwp-radio-info" data-sf>
                    <h4><?php esc_html_e( '📋 How it works', 'lknwp-radio-browser' ); ?></h4>
                    <ul>
                        <li><?php esc_html_e( 'Create a page (e.g. "Player" with slug "player").', 'lknwp-radio-browser' ); ?></li>
                        <li><?php esc_html_e( 'Add the shortcode', 'lknwp-radio-browser' ); ?> <code>[radio_browser_player]</code>.</li>
                        <li><?php esc_html_e( 'The player automatically receives the station from the URL.', 'lknwp-radio-browser' ); ?></li>
                        <li><?php esc_html_e( 'It works with links coming from the Radio List.', 'lknwp-radio-browser' ); ?></li>
                    </ul>
                </div>

                <div class="lknwp-radio-info" data-sf>
                    <h4><?php esc_html_e( '🔗 SEO-friendly URLs', 'lknwp-radio-browser' ); ?></h4>
                    <p><?php esc_html_e( 'Each station gets its own clean URL under the player page, e.g.', 'lknwp-radio-browser' ); ?></p>
                    <p><code>/player/Radio%20Name/</code></p>
                    <p class="lknwp-radio-muted"><?php esc_html_e( 'The page title is set to the station name automatically.', 'lknwp-radio-browser' ); ?></p>
                </div>

                <div class="lknwp-radio-note" data-sf>
                    <strong><?php esc_html_e( 'Legacy layout:', 'lknwp-radio-browser' ); ?></strong>
                    <?php esc_html_e( 'use', 'lknwp-radio-browser' ); ?> <code>[radio_browser_player_legado]</code> <?php esc_html_e( 'to keep the previous player layout.', 'lknwp-radio-browser' ); ?>
                </div>
            </section>

            <!-- ================= RADIO LIST ================= -->
            <section class="lknwp-radio-panel" id="panel-list" aria-labelledby="nav-list">
                <header class="lknwp-radio-panel__head">
                    <h2>📻 <?php esc_html_e( 'Radio List Shortcode', 'lknwp-radio-browser' ); ?></h2>
                    <p><?php esc_html_e( 'Displays a grid of stations with filters and a search box.', 'lknwp-radio-browser' ); ?></p>
                </header>

                <div class="lknwp-radio-code-block" data-sf>
                    <code>[radio_browser_list player_page="player"]</code>
                    <button type="button" class="lknwp-radio-copy-btn" data-copy="<?php echo esc_attr( '[radio_browser_list player_page="player"]' ); ?>"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></button>
                </div>

                <div class="lknwp-radio-info" data-sf>
                    <h4><?php esc_html_e( '📋 How it works', 'lknwp-radio-browser' ); ?></h4>
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
                    <strong><?php esc_html_e( 'Legacy layout:', 'lknwp-radio-browser' ); ?></strong>
                    <?php esc_html_e( 'use', 'lknwp-radio-browser' ); ?> <code>[radio_browser_list_legado player_page="player"]</code> <?php esc_html_e( 'to keep the previous list layout (same parameters).', 'lknwp-radio-browser' ); ?>
                </div>
            </section>

            <!-- ================= PARAMETERS ================= -->
            <section class="lknwp-radio-panel" id="panel-parameters" aria-labelledby="nav-parameters">
                <header class="lknwp-radio-panel__head">
                    <h2>⚙️ <?php esc_html_e( 'List Parameters', 'lknwp-radio-browser' ); ?></h2>
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
                                <td><code>player_page</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="player_page" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>">⧉</button></td>
                                <td><?php esc_html_e( 'Player page slug (required)', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"player"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>countrycode</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="countrycode" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>">⧉</button></td>
                                <td><?php esc_html_e( 'Country code (BR, US, FR, etc.)', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"BR"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>limit</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="limit" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>">⧉</button></td>
                                <td><?php esc_html_e( 'Number of radios to display (1–100)', 'lknwp-radio-browser' ); ?></td>
                                <td><code>20</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>sort</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="sort" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>">⧉</button></td>
                                <td><?php esc_html_e( 'Sort order (clickcount, name, random, bitrate)', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"clickcount"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>reverse</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="reverse" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>">⧉</button></td>
                                <td><?php esc_html_e( 'Reverse order (1 or 0)', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"1"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>search</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="search" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>">⧉</button></td>
                                <td><?php esc_html_e( 'Pre-filled search term', 'lknwp-radio-browser' ); ?></td>
                                <td><code>""</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>genre</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="genre" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>">⧉</button></td>
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
                    <h2>🎛️ <?php esc_html_e( 'Hide Filters', 'lknwp-radio-browser' ); ?></h2>
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
                                <td><code>hide_country</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="hide_country" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>">⧉</button></td>
                                <td><?php esc_html_e( 'Hide the Country field', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"yes"</code>/<code>"no"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>hide_limit</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="hide_limit" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>">⧉</button></td>
                                <td><?php esc_html_e( 'Hide the Limit field', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"yes"</code>/<code>"no"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>hide_sort</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="hide_sort" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>">⧉</button></td>
                                <td><?php esc_html_e( 'Hide the Sort field', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"yes"</code>/<code>"no"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>hide_order</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="hide_order" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>">⧉</button></td>
                                <td><?php esc_html_e( 'Hide the Order button', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"yes"</code>/<code>"no"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>hide_genre</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="hide_genre" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>">⧉</button></td>
                                <td><?php esc_html_e( 'Hide the Genre field', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"yes"</code>/<code>"no"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>hide_search</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="hide_search" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>">⧉</button></td>
                                <td><?php esc_html_e( 'Hide the Search field', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"yes"</code>/<code>"no"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>hide_button</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="hide_button" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>">⧉</button></td>
                                <td><?php esc_html_e( 'Hide the Search button', 'lknwp-radio-browser' ); ?></td>
                                <td><code>"yes"</code>/<code>"no"</code></td>
                            </tr>
                            <tr data-sf>
                                <td><code>hide_all_filters</code><button type="button" class="lknwp-radio-copy-btn lknwp-radio-copy-btn--mini" data-copy="hide_all_filters" title="<?php esc_attr_e( 'Copy', 'lknwp-radio-browser' ); ?>">⧉</button></td>
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
                    <h2>💡 <?php esc_html_e( 'Usage Examples', 'lknwp-radio-browser' ); ?></h2>
                    <p><?php esc_html_e( 'Copy-and-paste ready shortcodes for common setups.', 'lknwp-radio-browser' ); ?></p>
                </header>

                <div class="lknwp-radio-example" data-sf>
                    <h4><?php esc_html_e( 'Complete list with filters (default)', 'lknwp-radio-browser' ); ?></h4>
                    <div class="lknwp-radio-code-block">
                        <code>[radio_browser_list player_page="player"]</code>
                        <button type="button" class="lknwp-radio-copy-btn" data-copy="<?php echo esc_attr( '[radio_browser_list player_page="player"]' ); ?>"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></button>
                    </div>
                </div>

                <div class="lknwp-radio-example" data-sf>
                    <h4><?php esc_html_e( 'Clean list without filters', 'lknwp-radio-browser' ); ?></h4>
                    <div class="lknwp-radio-code-block">
                        <code>[radio_browser_list player_page="player" hide_all_filters="yes"]</code>
                        <button type="button" class="lknwp-radio-copy-btn" data-copy="<?php echo esc_attr( '[radio_browser_list player_page="player" hide_all_filters="yes"]' ); ?>"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></button>
                    </div>
                </div>

                <div class="lknwp-radio-example" data-sf>
                    <h4><?php esc_html_e( 'Text search only', 'lknwp-radio-browser' ); ?></h4>
                    <div class="lknwp-radio-code-block">
                        <code>[radio_browser_list player_page="player" hide_country="yes" hide_limit="yes" hide_sort="yes" hide_order="yes"]</code>
                        <button type="button" class="lknwp-radio-copy-btn" data-copy="<?php echo esc_attr( '[radio_browser_list player_page="player" hide_country="yes" hide_limit="yes" hide_sort="yes" hide_order="yes"]' ); ?>"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></button>
                    </div>
                </div>

                <div class="lknwp-radio-example" data-sf>
                    <h4><?php esc_html_e( 'Fixed configuration (US radios, 10 stations, no filters)', 'lknwp-radio-browser' ); ?></h4>
                    <div class="lknwp-radio-code-block">
                        <code>[radio_browser_list player_page="player" countrycode="US" limit="10" hide_all_filters="yes"]</code>
                        <button type="button" class="lknwp-radio-copy-btn" data-copy="<?php echo esc_attr( '[radio_browser_list player_page="player" countrycode="US" limit="10" hide_all_filters="yes"]' ); ?>"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></button>
                    </div>
                </div>

                <div class="lknwp-radio-example" data-sf>
                    <h4><?php esc_html_e( 'List sorted by name (alphabetical)', 'lknwp-radio-browser' ); ?></h4>
                    <div class="lknwp-radio-code-block">
                        <code>[radio_browser_list player_page="player" sort="name"]</code>
                        <button type="button" class="lknwp-radio-copy-btn" data-copy="<?php echo esc_attr( '[radio_browser_list player_page="player" sort="name"]' ); ?>"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></button>
                    </div>
                </div>
            </section>

            <!-- ================= FAQ ================= -->
            <section class="lknwp-radio-panel" id="panel-faq" aria-labelledby="nav-faq">
                <header class="lknwp-radio-panel__head">
                    <h2>❓ <?php esc_html_e( 'Frequently Asked Questions', 'lknwp-radio-browser' ); ?></h2>
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
                        <p><?php esc_html_e( 'Go to Pages, hover over the page that contains [radio_browser_player] and read its slug in the URL. Use that value in player_page="…".', 'lknwp-radio-browser' ); ?></p>
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
