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

<script>
window.LKNWP_RADIO_BROWSER_PLUGIN_URL = "<?php echo esc_js($plugin_url); ?>";
window.LKNWP_PLAYER_PAGE_SLUG = "<?php echo esc_js($atts['player_page']); ?>";
</script>

<div class="lrt-radio-wrap">
    
    <?php if ($atts['hide_all_filters'] !== 'yes'): ?>
    <!-- Navigation Form -->
    <nav class="lrt-radio-nav">
        <form method="get" class="lrt-radio-form">
            
            <!-- First Row: Country, Limit, Sort, Order -->
            <div class="lrt-radio-row lrt-radio-row--first">
                
                <?php if ($atts['hide_country'] !== 'yes'): ?>
                <!-- Country Field -->
                <div class="lrt-radio-field lrt-radio-field--country">
                    <label for="lrt_countrycode"><?php esc_html_e( 'Country', 'lknwp-radio-browser' ); ?></label>
                    <select id="lrt_countrycode" name="lrt_countrycode" class="lrt-radio-select lrt-radio-select--country">
                        <?php
                        $countries = array(
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
                        );
                        
                        // Add translated "All Countries" option
                        $countries[''] = '🌍 ' . __( 'All Countries', 'lknwp-radio-browser' );
                        
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
                
            </div>
            
            <!-- Second Row: Search and Submit -->
            <div class="lrt-radio-row lrt-radio-row--second">
                
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
            
            <!-- Hidden Fields -->
            <input type="hidden" id="lrt_reverse" name="lrt_reverse" value="<?php echo esc_attr($atts['reverse']); ?>">
        </form>
    </nav>
    <?php endif; ?>

    <!-- Radio Stations List -->
    <ul class="lrt-radio-list">
        
        <?php
        $count = 0;
        foreach ($stations as $station):
            if ($count >= $atts['limit']) break;
            
            $name = esc_html($station->name);
            $img = !empty($station->favicon) ? esc_url($station->favicon) : $default_img_url;
            
            // Gerar URL amigável - deixa o navegador formatar
            $radio_name_clean = str_replace(['/', '?', '#', '&'], '', $station->name);
            $radio_name_encoded = str_replace(' ', '%20', $radio_name_clean);
            $player_url = home_url('/' . $atts['player_page'] . '/' . $radio_name_encoded . '/');
            
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
        
        <?php endforeach; ?>
        
    </ul>
</div>