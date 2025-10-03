<?php

namespace Lkn\LKNWP_Radio_Browser\Public;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.linknacional.com.br/wordpress/
 * @since      1.0.0
 *
 * @package    Lknwp_Radio_Browser
 * @subpackage Lknwp_Radio_Browser/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Lknwp_Radio_Browser
 * @subpackage Lknwp_Radio_Browser/public
 * @author     Link Nacional <contato@linknacional.com>
 */
class Lknwp_Radio_Browser_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		global $post;
		if (isset($post->post_content) && has_shortcode($post->post_content, 'radio_browser_list')) {
			wp_enqueue_style('lknwp-radio-list', plugin_dir_url( __FILE__ ) . 'css/lknwp-radio-browser-list.css', array(), $this->version, 'all' );
		}

		if (isset($post->post_content) && has_shortcode($post->post_content, 'radio_browser_player')) {
			wp_enqueue_style('lknwp-radio-player', plugin_dir_url( __FILE__ ) . 'css/lknwp-radio-browser-player.css', array(), $this->version, 'all' );
			wp_enqueue_style('lknwp-radio-audio-visualizer', plugin_dir_url( __FILE__ ) . 'css/lknwp-radio-browser-audio-visualizer.css', array(), $this->version, 'all' );
		}


		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/lknwp-radio-browser-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/lknwp-radio-browser-public.js', array( 'jquery' ), $this->version, false );


		global $post;
		if (isset($post->post_content) && has_shortcode($post->post_content, 'radio_browser_player')) {
			wp_enqueue_script('lknwp-radio-player', plugin_dir_url( __FILE__ ) . 'js/lknwp-radio-browser-player.js', array(), $this->version, true);
			wp_enqueue_script('lknwp-radio-player-song', plugin_dir_url( __FILE__ ) . 'js/lknwp-radio-browser-player-song.js', array(), $this->version, true);

			// Localize player scripts
			wp_localize_script('lknwp-radio-player', 'lknwpRadioTexts', array(
				'unableToPlay' => __('Unable to play this radio station. Please try again later or choose another station.', 'lknwp-radio-browser'),
				'listeningTo' => __('🎵 Listening to {station} - ', 'lknwp-radio-browser'),
				'onlineRadio' => __('Online Radio', 'lknwp-radio-browser')
			));	

			wp_localize_script('lknwp-radio-player-song', 'lknwpRadioTexts', array(
				'warning' => __('Warning: This radio uses insecure streaming (HTTP) and cannot be played on HTTPS pages. Ask the provider to enable HTTPS or access via HTTP.', 'lknwp-radio-browser'),
				'listeners' => __('listeners', 'lknwp-radio-browser'),
				'likes' => __('likes', 'lknwp-radio-browser'),
				'noSongFoundJson' => __('No song found in JSON', 'lknwp-radio-browser'),
				'noSongFoundHtml' => __('No song found in HTML', 'lknwp-radio-browser'),
				'responseNotJson' => __('Response is not JSON', 'lknwp-radio-browser'),
				'audioComponent' => __('Detected audio component', 'lknwp-radio-browser'),
				'corsBlocked' => __('CORS_BLOCKED: Opaque response, cannot read content', 'lknwp-radio-browser'),
				'networkError' => __('NETWORK_ERROR: Status 0, possible network or CORS issue', 'lknwp-radio-browser'),
				'audioStream' => __('AUDIO_STREAM: Response is an audio stream', 'lknwp-radio-browser'),
				'textTimeout' => __('TEXT_TIMEOUT: Text conversion exceeded 5 seconds', 'lknwp-radio-browser'),
				'contentTypeNotJson' => __('Content-Type is not JSON: ', 'lknwp-radio-browser')
			));
		}

		if (isset($post->post_content) && has_shortcode($post->post_content, 'radio_browser_list')) {
			wp_enqueue_script('lknwp-radio-list', plugin_dir_url( __FILE__ ) . 'js/lknwp-radio-browser-list.js', array(), $this->version, true);
			
			// Localize list script
			wp_localize_script('lknwp-radio-list', 'lknwpRadioTexts', array(
				'loadingRadios' => __('Loading radios...', 'lknwp-radio-browser'),
				'noRadiosFound' => __('No radios found.', 'lknwp-radio-browser'),
				'tryingAlternativeServers' => __('Trying alternative servers...', 'lknwp-radio-browser'),
				'reverseActive' => __('Reverse active', 'lknwp-radio-browser'),
				'reverseInactive' => __('Reverse inactive', 'lknwp-radio-browser'),
				'apiError' => __('Error querying API. ', 'lknwp-radio-browser')
			));
		}
	}
}
