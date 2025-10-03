
<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.linknacional.com.br
 * @since             1.0.0
 * @package           Lknwp_Radio_Browser
 *
 * @wordpress-plugin
 * Plugin Name:       Radio Browser for Wordpress
 * Plugin URI:        https://www.linknacional.com.br
 * Description:       WordPress plugin to list online radios and listen to live broadcasts directly on your site, using the Radio Browser API.
 * Version:           1.0.0
 * Author:            Link Nacional
 * Author URI:        https://www.linknacional.com.br/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       lknwp-radio-browser
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Plugin constants
 */
define( 'LKNWP_RADIO_BROWSER_VERSION', '1.0.0' );
define( 'LKNWP_RADIO_BROWSER_PLUGIN_FILE', __FILE__ );
define( 'LKNWP_RADIO_BROWSER_PLUGIN_NAME', 'lknwp-radio-browser' );
define( 'LKNWP_RADIO_BROWSER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'LKNWP_RADIO_BROWSER_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Autoloader using Composer
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
} else {
	// Manual autoloader fallback for PSR-4
	spl_autoload_register( function ( $class ) {
		$namespaces = [
			'Lkn\\LKNWP_Radio_Browser\\Admin\\' => plugin_dir_path( __FILE__ ) . 'Admin/',
			'Lkn\\LKNWP_Radio_Browser\\Public\\' => plugin_dir_path( __FILE__ ) . 'Public/',
			'Lkn\\LKNWP_Radio_Browser\\Includes\\' => plugin_dir_path( __FILE__ ) . 'Includes/'
		];
		foreach ($namespaces as $prefix => $base_dir) {
			$len = strlen( $prefix );
			if ( strncmp( $prefix, $class, $len ) !== 0 ) {
				continue;
			}
			$relative_class = substr( $class, $len );
			$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';
			if ( file_exists( $file ) ) {
				require $file;
				return;
			}
		}
	});
}

use Lkn\LKNWP_Radio_Browser\Includes\Lknwp_Radio_Browser;
use Lkn\LKNWP_Radio_Browser\Includes\Lknwp_Radio_Browser_Activator;
use Lkn\LKNWP_Radio_Browser\Includes\Lknwp_Radio_Browser_Deactivator;

/**
 * The code that runs during plugin activation.
 */
function activate_lknwp_radio_browser() {
	Lknwp_Radio_Browser_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_lknwp_radio_browser() {
	Lknwp_Radio_Browser_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_lknwp_radio_browser' );
register_deactivation_hook( __FILE__, 'deactivate_lknwp_radio_browser' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_lknwp_radio_browser() {
	$plugin = new Lknwp_Radio_Browser();
	$plugin->run();
}
run_lknwp_radio_browser();
