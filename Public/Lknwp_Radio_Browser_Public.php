<?php

namespace Lkn\LKNWP_Radio_Browser\PublicView;

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
		$content = isset($post->post_content) ? $post->post_content : '';

		// Lista — carrega o CSS de acordo com o layout usado no conteúdo.
		$list_layouts = $this->get_content_shortcode_layouts($content, 'radio_browser_list');
		if (!empty($list_layouts)) {
			wp_enqueue_style('lknwp-colors', plugin_dir_url(__FILE__) . '../Includes/assets/css/colors.css', array(), $this->version, 'all');
			if (isset($list_layouts['modern'])) {
				wp_enqueue_style('lknwp-radio-list', plugin_dir_url( __FILE__ ) . 'css/lknwp-radio-browser-list.css', array(), $this->version, 'all' );
			}
			if (isset($list_layouts['legacy'])) {
				wp_enqueue_style('lknwp-radio-list-legacy', plugin_dir_url( __FILE__ ) . 'css/lknwp-radio-browser-list-legacy.css', array(), $this->version, 'all' );
			}
		}

		// Player — carrega o CSS de acordo com o layout usado no conteúdo.
		$player_layouts = $this->get_content_shortcode_layouts($content, 'radio_browser_player');
		if (!empty($player_layouts)) {
			wp_enqueue_style('lknwp-colors', plugin_dir_url(__FILE__) . '../Includes/assets/css/colors.css', array(), $this->version, 'all');
			if (isset($player_layouts['modern'])) {
				wp_enqueue_style('lknwp-radio-player', plugin_dir_url( __FILE__ ) . 'css/lknwp-radio-browser-player.css', array(), $this->version, 'all' );
				wp_enqueue_style('lknwp-radio-audio-visualizer', plugin_dir_url( __FILE__ ) . 'css/lknwp-radio-browser-audio-visualizer.css', array(), $this->version, 'all' );
			}
			if (isset($player_layouts['legacy'])) {
				wp_enqueue_style('lknwp-radio-player-legacy', plugin_dir_url( __FILE__ ) . 'css/lknwp-radio-browser-player-legacy.css', array(), $this->version, 'all' );
				wp_enqueue_style('lknwp-radio-audio-visualizer-legacy', plugin_dir_url( __FILE__ ) . 'css/lknwp-radio-browser-audio-visualizer-legacy.css', array(), $this->version, 'all' );
			}
		}


		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/lknwp-radio-browser-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Descobre quais layouts ("modern"/"legacy") um shortcode usa dentro do conteúdo.
	 * Como o layout agora é um atributo (`layout="modern"`), é preciso ler os
	 * atributos do shortcode — e não só a presença dele — para enfileirar os assets certos.
	 *
	 * @param string $content Conteúdo do post.
	 * @param string $tag     Nome do shortcode (ex.: radio_browser_list).
	 * @return array<string,bool> Mapa layout => true (ex.: array('modern' => true)).
	 */
	private function get_content_shortcode_layouts($content, $tag) {
		$layouts = array();
		if (!is_string($content) || $content === '') {
			return $layouts;
		}
		$pattern = get_shortcode_regex(array($tag));
		if (preg_match_all('/' . $pattern . '/s', $content, $matches) && !empty($matches[3])) {
			foreach ($matches[3] as $atts_str) {
				$atts = shortcode_parse_atts($atts_str);
				$is_modern = is_array($atts) && isset($atts['layout'])
					&& strtolower(trim((string) $atts['layout'])) === 'modern';
				$layouts[$is_modern ? 'modern' : 'legacy'] = true;
			}
		}
		return $layouts;
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
		if (isset($post->post_content)) {
			$player_layouts = $this->get_content_shortcode_layouts($post->post_content, 'radio_browser_player');
			if (isset($player_layouts['legacy'])) {
				$this->enqueue_player_scripts('-legacy');
			}
			if (isset($player_layouts['modern'])) {
				$this->enqueue_player_scripts('');
			}

			$list_layouts = $this->get_content_shortcode_layouts($post->post_content, 'radio_browser_list');
			if (isset($list_layouts['legacy'])) {
				$this->enqueue_list_scripts('-legacy');
			}
			if (isset($list_layouts['modern'])) {
				$this->enqueue_list_scripts('');
			}
		}
	}

	/**
	 * Enfileira e localiza os scripts do player.
	 *
	 * @since    1.0.0
	 * @param    string    $suffix    Sufixo do layout ('' para o atual, '-legacy' para o antigo).
	 */
	private function enqueue_player_scripts($suffix) {

		$player_handle = 'lknwp-radio-player' . $suffix;
		$song_handle = 'lknwp-radio-player-song' . $suffix;

		wp_enqueue_script($player_handle, plugin_dir_url( __FILE__ ) . 'js/lknwp-radio-browser-player' . $suffix . '.js', array(), $this->version, true);
		wp_enqueue_script($song_handle, plugin_dir_url( __FILE__ ) . 'js/lknwp-radio-browser-player-song' . $suffix . '.js', array(), $this->version, true);

		// Localize player scripts
		wp_localize_script($player_handle, 'lknwpRadioTextsPlayer', array(
			'unableToPlay' => __('Unable to play this radio station. Please try again later or choose another station.', 'lknwp-radio-browser'),
			'listeningTo' => __('🎵 Listening to {station} - ', 'lknwp-radio-browser'),
			'onlineRadio' => __('Online Radio', 'lknwp-radio-browser')
		));

		$default_album_url = defined('LKNWP_RADIO_BROWSER_PLUGIN_URL') ? LKNWP_RADIO_BROWSER_PLUGIN_URL . 'Includes/assets/images/default-radio-album.gif' : './Includes/assets/images/default-radio-album.gif';

		wp_localize_script($song_handle, 'lknwpRadioTextsSong', array(
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
			'contentTypeNotJson' => __('Content-Type is not JSON: ', 'lknwp-radio-browser'),
			'defaultAlbumUrl' => $default_album_url,
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'metadataNonce' => wp_create_nonce('lknwp_radio_metadata')
		));
	}

	/**
	 * Enfileira e localiza os scripts da lista de rádios.
	 *
	 * @since    1.0.0
	 * @param    string    $suffix    Sufixo do layout ('' para o atual, '-legacy' para o antigo).
	 */
	private function enqueue_list_scripts($suffix) {

		$handle = 'lknwp-radio-list' . $suffix;

		wp_enqueue_script($handle, plugin_dir_url( __FILE__ ) . 'jsCompiled/lknwp-radio-browser-list' . $suffix . '.COMPILED.js', array('jquery'), $this->version, true);

		// Localize list script
		$default_img_url = defined('LKNWP_RADIO_BROWSER_PLUGIN_URL') ? LKNWP_RADIO_BROWSER_PLUGIN_URL . 'Includes/assets/images/default-radio.png' : './Includes/assets/images/default-radio.png';

		// Busca a base do player igual ao template
		wp_localize_script($handle, 'lknwpRadioTextsList', array(
			'loadingRadios' => __('Loading radios...', 'lknwp-radio-browser'),
			'noRadiosFound' => __('No radios found.', 'lknwp-radio-browser'),
			'tryingAlternativeServers' => __('Trying alternative servers...', 'lknwp-radio-browser'),
			'ascending' => __('Menor', 'lknwp-radio-browser'),
			'descending' => __('Maior', 'lknwp-radio-browser'),
			'apiError' => __('Error querying API. ', 'lknwp-radio-browser'),
			'placeholder' => __('Select genre', 'lknwp-radio-browser'),
			'defaultImgUrl' => $default_img_url,
			'favorite' => __('Favoritar', 'lknwp-radio-browser'),
			'onAir' => __('OUVINDO AGORA', 'lknwp-radio-browser'),
			'logoAlt' => __('Radio logo', 'lknwp-radio-browser'),
			'play' => __('Play', 'lknwp-radio-browser'),
			'titleDiscover' => __('Descobrir rádios', 'lknwp-radio-browser'),
			'titleFavorites' => __('Favoritos', 'lknwp-radio-browser'),
			'titleRecents' => __('Recentes', 'lknwp-radio-browser'),
			'noFavorites' => __('Você ainda não favoritou nenhuma rádio.', 'lknwp-radio-browser'),
			'noRecents' => __('Você ainda não ouviu nenhuma rádio.', 'lknwp-radio-browser')
		));
	}
}
