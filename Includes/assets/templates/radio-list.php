<?php
/**
 * Template for Radio Browser List Shortcode
 * 
 * Variables available:
 * - $atts: Shortcode attributes
 * - $stations: Array of radio stations
 * - $sort_options: Array of sort options
 * - $plugin_url: Plugin URL
 * - $default_img_url: Default image URL
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="lrt-radio-wrap" id="lknwp-radio-list">
    
    <?php if ($atts['hide_all_filters'] !== 'yes'): ?>
    <!-- Navigation Form -->
    <nav class="lrt-radio-nav" id="lknwp-radio-list-nav">
    <form method="get" class="lrt-radio-form" action="#lknwp-radio-list">
            
            <!-- First Row: Country, Limit, Sort, Order -->
            <div class="lrt-radio-row lrt-radio-row--first">
                
                <?php if ($atts['hide_country'] !== 'yes'): ?>
                <!-- Country Field -->
                <div class="lrt-radio-field lrt-radio-field--country">
                    <label for="lrt_countrycode"><?php esc_html_e( 'Country', 'lknwp-radio-browser' ); ?></label>
                    <select id="lrt_countrycode" name="lrt_countrycode" class="lrt-radio-select lrt-radio-select--country">
                        <?php
                        // Cria o array com 'All Countries' na primeira posição
                        $countries = array_merge(
                            array('all' => '🌍 ' . __( 'All Countries', 'lknwp-radio-browser' )),
                            array(
                                'BR' => '🇧🇷 BR',
                                'US' => '🇺🇸 US',
                                'AR' => '🇦🇷 AR',
                                'CA' => '🇨🇦 CA',
                                'GB' => '🇬🇧 GB',
                                'FR' => '🇫🇷 FR',
                                'DE' => '🇩🇪 DE',
                                'ES' => '🇪🇸 ES',
                                'IT' => '🇮🇹 IT',
                                'PT' => '🇵🇹 PT',
                                'MX' => '🇲🇽 MX',
                                'CL' => '🇨🇱 CL',
                                'CO' => '🇨🇴 CO',
                                'PE' => '🇵🇪 PE',
                                'UY' => '🇺🇾 UY',
                                'PY' => '🇵🇾 PY',
                                'BO' => '🇧🇴 BO',
                                'EC' => '🇪🇨 EC',
                                'VE' => '🇻🇪 VE',
                                'AU' => '🇦🇺 AU',
                                'JP' => '🇯🇵 JP',
                                'KR' => '🇰🇷 KR',
                                'CN' => '🇨🇳 CN',
                                'IN' => '🇮🇳 IN',
                                'RU' => '🇷🇺 RU',
                                'NL' => '🇳🇱 NL',
                                'BE' => '🇧🇪 BE',
                                'CH' => '🇨🇭 CH',
                                'AT' => '🇦🇹 AT',
                                'SE' => '🇸🇪 SE',
                                'NO' => '🇳🇴 NO',
                                'DK' => '🇩🇰 DK',
                                'FI' => '🇫🇮 FI'
                            )
                        );
                        $selected_country = $atts['countrycode'];
                        if (empty($selected_country)) {
                            $selected_country = 'BR'; // Padrão Brasil
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
                
                <?php if ($atts['hide_limit'] !== 'yes'): ?>
                <!-- Limit Field -->
                <div class="lrt-radio-field lrt-radio-field--limit">
                    <label for="lrt_limit"><?php esc_html_e( 'Limit', 'lknwp-radio-browser' ); ?></label>
                    <input type="number" id="lrt_limit" name="lrt_limit" value="<?php echo esc_attr($atts['limit']); ?>" min="1" max="100" class="lrt-radio-input lrt-radio-input--small">
                </div>
                <?php endif; ?>
                
                <?php if ($atts['hide_sort'] !== 'yes'): ?>
                <!-- Sort Field -->
                <div class="lrt-radio-field lrt-radio-field--sort">
                    <label for="lrt_sort"><?php esc_html_e( 'Sort', 'lknwp-radio-browser' ); ?></label>
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
                <!-- Reverse Order Button -->
                <div class="lrt-radio-field lrt-radio-field--reverse">
                    <label for="lrt_reverse_btn"><?php esc_html_e( 'Order', 'lknwp-radio-browser' ); ?></label>
                    <button type="button" id="lrt_reverse_btn" class="lrt-radio-button lrt-radio-button--reverse">
                        <?php echo $atts['reverse'] === '1' 
                            ? esc_html__('Descending', 'lknwp-radio-browser') 
                            : esc_html__('Ascending', 'lknwp-radio-browser'); ?>
                    </button>
                </div>
                <?php endif; ?>

                 <!-- Genre Field -->
                <?php if ($atts['hide_genre'] !== 'yes'): ?>
                <div class="lrt-radio-field lrt-radio-field--genre">
                    <label for="lrt_genre"><?php esc_html_e( 'Genre', 'lknwp-radio-browser' ); ?></label>
                    <select id="lrt_genre" name="lrt_genre" class="lrt-radio-select lrt-radio-select--genre">
                        <option value="all" selected><?php esc_html_e( 'All Genres', 'lknwp-radio-browser' ); ?></option>
                        <?php
                        // Fetch genres/tags from Radio-Browser API
                        $tags = get_transient('lknwp_radio_tags');
                        if ($tags === false) {
                            $response = wp_remote_get('https://de2.api.radio-browser.info/json/tags', array('timeout' => 10));
                            if (!is_wp_error($response)) {
                                $body = wp_remote_retrieve_body($response);
                                $tags = json_decode($body);
                                set_transient('lknwp_radio_tags', $tags, 12 * HOUR_IN_SECONDS);
                            }
                        }
                        if (is_array($tags)) {
                            foreach ($tags as $tag) {
                                if (!empty($tag->name)) {
                                    $selected = (isset($_GET['lrt_genre']) && $_GET['lrt_genre'] === $tag->name) ? 'selected' : '';
                                    echo '<option value="' . esc_attr($tag->name) . '" ' . esc_attr($selected) . '>' . esc_html($tag->name) . '</option>';
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="lrt-radio-search-container">
                    <?php if ($atts['hide_search'] !== 'yes'): ?>
                    <!-- Search Field -->
                    <div class="lrt-radio-field lrt-radio-field--search">
                        <label for="lrt_radio_search"><?php esc_html_e( 'Search Radio', 'lknwp-radio-browser' ); ?></label>
                        <input type="text" id="lrt_radio_search" name="lrt_radio_search" value="<?php echo esc_attr($atts['search']); ?>" placeholder="<?php esc_attr_e( 'Search radio...', 'lknwp-radio-browser' ); ?>" class="lrt-radio-input lrt-radio-input--search">
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($atts['hide_button'] !== 'yes'): ?>
                    <!-- Submit Button -->
                    <div class="lrt-radio-field lrt-radio-field--submit">
                        <button type="submit" class="lrt-radio-button lrt-radio-button--submit"><?php esc_html_e( 'Search', 'lknwp-radio-browser' ); ?></button>
                    </div>
                    <?php endif; ?>
                </div>
                
            </div>
            
            <!-- Hidden Fields -->
            <input type="hidden" id="lrt_reverse" name="lrt_reverse" value="<?php echo esc_attr($atts['reverse']); ?>">
            <input type="hidden" id="lrt_player_base_url" name="lrt_player_base_url" value="<?php echo esc_attr(base64_encode($player_base_url)); ?>">
        </form>
    </nav>
    <?php endif; ?>

    <!-- Radio Stations List -->
    <ul class="lrt-radio-list" id="lknwp-radio-list-components">
        <?php if (!$stations || !is_array($stations) || count($stations) === 0): ?>
            <li class="lrt-radio-error"><?php esc_html_e('No radios found.', 'lknwp-radio-browser'); ?></li>
        <?php else: ?>
            <?php
            $count = 0;
            foreach ($stations as $station) {
                if ($count >= $atts['limit']) break;
                $name = esc_html($station->name);
                $img = !empty($station->favicon) ? esc_url($station->favicon) : $default_img_url;
                $radio_name_clean = str_replace(['/', '?', '#', '&'], '', $station->name);
                $radio_name_encoded = str_replace(' ', '%20', $radio_name_clean);
                $player_url = trailingslashit($player_base_url) . $radio_name_encoded . '/';
                $count++;
            ?>
                <li class="lrt-radio-station">
                    <a href="<?php echo esc_url($player_url); ?>" data-player-link="1" target="_blank" class="lrt-radio-station__link">
                        <img src="<?php echo esc_url($img); ?>" alt="<?php esc_attr_e( 'Radio logo', 'lknwp-radio-browser' ); ?>" class="lrt-radio-station__logo" onerror="this.onerror=null;this.src='<?php echo esc_url($default_img_url); ?>';">
                        <div class="lrt-radio-station__content">
                            <span class="lrt-radio-station__name"><?php echo esc_html($name); ?></span>
                        </div>
                    </a>
                </li>
            <?php
            }
        endif; ?>
    </ul>
</div>