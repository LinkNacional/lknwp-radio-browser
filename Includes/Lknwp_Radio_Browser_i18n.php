<?php

namespace Lkn\LKNWP_Radio_Browser\Includes;

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.linknacional.com.br/wordpress/
 * @since      1.0.0
 *
 * @package    Lknwp_Radio_Browser
 * @subpackage Lknwp_Radio_Browser/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Lknwp_Radio_Browser
 * @subpackage Lknwp_Radio_Browser/includes
 * @author     Link Nacional <contato@linknacional.com>
 */
class Lknwp_Radio_Browser_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'lknwp-radio-browser',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/Languages/'
		);

	}


}
