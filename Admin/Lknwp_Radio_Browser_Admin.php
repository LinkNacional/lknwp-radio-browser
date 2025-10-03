<?php

namespace Lkn\LKNWP_Radio_Browser\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.linknacional.com.br/wordpress/
 * @since      1.0.0
 *
 * @package    Lknwp_Radio_Browser
 * @subpackage Lknwp_Radio_Browser/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Lknwp_Radio_Browser
 * @subpackage Lknwp_Radio_Browser/admin
 * @author     Link Nacional <contato@linknacional.com>
 */
class Lknwp_Radio_Browser_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// Add admin menu
		add_action('admin_menu', array($this, 'add_admin_menu'));

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Lknwp_Radio_Browser_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Lknwp_Radio_Browser_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/lknwp-radio-browser-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Lknwp_Radio_Browser_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Lknwp_Radio_Browser_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/lknwp-radio-browser-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add admin menu page
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'LKN Radio Browser', 'lknwp-radio-browser' ),           // Page title
			__( 'LKN Radio Browser', 'lknwp-radio-browser' ),           // Menu title
			'manage_options',              // Capability
			'lknwp-radio-browser',         // Menu slug
			array($this, 'admin_page'),    // Callback function
			'dashicons-format-audio',      // Icon
			30                             // Position
		);
	}

	/**
	 * Display admin page
	 *
	 * @since    1.0.0
	 */
	public function admin_page() {
		// Enqueue specific styles and scripts for this page
		wp_enqueue_style(
			$this->plugin_name . '-admin-help', 
			plugin_dir_url(__FILE__) . 'css/lknwp-radio-browser-admin-help.css', 
			array(), 
			$this->version, 
			'all'
		);
		
		wp_enqueue_script(
			$this->plugin_name . '-admin-help', 
			plugin_dir_url(__FILE__) . 'js/lknwp-radio-browser-admin-help.js', 
			array('jquery'), 
			$this->version, 
			true
		);
		
		// Localize admin script
		wp_localize_script($this->plugin_name . '-admin-help', 'lknwpRadioTexts', array(
			'copyFallbackError' => __('Fallback: Could not copy', 'lknwp-radio-browser'),
			'buttonNotFound' => __('Button not found or invalid', 'lknwp-radio-browser'),
			'copied' => __('Copied!', 'lknwp-radio-browser'),
			'clickToCopy' => __('Click to copy shortcode', 'lknwp-radio-browser'),
			'copyError' => __('Copy error: ', 'lknwp-radio-browser'),
			'fallbackCopyError' => __('Fallback: Error copying', 'lknwp-radio-browser')
		));
		
		// Prepare variables for template
		$plugin_name = $this->plugin_name;
		$version = $this->version;
		
		// Load template
		include plugin_dir_path(__FILE__) . '../Includes/assets/templates/admin-help-page.php';
	}

}
