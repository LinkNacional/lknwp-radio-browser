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

<div class="wrap">
    <h1><?php esc_html_e( 'LKN Radio Browser - How to Use', 'lknwp-radio-browser' ); ?></h1>
    
    <div class="lknwp-radio-admin-content">

        <!-- Setup Instructions -->
        <div class="lknwp-radio-shortcode-section">
            <h2><?php esc_html_e( '🚀 Plugin Structure & Setup', 'lknwp-radio-browser' ); ?></h2>
            <div class="lknwp-radio-info">
                <h3><?php esc_html_e( 'This plugin is divided into two main shortcodes:', 'lknwp-radio-browser' ); ?></h3>
                <p><?php esc_html_e( 'Radio Player Shortcode: Displays the radio player.', 'lknwp-radio-browser' ); ?></p>
                <p><?php esc_html_e( 'Radio List Shortcode: Displays a list of radios.', 'lknwp-radio-browser' ); ?></p>

                <h4><?php esc_html_e( 'Step 1: Create the Player Page', 'lknwp-radio-browser' ); ?></h4>
                <ol>
                    <li><?php esc_html_e( 'Go to Pages > Add New.', 'lknwp-radio-browser' ); ?></li>
                    <li><?php esc_html_e( 'Create a page for the radio player.', 'lknwp-radio-browser' ); ?></li>
                    <li><?php esc_html_e( 'Add the shortcode for the player (see below for usage).', 'lknwp-radio-browser' ); ?></li>
                    <li><?php esc_html_e( 'Save the page.', 'lknwp-radio-browser' ); ?></li>
                </ol>
                <!-- Examples for the radio list page, shown after instructions -->
                <h4><?php esc_html_e( 'Step 2: Create the List Page', 'lknwp-radio-browser' ); ?></h4>
                <ol>
                    <li><?php esc_html_e( 'Go to Pages > Add New.', 'lknwp-radio-browser' ); ?></li>
                    <li><?php esc_html_e( 'Create a page to list the radios.', 'lknwp-radio-browser' ); ?></li>
                    <li><?php esc_html_e( 'Add the radio list shortcode: [radio_browser_list player_page="player-page"] (replace "player-page" with the actual slug of your player page).', 'lknwp-radio-browser' ); ?></li>
                    <div class="lknwp-radio-example">
                        <span><?php esc_html_e( 'Example:', 'lknwp-radio-browser' ); ?></span>
                        <span>https://www.url.com/radio-list/<strong>player-page</strong></span>
                    </div>
                    <div class="lknwp-radio-example">
                        <span><?php esc_html_e( 'Another Example:', 'lknwp-radio-browser' ); ?></span>
                        <span>https://www.url.com/adjacent-page/radio-list/<strong>player-page</strong></span>
                    </div>
                    <li><?php esc_html_e( 'Save the page.', 'lknwp-radio-browser' ); ?></li>
                </ol>
            </div>
        </div>
        
        <!-- Radio Player Shortcode -->
        <div class="lknwp-radio-shortcode-section">
            <h2><?php esc_html_e( '🎵 Radio Player Shortcode', 'lknwp-radio-browser' ); ?></h2>
            <p><?php esc_html_e( 'Use this shortcode to display the radio player on a specific page:', 'lknwp-radio-browser' ); ?></p>
            
            <div class="lknwp-radio-code-block">
                <code>[radio_browser_player]</code>
                <button class="lknwp-radio-copy-btn" onclick="copyToClipboard('[radio_browser_player]', this)"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></button>
            </div>
            
            <div class="lknwp-radio-info">
                <h4><?php esc_html_e( '📋 How it works:', 'lknwp-radio-browser' ); ?></h4>
                <ul>
                    <li><?php esc_html_e( 'Create a page (e.g., "Player" with slug "player")', 'lknwp-radio-browser' ); ?></li>
                    <li><?php esc_html_e( 'Add the shortcode', 'lknwp-radio-browser' ); ?> <code>[radio_browser_player]</code></li>
                    <li><?php esc_html_e( 'The player will automatically receive URL parameters', 'lknwp-radio-browser' ); ?></li>
                    <li><?php esc_html_e( 'Works with links from the radio list', 'lknwp-radio-browser' ); ?></li>
                </ul>
            </div>
        </div>

        <!-- Radio List Shortcode -->
        <div class="lknwp-radio-shortcode-section">
            <h2><?php esc_html_e( '📻 Radio List Shortcode', 'lknwp-radio-browser' ); ?></h2>
            <p><?php esc_html_e( 'Use this shortcode to display a list of radios with filters:', 'lknwp-radio-browser' ); ?></p>
            
            <div class="lknwp-radio-code-block">
                <code>[radio_browser_list player_page="player"]</code>
                <button class="lknwp-radio-copy-btn" onclick="copyToClipboard('[radio_browser_list player_page=&quot;player&quot;]', this)"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></button>
            </div>

            <div class="lknwp-radio-info">
                <h4><?php esc_html_e( '⚙️ Basic Parameters:', 'lknwp-radio-browser' ); ?></h4>
                <table class="lknwp-radio-params-table">
                    <tr>
                        <td><code>player_page</code></td>
                        <td><?php esc_html_e( 'Player page slug (required)', 'lknwp-radio-browser' ); ?></td>
                        <td><code>"player"</code></td>
                    </tr>
                    <tr>
                        <td><code>countrycode</code></td>
                        <td><?php esc_html_e( 'Country code (BR, US, FR, etc.)', 'lknwp-radio-browser' ); ?></td>
                        <td><code>"BR"</code></td>
                    </tr>
                    <tr>
                        <td><code>limit</code></td>
                        <td><?php esc_html_e( 'Number of radios to display', 'lknwp-radio-browser' ); ?></td>
                        <td><code>20</code></td>
                    </tr>

                    <tr>
                        <td><code>sort</code></td>
                        <td><?php esc_html_e( 'Sort order (clickcount, name, random, bitrate)', 'lknwp-radio-browser' ); ?></td>
                        <td><code>"clickcount"</code></td>
                    </tr>
                    <tr>
                        <td><code>reverse</code></td>
                        <td><?php esc_html_e( 'Reverse order (1 or 0)', 'lknwp-radio-browser' ); ?></td>
                        <td><code>"1"</code></td>
                    </tr>
                    <tr>
                        <td><code>search</code></td>
                        <td><?php esc_html_e( 'Search term', 'lknwp-radio-browser' ); ?></td>
                        <td><code>""</code></td>
                    </tr>
                </table>
            </div>

            <div class="lknwp-radio-info">
                <h4><?php esc_html_e( '🎛️ Parameters to Hide Filters:', 'lknwp-radio-browser' ); ?></h4>
                <table class="lknwp-radio-params-table">
                    <tr>
                        <td><code>hide_country</code></td>
                        <td><?php esc_html_e( 'Hide Country field', 'lknwp-radio-browser' ); ?></td>
                        <td><code>"yes"</code> <?php esc_html_e( 'or', 'lknwp-radio-browser' ); ?> <code>"no"</code></td>
                    </tr>
                    <tr>
                        <td><code>hide_limit</code></td>
                        <td><?php esc_html_e( 'Hide Limit field', 'lknwp-radio-browser' ); ?></td>
                        <td><code>"yes"</code> <?php esc_html_e( 'or', 'lknwp-radio-browser' ); ?> <code>"no"</code></td>
                    </tr>
                    <tr>
                        <td><code>hide_sort</code></td>
                        <td><?php esc_html_e( 'Hide Sort field', 'lknwp-radio-browser' ); ?></td>
                        <td><code>"yes"</code> <?php esc_html_e( 'or', 'lknwp-radio-browser' ); ?> <code>"no"</code></td>
                    </tr>
                    <tr>
                        <td><code>hide_order</code></td>
                        <td><?php esc_html_e( 'Hide Order button', 'lknwp-radio-browser' ); ?></td>
                        <td><code>"yes"</code> <?php esc_html_e( 'or', 'lknwp-radio-browser' ); ?> <code>"no"</code></td>
                    </tr>
                    <tr>
                        <td><code>hide_search</code></td>
                        <td><?php esc_html_e( 'Hide Search field', 'lknwp-radio-browser' ); ?></td>
                        <td><code>"yes"</code> <?php esc_html_e( 'or', 'lknwp-radio-browser' ); ?> <code>"no"</code></td>
                    </tr>
                    <tr>
                        <td><code>hide_button</code></td>
                        <td><?php esc_html_e( 'Hide Search button', 'lknwp-radio-browser' ); ?></td>
                        <td><code>"yes"</code> <?php esc_html_e( 'or', 'lknwp-radio-browser' ); ?> <code>"no"</code></td>
                    </tr>
                    <tr>
                        <td><code>hide_all_filters</code></td>
                        <td><?php esc_html_e( 'Hide all filters', 'lknwp-radio-browser' ); ?></td>
                        <td><code>"yes"</code> <?php esc_html_e( 'or', 'lknwp-radio-browser' ); ?> <code>"no"</code></td>
                    </tr>
                    <tr>
                        <td><code>hide_genre</code></td>
                        <td><?php esc_html_e( 'Hide Genre field in filter and genre component in the list', 'lknwp-radio-browser' ); ?></td>
                        <td><code>"yes"</code> <?php esc_html_e( 'or', 'lknwp-radio-browser' ); ?> <code>"no"</code></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Examples Section -->
        <div class="lknwp-radio-shortcode-section">
            <h2><?php esc_html_e( '💡 Usage Examples', 'lknwp-radio-browser' ); ?></h2>
            
            <div class="lknwp-radio-example">
                <h4><?php esc_html_e( 'Complete list with filters (default):', 'lknwp-radio-browser' ); ?></h4>
                <div class="lknwp-radio-code-block">
                    <code>[radio_browser_list player_page="player"]</code>
                    <button class="lknwp-radio-copy-btn" onclick="copyToClipboard('[radio_browser_list player_page=&quot;player&quot;]')"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></button>
                </div>
            </div>

            <div class="lknwp-radio-example">
                <h4><?php esc_html_e( 'Clean list without filters:', 'lknwp-radio-browser' ); ?></h4>
                <div class="lknwp-radio-code-block">
                    <code>[radio_browser_list player_page="player" hide_all_filters="yes"]</code>
                    <button class="lknwp-radio-copy-btn" onclick="copyToClipboard('[radio_browser_list player_page=&quot;player&quot; hide_all_filters=&quot;yes&quot;]')"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></button>
                </div>
            </div>

            <div class="lknwp-radio-example">
                <h4><?php esc_html_e( 'Text search only:', 'lknwp-radio-browser' ); ?></h4>
                <div class="lknwp-radio-code-block">
                    <code>[radio_browser_list player_page="player" hide_country="yes" hide_limit="yes" hide_sort="yes" hide_order="yes"]</code>
                    <button class="lknwp-radio-copy-btn" onclick="copyToClipboard('[radio_browser_list player_page=&quot;player&quot; hide_country=&quot;yes&quot; hide_limit=&quot;yes&quot; hide_sort=&quot;yes&quot; hide_order=&quot;yes&quot;]')"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></button>
                </div>
            </div>

            <div class="lknwp-radio-example">
                <h4><?php esc_html_e( 'Fixed configuration (US radios, 10 stations, no filters):', 'lknwp-radio-browser' ); ?></h4>
                <div class="lknwp-radio-code-block">
                    <code>[radio_browser_list player_page="player" countrycode="US" limit="10" hide_all_filters="yes"]</code>
                    <button class="lknwp-radio-copy-btn" onclick="copyToClipboard('[radio_browser_list player_page=&quot;player&quot; countrycode=&quot;US&quot; limit=&quot;10&quot; hide_all_filters=&quot;yes&quot;]')"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></button>
                </div>
            </div>

            <div class="lknwp-radio-example">
                <h4><?php esc_html_e( 'List sorted by name (alphabetical):', 'lknwp-radio-browser' ); ?></h4>
                <div class="lknwp-radio-code-block">
                    <code>[radio_browser_list player_page="player" sort="name"]</code>
                    <button class="lknwp-radio-copy-btn" onclick="copyToClipboard('[radio_browser_list player_page=&quot;player&quot; sort=&quot;name&quot;]')"><?php esc_html_e( 'Copy', 'lknwp-radio-browser' ); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>