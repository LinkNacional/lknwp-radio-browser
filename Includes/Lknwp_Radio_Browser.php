<?php

namespace Lkn\LKNWP_Radio_Browser\Includes;

use Lkn\LKNWP_Radio_Browser\Admin\Lknwp_Radio_Browser_Admin;
use Lkn\LKNWP_Radio_Browser\Public\Lknwp_Radio_Browser_Public;

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
			$this->version = '1.0.0';
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
	 * - Lknwp_Radio_Browser_i18n. Defines internationalization functionality.
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
	 * Uses the Lknwp_Radio_Browser_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Lknwp_Radio_Browser_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

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

	}

	/**
	 * Register shortcodes for radio list and player
	 */
	public function register_radio_browser_shortcodes() {
		add_shortcode('radio_browser_list', array($this, 'radio_browser_list_shortcode'));
		add_shortcode('radio_browser_player', array($this, 'radio_browser_player_shortcode'));
	}

	/**
	 * Shortcode to display the radio player
	 */
	public function radio_browser_player_shortcode() {
		$radio_name = get_query_var('radio_name');
		$default_img_url = defined('LKNWP_RADIO_BROWSER_PLUGIN_URL') ? LKNWP_RADIO_BROWSER_PLUGIN_URL . 'Includes/assets/images/default-radio.png' : './Includes/assets/images/default-radio.png';
		
		if ($radio_name) {
			// URL amigável: decodifica o nome da rádio da URL
			$radio_name_decoded = str_replace('%20', ' ', urldecode($radio_name));
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
			
			if (empty($stream)) {
				return '<div class="lkp-radio-error">
							<h3>Nenhuma rádio selecionada</h3>
							<p>Por favor, selecione uma rádio para reproduzir.</p>
						</div>';
			}
		}
		
		// Load template
		ob_start();
		include plugin_dir_path(__FILE__) . 'assets/templates/radio-player.php';
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
	 * Usage: [radio_browser_list player_page="player" hide_country="yes" hide_limit="yes" hide_sort="yes" hide_order="yes" hide_search="yes" hide_button="yes" hide_all_filters="yes"]
	 * 
	 * Parameters:
	 * - player_page: Page slug for the radio player
	 * - countrycode: Country code filter (default: BR)
	 * - limit: Number of stations to show (default: 20)
	 * - sort: Sort order (clickcount, name, random, bitrate) - default: clickcount
	 * - reverse: Sort direction (1 or 0)
	 * - search: Search term
	 * - hide_country: Hide country field (yes/no)
	 * - hide_limit: Hide limit field (yes/no)
	 * - hide_sort: Hide sort field (yes/no)
	 * - hide_order: Hide order button (yes/no)
	 * - hide_search: Hide search field (yes/no)
	 * - hide_button: Hide submit button (yes/no)
	 * - hide_all_filters: Hide entire filter form (yes/no)
	 */
	public function radio_browser_list_shortcode($atts) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public shortcode for radio list, nonce not applicable
		$countrycode = isset($_GET['lrt_countrycode']) ? sanitize_text_field(wp_unslash($_GET['lrt_countrycode'])) : (isset($atts['countrycode']) ? $atts['countrycode'] : 'BR');
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public shortcode for radio list, nonce not applicable
		$limit = isset($_GET['lrt_limit']) ? intval(wp_unslash($_GET['lrt_limit'])) : (isset($atts['limit']) ? intval($atts['limit']) : 20);
		$player_page = isset($atts['player_page']) ? sanitize_title($atts['player_page']) : 'player';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public shortcode for radio list, nonce not applicable
		$search = isset($_GET['lrt_radio_search']) ? sanitize_text_field(wp_unslash($_GET['lrt_radio_search'])) : '';
		$sort_options = [
			'clickcount' => __('Most popular', 'lknwp-radio-browser'),
			'name' => __('Name', 'lknwp-radio-browser'),
			'random' => __('Random', 'lknwp-radio-browser'),
			'bitrate' => __('Bitrate', 'lknwp-radio-browser')
		];
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public shortcode for radio list, nonce not applicable
		$sort = isset($_GET['lrt_sort']) && isset($sort_options[sanitize_text_field(wp_unslash($_GET['lrt_sort']))]) ? sanitize_text_field(wp_unslash($_GET['lrt_sort'])) : 'clickcount';
		$genre = isset($_GET['lrt_genre']) ? sanitize_text_field(wp_unslash($_GET['lrt_genre'])) : 'all';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public shortcode for radio list, nonce not applicable  
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
			// Monta a URL de busca unificada
			$api_url = $base_url . '/json/stations/search?';
			$params = array();
			$params[] = 'name=' . urlencode($atts['search']);
			$params[] = 'countrycode=' . urlencode($atts['countrycode']);
			$params[] = 'order=' . urlencode($atts['sort']);
			$params[] = 'limit=' . ($atts['limit'] * 2);
			$params[] = 'hidebroken=true';
			$params[] = ($atts['reverse'] === '1') ? 'reverse=true' : 'reverse=false';
			// Sempre inclui tagList, mesmo vazio ou 'all'
			if (empty($atts['genre']) || $atts['genre'] === 'all') {
				$params[] = 'tagList=';
			} else {
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

		// Load template, passando $player_base_url
		ob_start();
		include plugin_dir_path(__FILE__) . 'assets/templates/radio-list.php';
		return ob_get_clean();
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

	public static function lknwp_find_page_by_slug($slug) {
        global $wpdb;
        $query = $wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish' AND (post_name = %s OR post_name LIKE %s)",
            $slug,
            '%/' . $wpdb->esc_like($slug)
        );
        $result = $wpdb->get_var($query);
        return $result ? get_permalink($result) : false;
    }

}
