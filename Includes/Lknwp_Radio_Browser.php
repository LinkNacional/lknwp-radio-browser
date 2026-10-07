<?php

namespace Lkn\LKNWP_Radio_Browser\Includes;

use Lkn\LKNWP_Radio_Browser\Admin\Lknwp_Radio_Browser_Admin;
use Lkn\LKNWP_Radio_Browser\PublicView\Lknwp_Radio_Browser_Public;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.linknacional.com.br/wordpress/
 * @since      1.0.0
 *
 * @package    Lknwp_Radio_Browser
 * @subpackage Lknwp_Radio_Browser/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Lknwp_Radio_Browser
 * @subpackage Lknwp_Radio_Browser/includes
 * @author     Link Nacional <contato@linknacional.com>
 */
class Lknwp_Radio_Browser {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Lknwp_Radio_Browser_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'LKNWP_RADIO_BROWSER_VERSION' ) ) {
			$this->version = LKNWP_RADIO_BROWSER_VERSION;
		} else {
			$this->version = '1.1.2';
		}
		$this->plugin_name = 'lknwp-radio-browser';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Lknwp_Radio_Browser_Loader. Orchestrates the hooks of the plugin.
	 * - Lknwp_Radio_Browser_Admin. Defines all hooks for the admin area.
	 * - Lknwp_Radio_Browser_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		$this->loader = new Lknwp_Radio_Browser_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Lknwp_Radio_Browser_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles', 200 );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );

	}




	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Lknwp_Radio_Browser_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Register radio browser shortcodes
		$this->register_radio_browser_shortcodes();

		// URLs amigáveis para o player
		$this->loader->add_action('init', $this, 'register_player_rewrite_rules');
		$this->loader->add_action('save_post', $this, 'handle_player_page_changes', 10, 2);
		$this->loader->add_filter('query_vars', $this, 'add_player_query_vars');

		// Endpoint (proxy) para buscar metadados do stream no servidor (evita CORS no navegador)
		$this->loader->add_action('wp_ajax_lknwp_radio_metadata', $this, 'ajax_radio_metadata');
		$this->loader->add_action('wp_ajax_nopriv_lknwp_radio_metadata', $this, 'ajax_radio_metadata');

		// Endpoint (proxy same-origin) que reencaminha o áudio do stream, para o
		// visualizador (Web Audio API) funcionar mesmo quando a rádio não usa CORS.
		$this->loader->add_action('wp_ajax_lknwp_radio_stream', $this, 'ajax_radio_stream');
		$this->loader->add_action('wp_ajax_nopriv_lknwp_radio_stream', $this, 'ajax_radio_stream');

	}

	/**
	 * Register shortcodes for radio list and player
	 */
	public function register_radio_browser_shortcodes() {
		add_shortcode('radio_browser_list', array($this, 'radio_browser_list_shortcode'));
		add_shortcode('radio_browser_player', array($this, 'radio_browser_player_shortcode'));
	}

	/**
	 * Resolve o layout pedido pelo atributo `layout`.
	 * Válidos: "modern" e "legacy" (case-insensitive). Ausente = legacy.
	 * Qualquer outro valor é inválido — cai no legacy e sinaliza `invalid`.
	 *
	 * @param mixed $atts Atributos do shortcode.
	 * @return array{layout:string,invalid:bool}
	 */
	private static function resolve_layout($atts) {
		if (!is_array($atts) || !array_key_exists('layout', $atts)) {
			return array('layout' => 'legacy', 'invalid' => false);
		}
		$value = strtolower(trim(is_scalar($atts['layout']) ? (string) $atts['layout'] : ''));
		if ($value === 'modern') {
			return array('layout' => 'modern', 'invalid' => false);
		}
		if ($value === 'legacy') {
			return array('layout' => 'legacy', 'invalid' => false);
		}
		return array('layout' => 'legacy', 'invalid' => true);
	}

	/**
	 * Aviso (apenas para quem pode editar) de `layout` inválido.
	 * Visitantes comuns não veem — nesse caso o layout legacy é renderizado.
	 *
	 * @param mixed $atts Atributos do shortcode.
	 * @return string HTML do aviso (vazio se não aplicável).
	 */
	private static function layout_error_notice($atts) {
		if (!current_user_can('edit_posts')) {
			return '';
		}
		$value = (is_array($atts) && isset($atts['layout']) && is_scalar($atts['layout']))
			? (string) $atts['layout']
			: '';
		return '<div class="lknwp-radio-layout-notice">' .
			'<strong>' . esc_html__('Radio Browser', 'lknwp-radio-browser') . ':</strong> ' .
			sprintf(
				/* translators: %s: the invalid layout value provided in the shortcode. */
				esc_html__('unknown layout "%s". Use layout="modern" or layout="legacy". Showing the legacy layout.', 'lknwp-radio-browser'),
				esc_html($value)
			) .
			'</div>';
	}

	/**
	 * Shortcode do player. O layout é escolhido pelo atributo `layout`:
	 * "modern" para o layout novo; "legacy" (ou ausente) usa o legacy.
	 * Um valor inválido cai no legacy e mostra um aviso para quem edita.
	 *
	 * @param array|string $atts Atributos do shortcode.
	 * @return string HTML do player.
	 */
	public function radio_browser_player_shortcode($atts = array()) {
		$resolved = self::resolve_layout($atts);
		$template = $resolved['layout'] === 'modern'
			? 'assets/templates/radio-player.php'
			: 'assets/templates/radio-player-legacy.php';
		$output = $this->render_radio_browser_player($template);
		if ($resolved['invalid']) {
			$output = self::layout_error_notice($atts) . $output;
		}
		return $output;
	}

	/**
	 * Renderiza o player usando o template informado.
	 *
	 * @param string $template Caminho relativo do template (dentro do plugin).
	 * @return string HTML do player.
	 */
	private function render_radio_browser_player($template) {
		$radio_name = get_query_var('radio_name');
		$default_img_url = defined('LKNWP_RADIO_BROWSER_PLUGIN_URL') ? LKNWP_RADIO_BROWSER_PLUGIN_URL . 'Includes/assets/images/default-radio.png' : './Includes/assets/images/default-radio.png';
		
		if ($radio_name) {
			// URL amigável: decodifica o nome da rádio da URL
			$radio_name_decoded = str_replace('%20', ' ', urldecode($radio_name));
			add_filter('document_title_parts', function($title) use ($radio_name_decoded) {
				$title['title'] = esc_html($radio_name_decoded);
				if (isset($title['site'])) unset($title['site']);
				if (isset($title['tagline'])) unset($title['tagline']);
				return $title;
			}, 999);
			$station_data = $this->fetch_station_by_name_smart($radio_name_decoded);
			
			if ($station_data) {
				// Sempre pega o primeiro resultado
				$stream = $station_data->url_resolved ?: $station_data->url;
				// Força https no início da URL do stream se vier como http
				if ($stream && strpos($stream, 'http://') === 0) {
					$stream = 'https://' . substr($stream, 7);
				}
				$station_name = $station_data->name;
				$station_img = !empty($station_data->favicon) ? $station_data->favicon : $default_img_url;
				$station_homepage = $station_data->homepage ?: '';
				
				// Dados da estação para exibição
				$station_clickcount = isset($station_data->clickcount) ? intval($station_data->clickcount) : 0;
				$station_votes = isset($station_data->votes) ? intval($station_data->votes) : 0;
				$station_tags = !empty($station_data->tags) ? $station_data->tags : '';
				$station_country = !empty($station_data->country) ? $station_data->country : '';
				$station_cc = !empty($station_data->countrycode) ? strtoupper($station_data->countrycode) : '';
				$station_codec = !empty($station_data->codec) ? strtoupper($station_data->codec) : '';
				$station_bitrate = (isset($station_data->bitrate) && intval($station_data->bitrate) > 0) ? intval($station_data->bitrate) . ' kbps' : '';
				$station_uuid = isset($station_data->stationuuid) ? $station_data->stationuuid : '';
			} else {
				// Rádio não encontrada na API - mostrar debug info
				return '<div class="lkp-radio-error">
							<h3>Rádio não encontrada</h3>
							<p>A rádio "' . esc_html($radio_name_decoded) . '" não foi encontrada em nossa base de dados.</p>
							<p><small>Debug: slug original "' . esc_html($radio_name) . '" convertido para "' . esc_html($radio_name_decoded) . '"</small></p>
						</div>';
			}
		} else {
			// Fallback: Método antigo com parâmetros (manter compatibilidade)
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public shortcode for radio streams, nonce not applicable
			$stream = isset($_GET['lrt_radio']) ? esc_url_raw(wp_unslash($_GET['lrt_radio'])) : '';
			// Força https no início da URL do stream se vier como http
			if ($stream && strpos($stream, 'http://') === 0) {
				$stream = 'https://' . substr($stream, 7);
			}
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public shortcode for radio streams, nonce not applicable
			$station_name = isset($_GET['lrt_name']) ? sanitize_text_field(wp_unslash($_GET['lrt_name'])) : '';
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public shortcode for radio streams, nonce not applicable
			$station_img = isset($_GET['lrt_img']) ? esc_url_raw(wp_unslash($_GET['lrt_img'])) : '';
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public shortcode for radio streams, nonce not applicable
			$station_homepage = isset($_GET['lrt_homepage']) ? esc_url_raw(wp_unslash($_GET['lrt_homepage'])) : '';
			
			// No método antigo não temos dados da estação, então zera as estatísticas
			$station_clickcount = 0;
			$station_votes = 0;
			$station_tags = '';
			$station_country = '';
			$station_cc = '';
			$station_codec = '';
			$station_bitrate = '';
			$station_uuid = '';
			
			if (empty($stream)) {
				return '<div class="lkp-radio-error">
							<h3>Nenhuma rádio selecionada</h3>
							<p>Por favor, selecione uma rádio para reproduzir.</p>
						</div>';
			}
		}
		
		// Load template
		ob_start();
		include plugin_dir_path(__FILE__) . $template;
		return ob_get_clean();
	}
	

	
	/**
	 * Busca estação por nome na API com múltiplas estratégias inteligentes
	 */
	private function fetch_station_by_name_smart($name) {
		$servers = [
			'https://de2.api.radio-browser.info',
			'https://fi1.api.radio-browser.info',
			'https://fr1.api.radio-browser.info',
			'https://nl1.api.radio-browser.info'
		];
		
		// Estratégias de busca em ordem de prioridade
		$search_strategies = array(
			// 1. Busca exata pelo nome reconstruído
			$name,
			// 2. Busca com "Rádio" no início (padrão brasileiro)
			'Rádio ' . $name,
			// 3. Busca com primeira letra maiúscula em cada palavra
			ucwords(strtolower($name)),
			// 4. Busca com "Rádio" + primeira letra maiúscula
			'Rádio ' . ucwords(strtolower($name)),
			// 5. Busca apenas pelas palavras principais (remove números e FM)
			trim(preg_replace('/\d+\.?\d*\s*(fm|am|khz|mhz)?/i', '', $name)),
			// 6. Busca pela primeira palavra significativa + frequência se houver
			$this->extract_main_word_with_frequency($name),
			// 7. Busca apenas pela primeira palavra significativa
			explode(' ', trim($name))[0]
		);
		
		foreach ($search_strategies as $search_term) {
			if (empty(trim($search_term)) || strlen(trim($search_term)) < 2) continue;
			
			foreach ($servers as $server) {
				$api_url = $server . '/json/stations/search?name=' . urlencode($search_term) . '&limit=20';
				
				$response = wp_remote_get($api_url, array(
					'timeout' => 10,
					'headers' => array(
						'User-Agent' => 'LKNWP Radio Browser Plugin/1.0.0'
					)
				));
				
				if (is_wp_error($response)) {
					continue;
				}
				
				$body = wp_remote_retrieve_body($response);
				$stations = json_decode($body);
				
				if (!empty($stations) && is_array($stations)) {
					// Procura pela melhor correspondência
					$best_match = $this->find_best_station_match($stations, $name);
					if ($best_match) {
						return $best_match;
					}
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Encontra a melhor correspondência entre as estações encontradas
	 */
	private function find_best_station_match($stations, $target_name) {
		$target_clean = $this->clean_station_name($target_name);
		$best_score = 0;
		$best_match = null;
		
		foreach ($stations as $station) {
			$station_clean = $this->clean_station_name($station->name);
			
			// Calcula score de similaridade
			$score = 0;
			
			// Score por correspondência exata (ignoring case)
			if (strcasecmp($station_clean, $target_clean) === 0) {
				$score += 100;
			}
			
			// Score por palavras em comum
			$target_words = array_filter(explode(' ', strtolower($target_clean)));
			$station_words = array_filter(explode(' ', strtolower($station_clean)));
			$common_words = array_intersect($target_words, $station_words);
			$score += count($common_words) * 15;
			
			// Score por substring match
			if (stripos($station_clean, $target_clean) !== false || stripos($target_clean, $station_clean) !== false) {
				$score += 25;
			}
			
			// Score por número de caracteres em comum
			$similarity = (similar_text(strtolower($target_clean), strtolower($station_clean)) / max(strlen($target_clean), strlen($station_clean))) * 30;
			$score += $similarity;
			
			if ($score > $best_score && $score > 25) { // Threshold mínimo
				$best_score = $score;
				$best_match = $station;
			}
		}
		
		return $best_match;
	}
	
	/**
	 * Extrai palavra principal + frequência do nome
	 */
	private function extract_main_word_with_frequency($name) {
		// Pega a primeira palavra significativa
		$words = explode(' ', trim($name));
		$main_word = '';
		$frequency = '';
		
		foreach ($words as $word) {
			if (strlen($word) > 2 && !in_array(strtolower($word), ['fm', 'am', 'khz', 'mhz'])) {
				if (empty($main_word)) {
					$main_word = $word;
				}
				// Se parece com frequência (número com ponto)
				if (preg_match('/\d+\.\d+/', $word)) {
					$frequency = $word;
				}
			}
		}
		
		return trim($main_word . ' ' . $frequency);
	}
	
	/**
	 * Limpa nome da estação para comparação
	 */
	private function clean_station_name($name) {
		$name = trim($name);
		$name = preg_replace('/[^\w\s\.]/', ' ', $name); // Remove caracteres especiais exceto pontos
		$name = preg_replace('/\s+/', ' ', $name); // Normaliza espaços
		return trim($name);
	}

	/**
	 * Shortcode to list radios with a link to the player page
	 * Usage: [radio_browser_list player_page="player" layout="modern" hide_country="yes" ...]
	 * 
	 * Parameters:
	 * - layout: Layout to render ("modern" for the new layout; anything else/absent = legacy)
	 * - player_page: Page slug for the radio player
	 * - countrycode: Country code filter (default: the country of the WordPress
	 *   locale, e.g. pt_BR → BR; "all" when the locale has no supported country).
	 *   The visitor can change it and the choice is remembered in the browser.
	 * - limit: Number of stations to show (default: 20)
	 * - sort: Sort order (clickcount, name, random, bitrate) - default: clickcount
	 * - reverse: Sort direction (1 or 0)
	 * - search: Search term
	 * - hide_country: Hide country field (yes/no)
	 * - hide_limit: Hide limit field (yes/no)
	 * - hide_sort: Hide sort field (yes/no)
	 * - hide_order: Hide order button (yes/no)
	 * - hide_genre: Hide genre field/component in the filter and list (yes/no)
	 * - hide_search: Hide search field (yes/no)
	 * - hide_button: Hide submit button (yes/no)
	 * - hide_all_filters: Hide entire filter form (yes/no)
	 */
	public function radio_browser_list_shortcode($atts) {
		$resolved = self::resolve_layout($atts);
		$template = $resolved['layout'] === 'modern'
			? 'assets/templates/radio-list.php'
			: 'assets/templates/radio-list-legacy.php';
		$output = $this->render_radio_browser_list($atts, $template);
		if ($resolved['invalid']) {
			$output = self::layout_error_notice($atts) . $output;
		}
		return $output;
	}

	/**
	 * Renderiza a lista de rádios usando o template informado.
	 *
	 * @param array  $atts     Atributos do shortcode.
	 * @param string $template Caminho relativo do template (dentro do plugin).
	 * @return string HTML da lista.
	 */
	private function render_radio_browser_list($atts, $template) {
		// Verifica o nonce do formulário
		if (isset($_GET['lknwp_radio_list_nonce'])) {
			$nonce = sanitize_text_field(wp_unslash($_GET['lknwp_radio_list_nonce']));
			if (!wp_verify_nonce($nonce, 'lknwp_radio_list_action')) {
				return '<div class="lrt-radio-error">' . esc_html__('Security check failed. Please reload the page.', 'lknwp-radio-browser') . '</div>';
			}
		}

		// País inicial: prioridade → valor explícito na URL (submit do form) >
		// escolha salva do visitante (cookie) > atributo do shortcode > país do
		// locale do WordPress. Como o cookie é lido no servidor, o HTML já sai com
		// o país certo e o filtro não volta para o padrão.
		$attr_countrycode = (isset($atts['countrycode']) && '' !== (string) $atts['countrycode'])
			? (string) $atts['countrycode']
			: '';
		if (isset($_GET['lrt_countrycode'])) {
			$countrycode = sanitize_text_field(wp_unslash($_GET['lrt_countrycode']));
		} else {
			$countrycode = ('' !== $attr_countrycode) ? $attr_countrycode : self::get_default_country();
			$hide_country = (isset($atts['hide_country']) && 'yes' === $atts['hide_country']);
			$saved_countrycode = self::get_visitor_country_from_cookie();
			if ('' !== $saved_countrycode && !$hide_country) {
				$countrycode = $saved_countrycode;
			}
		}
		$limit = isset($_GET['lrt_limit']) ? intval(wp_unslash($_GET['lrt_limit'])) : (isset($atts['limit']) ? intval($atts['limit']) : 20);
		$player_page = isset($atts['player_page']) ? sanitize_title($atts['player_page']) : 'player';
		$search = isset($_GET['lrt_radio_search']) ? sanitize_text_field(wp_unslash($_GET['lrt_radio_search'])) : '';
		$sort_options = [
			'clickcount' => __('Most popular', 'lknwp-radio-browser'),
			'name' => __('Name', 'lknwp-radio-browser'),
			'random' => __('Random', 'lknwp-radio-browser'),
			'bitrate' => __('Bitrate', 'lknwp-radio-browser')
		];
		$sort = isset($_GET['lrt_sort']) && isset($sort_options[sanitize_text_field(wp_unslash($_GET['lrt_sort']))]) ? sanitize_text_field(wp_unslash($_GET['lrt_sort'])) : 'clickcount';
		$genre = isset($_GET['lrt_genre']) ? sanitize_text_field(wp_unslash($_GET['lrt_genre'])) : 'all';
		$reverse = isset($_GET['lrt_reverse']) ? sanitize_text_field(wp_unslash($_GET['lrt_reverse'])) : '1'; // 1 = reverso ativo por padrão

		$atts = shortcode_atts([
			'countrycode' => $countrycode,
			'limit' => $limit,
			'player_page' => $player_page,
			'sort' => $sort, // Padrão clickcount, mas permite outras opções
			'reverse' => $reverse,
			'search' => $search,
			'genre' => $genre,
			'hide_country' => 'no',
			'hide_limit' => 'no',
			'hide_sort' => 'no',
			'hide_order' => 'no',
			'hide_genre' => 'no',
			'hide_search' => 'no',
			'hide_button' => 'no',
			'hide_all_filters' => 'no'
		], $atts);

		$servers = [
			'https://de2.api.radio-browser.info',
			'https://fi1.api.radio-browser.info',
			'https://fr1.api.radio-browser.info',
			'https://nl1.api.radio-browser.info'
		];
		shuffle($servers);
		$stations = null;
		foreach ($servers as $base_url) {
			// Monta a URL de busca unificada, removendo parâmetros vazios
			$api_url = $base_url . '/json/stations/search?';
			$params = array();
			if (!empty($atts['search'])) {
				$params[] = 'name=' . urlencode($atts['search']);
			}
			if (!empty($atts['countrycode']) && $atts['countrycode'] !== 'all') {
				$params[] = 'countrycode=' . urlencode($atts['countrycode']);
			}
			if (!empty($atts['sort'])) {
				$params[] = 'order=' . urlencode($atts['sort']);
			}
			if (!empty($atts['limit'])) {
				$params[] = 'limit=' . ($atts['limit'] * 2);
			}
			$params[] = 'hidebroken=true';
			if ($atts['reverse'] === '1') {
				$params[] = 'reverse=true';
			}
			if (!empty($atts['genre']) && $atts['genre'] !== 'all') {
				$params[] = 'tagList=' . urlencode($atts['genre']);
			}
			$api_url .= implode('&', $params);

			$args = [
				'headers' => [
					'User-Agent' => 'lknwp-radio-browser/1.0'
				]
			];
			$response = wp_remote_get($api_url, $args);
			if (!is_wp_error($response)) {
				$body = wp_remote_retrieve_body($response);
				$stations = json_decode($body);
				if ($stations && is_array($stations)) {
					break;
				}
			}
		}

		// Prepare variables for template
		$plugin_url = defined('LKNWP_RADIO_BROWSER_PLUGIN_URL') ? LKNWP_RADIO_BROWSER_PLUGIN_URL : '';
		$default_img_url = defined('LKNWP_RADIO_BROWSER_PLUGIN_URL') ? LKNWP_RADIO_BROWSER_PLUGIN_URL . 'Includes/assets/images/default-radio.png' : './Includes/assets/images/default-radio.png';
		
		// Corrige a URL base do player para suportar páginas ascendentes/nested
		$player_base_url = false;
		if (!empty($atts['player_page'])) {
			$player_base_url = self::lknwp_find_page_by_slug($atts['player_page']);
		}

		if (!$player_base_url) {
			$player_base_url = home_url('/' . $atts['player_page'] . '/');
		}

		// Load template, passando $player_base_url, $countries e $selected_country
		$countries = self::get_supported_countries();
		$selected_country = (isset($atts['countrycode']) && '' !== (string) $atts['countrycode'])
			? $atts['countrycode']
			: self::get_default_country();
		if (empty($selected_country)) {
			$selected_country = 'all';
		}
		ob_start();
		include plugin_dir_path(__FILE__) . $template;
		return ob_get_clean();
	}

	/**
	 * Lista canônica de países do seletor de filtro (código => rótulo).
	 * Compartilhada pelos templates moderno e legado para evitar divergência.
	 *
	 * @return array<string,string>
	 */
	public static function get_supported_countries() {
		return array_merge(
			array('all' => '🌍 ' . __('All Countries', 'lknwp-radio-browser')),
			array(
				'BR' => '🇧🇷 BR', 'US' => '🇺🇸 US', 'AR' => '🇦🇷 AR', 'CA' => '🇨🇦 CA',
				'GB' => '🇬🇧 GB', 'FR' => '🇫🇷 FR', 'DE' => '🇩🇪 DE', 'ES' => '🇪🇸 ES',
				'IT' => '🇮🇹 IT', 'PT' => '🇵🇹 PT', 'MX' => '🇲🇽 MX', 'CL' => '🇨🇱 CL',
				'CO' => '🇨🇴 CO', 'PE' => '🇵🇪 PE', 'UY' => '🇺🇾 UY', 'PY' => '🇵🇾 PY',
				'BO' => '🇧🇴 BO', 'EC' => '🇪🇨 EC', 'VE' => '🇻🇪 VE', 'AU' => '🇦🇺 AU',
				'JP' => '🇯🇵 JP', 'KR' => '🇰🇷 KR', 'CN' => '🇨🇳 CN', 'IN' => '🇮🇳 IN',
				'RU' => '🇷🇺 RU', 'NL' => '🇳🇱 NL', 'BE' => '🇧🇪 BE', 'CH' => '🇨🇭 CH',
				'AT' => '🇦🇹 AT', 'SE' => '🇸🇪 SE', 'NO' => '🇳🇴 NO', 'DK' => '🇩🇰 DK',
				'FI' => '🇫🇮 FI'
			)
		);
	}

	/**
	 * País padrão do filtro, derivado do locale do WordPress (ex.: pt_BR → BR,
	 * en_US → US). Se o locale não trouxer um país suportado, retorna 'all'.
	 * O visitante pode sobrescrever e a escolha é lembrada no navegador.
	 *
	 * @return string Código de país (ex.: 'BR') ou 'all'.
	 */
	public static function get_default_country() {
		$locale = function_exists('determine_locale') ? determine_locale() : get_locale();
		$cc = '';
		if (is_string($locale) && preg_match('/[_-]([A-Za-z]{2})(?:[_-]|$)/', $locale, $m)) {
			$cc = strtoupper($m[1]);
		}

		$countries = self::get_supported_countries();
		if ('' !== $cc && isset($countries[$cc])) {
			return $cc;
		}

		return 'all';
	}

	/**
	 * País escolhido pelo visitante, lido do cookie `lknwp_country` (gravado pelo
	 * JS no navegador). Validado contra a lista de países suportados.
	 *
	 * @return string Código de país (ex.: 'BR', 'all') ou '' se ausente/inválido.
	 */
	public static function get_visitor_country_from_cookie() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Preferência de UI; não altera estado no servidor.
		if (empty($_COOKIE['lknwp_country']) || !is_string($_COOKIE['lknwp_country'])) {
			return '';
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Preferência de UI; não altera estado no servidor.
		$cc = sanitize_text_field(wp_unslash($_COOKIE['lknwp_country']));
		$countries = self::get_supported_countries();

		return isset($countries[$cc]) ? $cc : '';
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Lknwp_Radio_Browser_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Registra as rewrite rules para URLs amigáveis do player
	 */
	public function register_player_rewrite_rules() {
		$cached_rules = get_option('lknwp_player_rewrite_rules');
		
		if ($cached_rules === false) {
			$cached_rules = $this->get_player_pages_rules();
			update_option('lknwp_player_rewrite_rules', $cached_rules);
		}
		
		foreach ($cached_rules as $rule) {
			add_rewrite_rule($rule['regex'], $rule['redirect'], 'top');
		}
		flush_rewrite_rules(); // Garante que as regras sejam aplicadas imediatamente
	}

	/**
	 * Busca páginas com shortcode do player e gera regras de rewrite
	 */
	private function get_player_pages_rules() {
		global $wpdb;
		
		$player_pages = $wpdb->get_results($wpdb->prepare("
			SELECT post_name, post_parent
			FROM {$wpdb->posts} 
			WHERE post_type = 'page' 
			AND post_status = 'publish' 
			AND post_content LIKE %s
		", '%[radio_browser_player%'));

		$rules = array();
		foreach ($player_pages as $page) {
			if (!empty($page->post_parent)) {
				$parent_obj = get_post($page->post_parent);
				$full_uri = ($parent_obj && $parent_obj->post_name)
					? $parent_obj->post_name . '/' . $page->post_name
					: $page->post_name;
			} else {
				$full_uri = $page->post_name;
			}
			$rules[] = array(
				'regex' => "^{$full_uri}/([^/]+)/?$",
				'redirect' => "index.php?pagename={$full_uri}&radio_name=\$matches[1]"
			);
		}

		return $rules;
	}

	/**
	 * Atualiza rewrite rules quando página com shortcode é salva
	 */
	public function handle_player_page_changes($post_id, $post) {
		if ($post->post_type !== 'page') return;
		
		if (has_shortcode($post->post_content, 'radio_browser_player')) {
			delete_option('lknwp_player_rewrite_rules');
			flush_rewrite_rules();
		}
	}

	/**
	 * Adiciona query vars personalizadas
	 */
	public function add_player_query_vars($vars) {
		$vars[] = 'radio_name';
		return $vars;
	}

	/**
	 * Busca estação por nome na API do Radio-Browser
	 */
	private function fetch_station_by_name($name) {
		$api_url = 'https://de2.api.radio-browser.info/json/stations/search?name=' . urlencode($name);
		
		$response = wp_remote_get($api_url, array(
			'timeout' => 15,
			'headers' => array(
				'User-Agent' => 'LKNWP Radio Browser Plugin/1.0.0'
			)
		));
		
		if (is_wp_error($response)) {
			return false;
		}
		
		$body = wp_remote_retrieve_body($response);
		$stations = json_decode($body);
		
		// Sempre retorna o primeiro resultado (mesmo com múltiplas opções)
		return !empty($stations) && is_array($stations) ? $stations[0] : false;
	}

	/**
	 * Endpoint AJAX (proxy server-side) que busca os metadados do stream
	 * (música atual, artista e público/ouvintes) sem sofrer CORS no navegador.
	 */
	public function ajax_radio_metadata() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Verificado logo abaixo.
		$nonce = isset($_GET['nonce']) ? sanitize_text_field(wp_unslash($_GET['nonce'])) : '';
		if (!wp_verify_nonce($nonce, 'lknwp_radio_metadata')) {
			wp_send_json_error(array('message' => 'invalid_nonce'), 403);
		}

		// Rate limit simples por IP (evita uso abusivo do proxy).
		$ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
		$rl_key = 'lknwp_radio_meta_rl_' . md5($ip);
		$rl_count = (int) get_transient($rl_key);
		if ($rl_count >= 120) {
			wp_send_json_success(array('found' => false));
		}
		set_transient($rl_key, $rl_count + 1, MINUTE_IN_SECONDS);

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce validado acima.
		$stream = isset($_GET['stream']) ? esc_url_raw(wp_unslash($_GET['stream'])) : '';
		if (empty($stream)) {
			wp_send_json_success(array('found' => false));
		}

		$cache_key = 'lknwp_radio_meta_' . md5($stream);
		$cached = get_transient($cache_key);
		if ($cached !== false) {
			wp_send_json_success($cached);
		}

		$data = $this->fetch_stream_metadata($stream);
		// Cache curto: encontrado por 10s, não encontrado por 15s.
		set_transient($cache_key, $data, !empty($data['found']) ? 10 : 15);
		wp_send_json_success($data);
	}

	/**
	 * Endpoint AJAX (proxy same-origin) que reencaminha o áudio do stream.
	 *
	 * O visualizador usa a Web Audio API (AnalyserNode) para desenhar as "waves",
	 * o que exige que o navegador consiga LER o áudio. Se a rádio não responde com
	 * cabeçalhos CORS (algumas dividem o DNS entre servidores que hora mandam, hora
	 * não mandam `Access-Control-Allow-Origin`), o áudio fica "opaco" e as barras
	 * não se mexem. Este endpoint entrega os bytes no mesmo domínio do site, então
	 * a análise passa a funcionar independentemente do CORS da rádio.
	 */
	public function ajax_radio_stream() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Verificado logo abaixo.
		$nonce = isset($_GET['nonce']) ? sanitize_text_field(wp_unslash($_GET['nonce'])) : '';
		if (!wp_verify_nonce($nonce, 'lknwp_radio_stream')) {
			status_header(403);
			exit;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce validado acima.
		$stream = isset($_GET['stream']) ? esc_url_raw(wp_unslash($_GET['stream'])) : '';
		$parts = ($stream !== '') ? wp_parse_url($stream) : array();
		if (
			!is_array($parts) ||
			empty($parts['scheme']) ||
			empty($parts['host']) ||
			!in_array(strtolower($parts['scheme']), array('http', 'https'), true)
		) {
			status_header(400);
			exit;
		}

		// Resolve/valida o host em IPs públicos (proteção SSRF). Só seguimos se TODOS
		// os endereços forem públicos; fixamos cada IP no cURL (anti DNS-rebinding).
		$host = $parts['host'];
		$ips = $this->resolve_public_ips($host);
		if (empty($ips)) {
			status_header(400);
			exit;
		}
		$port = isset($parts['port']) ? intval($parts['port']) : ('https' === strtolower($parts['scheme']) ? 443 : 80);

		// Rate limit simples por IP do visitante (evita uso abusivo do proxy).
		$client_ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';

		$rl_key = 'lknwp_radio_stream_rl_' . md5($client_ip);
		$rl_count = (int) get_transient($rl_key);
		if ($rl_count >= 60) {
			status_header(429);
			exit;
		}
		set_transient($rl_key, $rl_count + 1, MINUTE_IN_SECONDS);

		if (!function_exists('curl_init')) {
			status_header(501);
			exit;
		}

		// Stream contínuo: desliga buffers/compressão e remove o limite de execução.
		while (ob_get_level() > 0) {
			ob_end_clean();
		}
		@ini_set('zlib.output_compression', '0');
		@ini_set('output_buffering', '0');
		@set_time_limit(0);
		ignore_user_abort(false);

		// Tenta cada IP público. Provedores de rádio costumam ser multi-homed e parte
		// dos IPs pode estar fora do ar; como fixamos o IP no cURL (RESOLVE), sem esse
		// failover uma resposta ruim de um único IP derrubaria o proxy.
		foreach ($ips as $ip) {
			$state = array(
				'ok'       => null,
				'status'   => 0,
				'ctype'    => 'audio/mpeg',
				'sent'     => false,
				'bad_type' => false,
			);

			$this->proxy_stream_from_ip($stream, $host, $port, $ip, $state);

			// Conseguiu repassar, ou o tipo não é áudio (não vale tentar outro IP).
			if ($state['sent'] || $state['bad_type']) {
				break;
			}
		}

		// Se nunca conseguimos iniciar o repasse, devolve um status de erro coerente.
		if (!$state['sent']) {
			if ($state['bad_type']) {
				status_header(415);
			} else {
				$code = (int) $state['status'];
				status_header(($code >= 400 && $code < 600) ? $code : 502);
			}
		}
		exit;
	}

	/**
	 * Executa uma tentativa de repasse do stream fixando a conexão em um IP já
	 * validado. O estado é compartilhado com os callbacks do cURL por referência.
	 *
	 * @param string $stream URL completa do stream.
	 * @param string $host   Host do stream.
	 * @param int    $port   Porta do stream.
	 * @param string $ip     IP público validado.
	 * @param array  $state  Estado da conexão (por referência).
	 */
	private function proxy_stream_from_ip($stream, $host, $port, $ip, &$state) {
		$curl = curl_init();
		curl_setopt_array(
			$curl,
			array(
				CURLOPT_URL             => $stream,
				CURLOPT_USERAGENT       => 'LKNWP Radio Browser/' . $this->version,
				CURLOPT_FOLLOWLOCATION  => false,
				CURLOPT_CONNECTTIMEOUT  => 10,
				CURLOPT_TIMEOUT         => LKNWP_RADIO_STREAM_MAX_SECONDS,
				CURLOPT_LOW_SPEED_LIMIT => 64,
				CURLOPT_LOW_SPEED_TIME  => 30,
				CURLOPT_BUFFERSIZE      => 8192,
				CURLOPT_SSL_VERIFYPEER  => true,
				CURLOPT_SSL_VERIFYHOST  => 2,
				CURLOPT_HTTPHEADER      => array('Icy-MetaData: 0', 'Accept: audio/*,*/*'),
				CURLOPT_RESOLVE         => array($host . ':' . $port . ':' . $ip),
				CURLOPT_HEADERFUNCTION  => function ($ch, $header) use (&$state) {
					if (preg_match('#^HTTP/\S+\s+(\d{3})#', $header, $m)) {
						$state['status'] = (int) $m[1];
					} elseif (0 === stripos($header, 'content-type:')) {
						$ctype = trim(substr($header, 13));
						if ($ctype !== '') {
							$state['ctype'] = $ctype;
						}
					}
					return strlen($header);
				},
				CURLOPT_WRITEFUNCTION   => function ($ch, $data) use (&$state) {
					if (null === $state['ok']) {
						$is_2xx            = ($state['status'] >= 200 && $state['status'] < 300);
						$type_ok           = $this->is_allowed_stream_type($state['ctype']);
						$state['ok']       = ($is_2xx && $type_ok);
						$state['bad_type'] = ($is_2xx && !$type_ok);
					}
					if (!$state['ok']) {
						return strlen($data); // Descarta corpo de erro (nada foi enviado ainda).
					}
					if (!$state['sent']) {
						header('Content-Type: ' . $state['ctype']);
						header('X-Content-Type-Options: nosniff');
						header('Cache-Control: no-cache, no-store, must-revalidate');
						header('Pragma: no-cache');
						header('X-Accel-Buffering: no');
						$state['sent'] = true;
					}
					echo $data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Bytes do stream, repassados sem alteração.
					flush();
					return strlen($data);
				},
			)
		);

		curl_exec($curl);
		curl_close($curl);
	}

	/**
	 * Só repassamos tipos de mídia de áudio. Isso impede que o proxy (que roda no
	 * mesmo domínio do site) sirva HTML/JS/SVG e vire vetor de XSS em cima da
	 * `Content-Type` controlável pela origem remota.
	 *
	 * @param string $ctype Content-Type devolvido pelo upstream.
	 * @return bool
	 */
	private function is_allowed_stream_type($ctype) {
		$ctype = strtolower(trim((string) $ctype));
		$semi = strpos($ctype, ';');
		if (false !== $semi) {
			$ctype = trim(substr($ctype, 0, $semi));
		}
		if (0 === strpos($ctype, 'audio/')) {
			return true;
		}
		return in_array($ctype, array('application/ogg', 'application/octet-stream'), true);
	}

	/**
	 * Resolve um host para TODOS os IPs públicos (bloqueia faixas privadas/reservadas).
	 * Retorna a lista de IPs ou um array vazio (proteção SSRF). Os IPs devolvidos são
	 * fixados no cURL via CURLOPT_RESOLVE para evitar rebinding de DNS.
	 *
	 * @param string $host Hostname ou IP.
	 * @return string[] IPs públicos válidos (pode ser vazio).
	 */
	private function resolve_public_ips($host) {
		$host = strtolower(trim((string) $host, " \t\n\r\0\x0B."));
		if ('' === $host || 'localhost' === $host || substr($host, -6) === '.local' || substr($host, -9) === '.internal') {
			return array();
		}

		$flags = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
		if (filter_var($host, FILTER_VALIDATE_IP)) {
			return filter_var($host, FILTER_VALIDATE_IP, $flags) ? array($host) : array();
		}

		$ips = array();
		$records = @dns_get_record($host, DNS_A);
		if (is_array($records)) {
			foreach ($records as $record) {
				if (!empty($record['ip'])) {
					$ips[] = $record['ip'];
				}
			}
		}
		if (empty($ips)) {
			$single = gethostbyname($host);
			if ($single && $single !== $host) {
				$ips[] = $single;
			}
		}
		if (empty($ips)) {
			return array();
		}

		// Todos os endereços precisam ser públicos; qualquer um privado/reservado
		// invalida a requisição (evita SSRF via host multi-homed ou rebinding).
		$public = array();
		foreach (array_unique($ips) as $ip) {
			if (!filter_var($ip, FILTER_VALIDATE_IP, $flags)) {
				return array();
			}
			$public[] = $ip;
		}

		return $public;
	}

	/**
	 * Consulta a API de status (Icecast/Shoutcast) do host do stream e extrai
	 * música/artista/ouvintes. Roda no servidor (sem CORS).
	 */
	private function fetch_stream_metadata($stream) {
		$result = array('found' => false);

		$parts = wp_parse_url($stream);
		if (empty($parts['scheme']) || empty($parts['host']) || !in_array(strtolower($parts['scheme']), array('http', 'https'), true)) {
			return $result;
		}
		if (!$this->is_public_host($parts['host'])) {
			return $result;
		}

		$origin = $parts['scheme'] . '://' . $parts['host'] . (isset($parts['port']) ? ':' . intval($parts['port']) : '');
		$dir = '';
		if (!empty($parts['path'])) {
			$dir = (strpos($parts['path'], '/') !== false) ? rtrim(dirname($parts['path']), '/\\') : '';
			if ($dir === '.' || $dir === '/') {
				$dir = '';
			}
		}
		$base = $origin . $dir;

		$candidates = array(
			array('url' => $base . '/status-json.xsl', 'type' => 'json'),
			array('url' => $base . '/status.xsl', 'type' => 'html'),
			array('url' => $base . '/index.html', 'type' => 'html'),
			array('url' => $base . '/index.html?sid=1', 'type' => 'html'),
		);

		foreach ($candidates as $candidate) {
			$response = $this->remote_get_safe($candidate['url'], $candidate['type'] === 'json' ? 'application/json' : 'text/html,application/xhtml+xml');
			if (is_wp_error($response)) {
				continue;
			}
			$code = wp_remote_retrieve_response_code($response);
			if ($code < 200 || $code >= 400) {
				continue;
			}
			$body = wp_remote_retrieve_body($response);
			if (empty($body)) {
				continue;
			}

			$parsed = ($candidate['type'] === 'json') ? $this->parse_stream_json($body) : $this->parse_stream_html($body);
			if (!empty($parsed['found'])) {
				$result = $parsed;
				break;
			}
		}

		// Capa do álbum via iTunes (host fixo e conhecido, seguro)
		if (!empty($result['found']) && !empty($result['title'])) {
			$term = trim((!empty($result['artist']) ? $result['artist'] . ' ' : '') . $result['title']);
			$art = $this->fetch_itunes_artwork($term);
			if (!empty($art)) {
				$result['album_art'] = $art;
			}
		}

		return $result;
	}

	/**
	 * Extrai música/artista/ouvintes do JSON de status do Icecast (status-json.xsl).
	 */
	private function parse_stream_json($body) {
		$data = json_decode($body, true);
		if (empty($data['icestats']['source'])) {
			return array('found' => false);
		}

		$source = $data['icestats']['source'];
		$item = null;
		if (isset($source[0]) && is_array($source)) {
			foreach ($source as $s) {
				if (!empty($s['title']) || !empty($s['yp_currently_playing'])) {
					$item = $s;
					break;
				}
			}
			if ($item === null) {
				$item = $source[0];
			}
		} else {
			$item = $source;
		}

		$raw = '';
		if (!empty($item['title'])) {
			$raw = $item['title'];
		} elseif (!empty($item['yp_currently_playing'])) {
			$raw = $item['yp_currently_playing'];
		}
		if (empty(trim((string) $raw))) {
			return array('found' => false);
		}

		$artist = '';
		$title = trim($raw);
		if (strpos($title, ' - ') !== false) {
			list($artist, $title) = explode(' - ', $title, 2);
			$artist = trim($artist);
			$title = trim($title);
		}
		$title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
		$artist = html_entity_decode($artist, ENT_QUOTES, 'UTF-8');

		return array(
			'found' => true,
			'title' => $title,
			'artist' => $artist,
			'listeners' => isset($item['listeners']) ? intval($item['listeners']) : 0,
		);
	}

	/**
	 * Extrai música/artista/ouvintes do HTML de status (Icecast/Shoutcast).
	 */
	private function parse_stream_html($body) {
		$song = '';
		$patterns = array(
			'/<td>\s*Current Song:?\s*<\/td>\s*<td[^>]*class="streamdata"[^>]*>\s*([^<]*?)\s*<\/td>/i',
			'/Playing Now:\s*<\/td>\s*<td>\s*<b>\s*<a[^>]*>([^<]+)<\/a>/i',
			'/<td>\s*M[úu]sica Atual:?\s*<\/td>\s*<td[^>]*>\s*([^<]+?)\s*<\/td>/i',
			'/<td>\s*T[ií]tulo:?\s*<\/td>\s*<td[^>]*>\s*([^<]+?)\s*<\/td>/i',
		);
		foreach ($patterns as $pattern) {
			if (preg_match($pattern, $body, $m) && trim($m[1]) !== '') {
				$song = trim($m[1]);
				break;
			}
		}

		if ($song === '') {
			return array('found' => false);
		}

		$listeners = 0;
		if (preg_match('/<td>\s*Current Listeners:?\s*<\/td>\s*<td[^>]*>\s*([^<]+?)\s*<\/td>/i', $body, $lm)) {
			$listeners = intval(preg_replace('/[^0-9]/', '', $lm[1]));
		}

		$artist = '';
		$title = $song;
		if (strpos($song, ' - ') !== false) {
			list($artist, $title) = explode(' - ', $song, 2);
			$artist = trim($artist);
			$title = trim($title);
		}
		$title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
		$artist = html_entity_decode($artist, ENT_QUOTES, 'UTF-8');

		return array(
			'found' => true,
			'title' => $title,
			'artist' => $artist,
			'listeners' => $listeners,
		);
	}

	/**
	 * GET seguro (server-side) com validação de host/porta e sem seguir redirects.
	 * Usa wp_safe_remote_get, que bloqueia IPs privados/reservados e revalida
	 * redirecionamentos. Portas comuns de streaming são permitidas.
	 */
	private function remote_get_safe($url, $accept = 'text/html') {
		$allow_ports = function ($ports) {
			return array_merge((array) $ports, array(8000, 8005, 8008, 8010, 8020, 8030, 8040, 8050, 8060, 8070, 8080, 8085, 8090, 8443, 9000, 9001, 2082, 2086, 2095, 2096, 2199));
		};
		add_filter('http_allowed_safe_ports', $allow_ports);

		$response = wp_safe_remote_get($url, array(
			'timeout' => 6,
			'redirection' => 0,
			'headers' => array(
				'User-Agent' => 'LKNWP Radio Browser/' . $this->version,
				'Accept' => $accept,
			),
		));

		remove_filter('http_allowed_safe_ports', $allow_ports);
		return $response;
	}

	/**
	 * Busca a capa do álbum no iTunes (server-side).
	 */
	private function fetch_itunes_artwork($term) {
		if (empty($term)) {
			return '';
		}
		$url = 'https://itunes.apple.com/search?term=' . rawurlencode($term) . '&entity=song&limit=1';
		$response = wp_safe_remote_get($url, array('timeout' => 6, 'redirection' => 0));
		if (is_wp_error($response)) {
			return '';
		}
		$data = json_decode(wp_remote_retrieve_body($response), true);
		if (!empty($data['results'][0]['artworkUrl100'])) {
			return preg_replace('/\d+x\d+bb\.jpg$/', '600x600bb.jpg', $data['results'][0]['artworkUrl100']);
		}
		return '';
	}

	/**
	 * Bloqueia hosts privados/reservados (proteção básica contra SSRF).
	 */
	private function is_public_host($host) {
		$host = strtolower(trim((string) $host, " \t\n\r\0\x0B."));
		if ($host === '' || $host === 'localhost' || substr($host, -6) === '.local' || substr($host, -9) === '.internal') {
			return false;
		}

		$ip = filter_var($host, FILTER_VALIDATE_IP) ? $host : gethostbyname($host);
		if (!filter_var($ip, FILTER_VALIDATE_IP)) {
			return false;
		}

		return (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
	}

	public static function lknwp_find_page_by_slug($slug) {
		global $wpdb;
		$result = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish' AND (post_name = %s OR post_name LIKE %s)",
				$slug,
				'%/' . $wpdb->esc_like($slug)
			)
		);
		return $result ? get_permalink($result) : false;
	}

}
