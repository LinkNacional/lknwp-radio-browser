<?php
/**
 * Template for Radio Browser Player Shortcode
 * 
 * Variables available:
 * - $stream: Radio stream URL
 * - $station_name: Station name
 * - $station_img: Station image URL
 * - $default_img_url: Default image URL
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

<script>
    // Global variables for the simple visualizer
    window.LKNWP_RADIO_BROWSER_PLUGIN_URL = '<?php echo defined('LKNWP_RADIO_BROWSER_PLUGIN_URL') ? esc_js(LKNWP_RADIO_BROWSER_PLUGIN_URL) : ''; ?>';
    window.LKNWP_RADIO_HOMEPAGE = '<?php echo !empty($station_homepage) ? esc_js($station_homepage) : ''; ?>';
    
    // Station data for display along with music
    window.LKNWP_STATION_CLICKCOUNT = <?php echo isset($station_clickcount) ? intval($station_clickcount) : 0; ?>;
    window.LKNWP_STATION_VOTES = <?php echo isset($station_votes) ? intval($station_votes) : 0; ?>;
</script>

<div class="lkp-player-wrapper">
    <div id="lknwp-radio-custom-player" class="lkp-player-container">
        
        <!-- Header: Station Image + Name -->
        <div id="lknwp-radio-header" class="lkp-header">
        <div id="lknwp-radio-img-parent" class="lkp-img-parent">
            <img src="<?php echo esc_attr($station_img); ?>" alt="<?php esc_attr_e( 'Radio logo', 'lknwp-radio-browser' ); ?>" class="lkp-station-img" onerror="this.onerror=null;this.src='<?php echo esc_js($default_img_url); ?>';">
        </div>
        <div id="lknwp-radio-station-name" class="lkp-station-name">
            <?php echo $station_name ? esc_html($station_name) : esc_html__( 'Online Radio', 'lknwp-radio-browser' ); ?>
        </div>
    </div>
    
    <!-- Audio Element -->
    <audio id="lknwp-radio-player" src="<?php echo esc_attr($stream); ?>" preload="auto" class="lkp-audio"></audio>
    
    <!-- Play Button com Visualizer Atrás -->
    <div id="lknwp-radio-play-section" class="lkp-play-section">
        
        <!-- Audio Visualizer (fica atrás do botão) -->
        <div id="lknwp-radio-audio-visualizer" class="lkp-audio-visualizer">
            <div id="lknwp-radio-visualizer-top" class="lkp-visualizer-top"></div>
            <div id="lknwp-radio-visualizer-bottom" class="lkp-visualizer-bottom"></div>
        </div>
        
        <!-- Play Button (fica na frente) -->
        <div id="lknwp-radio-play-btn-wrap" class="lkp-play-btn-wrap">
            <button id="lknwp-radio-play-btn" class="lkp-play-btn">
                <span id="lknwp-radio-play-icon">
                    <svg width="120" height="120" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="24" cy="24" r="24" fill="#e0e6f0"/>
                        <polygon points="18,15 36,24 18,33" fill="#232b36"/>
                    </svg>
                </span>
            </button>
        </div>

        <!-- Share Buttons (posição absoluta dentro da play section) -->
        <div class="lkp-share-section">
            <!-- Compartilhar (copia URL) -->
            <button id="lknwp-share-copy" class="lkp-share-btn" title="<?php esc_attr_e( 'Copy link', 'lknwp-radio-browser' ); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z" fill="currentColor"/>
                </svg>
            </button>

            <!-- Instagram -->
            <a id="lknwp-share-instagram" class="lkp-share-btn" title="<?php esc_attr_e( 'Share on Instagram', 'lknwp-radio-browser' ); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5" stroke="currentColor" stroke-width="2"/>
                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" stroke="currentColor" stroke-width="2"/>
                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke="currentColor" stroke-width="2"/>
                </svg>
            </a>

            <!-- WhatsApp -->
            <a id="lknwp-share-whatsapp" class="lkp-share-btn" title="<?php esc_attr_e( 'Share on WhatsApp', 'lknwp-radio-browser' ); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.63" fill="currentColor"/>
                </svg>
            </a>

            <!-- Twitter -->
            <a id="lknwp-share-twitter" class="lkp-share-btn" title="<?php esc_attr_e( 'Share on Twitter', 'lknwp-radio-browser' ); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z" stroke="currentColor" stroke-width="2" fill="none"/>
                </svg>
            </a>
        </div>
        
    </div>
   
    
    <!-- Current Song Block -->
    <div id="lknwp-radio-current-song-block" class="lkp-current-song-block">
        <div id="lknwp-radio-album-img" class="lkp-album-img"></div>
        <div class="lkp-song-info">
            <div id="lknwp-radio-artist" class="lkp-artist"></div>
            <div id="lknwp-radio-current-song" class="lkp-current-song"><?php esc_html_e( 'Loading...', 'lknwp-radio-browser' ); ?></div>
            <div id="lknwp-radio-station-stats" class="lkp-station-stats"></div>
        </div>
    </div>
    
    <!-- Volume Control -->
    <div class="lkp-volume-section">
        <label for="lknwp-radio-volume" class="lkp-volume-label"><?php esc_html_e( 'Volume', 'lknwp-radio-browser' ); ?></label>
        <div class="lkp-volume-controls">
            <input type="range" id="lknwp-radio-volume" min="0" max="1" step="0.01" value="0.2" class="lkp-volume-slider">
            <span id="lknwp-radio-volume-value" class="lkp-volume-display">20%</span>
        </div>
    </div>
    
</div>

<?php endif; ?>