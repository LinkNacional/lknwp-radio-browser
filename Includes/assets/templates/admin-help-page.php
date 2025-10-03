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
    <h1><?php esc_htmlesc_html_e( 'LKN Radio Browser - How to Use', 'lknwp-radio-browser' ); ?></h1>
    
    <div class="lknwp-radio-admin-content">
        
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
                    <li><?php esc_htmlesc_html_e( 'Add the shortcode', 'lknwp-radio-browser' ); ?> <code>[radio_browser_player]</code></li>
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

        <!-- Setup Instructions -->
        <div class="lknwp-radio-shortcode-section">
            <h2><?php esc_html_e( '🚀 How to Configure', 'lknwp-radio-browser' ); ?></h2>
            <div class="lknwp-radio-info">
                <h4><?php esc_html_e( '1. Create the pages:', 'lknwp-radio-browser' ); ?></h4>
                <ol>
                    <li><?php esc_html_e( 'Go to <strong>Pages > Add New</strong>', 'lknwp-radio-browser' ); ?></li>
                    <li><?php esc_html_e( 'Create a page called "Radios" with slug "radios"', 'lknwp-radio-browser' ); ?></li>
                    <li><?php esc_html_e( 'Add the shortcode: <code>[radio_browser_list player_page="player"]</code>', 'lknwp-radio-browser' ); ?></li>
                    <li><?php esc_html_e( 'Create another page called "Player" with slug "player"', 'lknwp-radio-browser' ); ?></li>
                    <li><?php esc_html_e( 'Add the shortcode: <code>[radio_browser_player]</code>', 'lknwp-radio-browser' ); ?></li>
                </ol>

                <h4><?php esc_html_e( '2. Add to menu:', 'lknwp-radio-browser' ); ?></h4>
                <ol>
                    <li><?php esc_html_e( 'Go to <strong>Appearance > Menus</strong>', 'lknwp-radio-browser' ); ?></li>
                    <li><?php esc_html_e( 'Add the "Radios" page to the menu', 'lknwp-radio-browser' ); ?></li>
                    <li><?php esc_html_e( 'The "Player" page does not need to be in the menu (it is accessed automatically)', 'lknwp-radio-browser' ); ?></li>
                </ol>

                <h4><?php esc_html_e( '3. Customize (optional):', 'lknwp-radio-browser' ); ?></h4>
                <ul>
                    <li><?php esc_html_e( 'Use the <code>hide_*</code> parameters to hide unnecessary filters', 'lknwp-radio-browser' ); ?></li>
                    <li><?php esc_html_e( 'Configure default values with <code>countrycode</code>, <code>limit</code>, etc.', 'lknwp-radio-browser' ); ?></li>
                    <li><?php esc_html_e( 'Customize styles via theme CSS', 'lknwp-radio-browser' ); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>