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

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );
		$this->loader->add_action( 'rest_api_init', $this, 'register_proxy_endpoint' );

	}

	public function register_proxy_endpoint() {
        register_rest_route('lknwp-radio/v1', '/proxy-stream', array(
            'methods' => 'GET',
            'callback' => array($this, 'proxy_stream'),
            'permission_callback' => '__return_true',
            'args' => array(
                'url' => array(
                    'required' => true,
                    'validate_callback' => array($this, 'validate_stream_url')
                )
            )
        ));
    }

    /**
     * Valida URL do stream
     */
    public function validate_stream_url($param) {
        $is_valid = filter_var($param, FILTER_VALIDATE_URL) && 
                   (strpos($param, 'http') === 0);
        
        return $is_valid;
    }

    /**
     * Faz proxy do stream com headers CORS corretos
     */
    public function proxy_stream($request) {
        $stream_url = $request->get_param('url');
        
        // Headers CORS permissivos ANTES de qualquer output
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, HEAD, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Range, Accept');
        header('Access-Control-Expose-Headers: Content-Type, Content-Length, Accept-Ranges');
        
        // Lidar com preflight OPTIONS
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
        
        // Headers de cache para streams
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Primeiro testar conectividade básica
        $parsed_url = wp_parse_url($stream_url);
        $host = $parsed_url['host'];
        $port = isset($parsed_url['port']) ? $parsed_url['port'] : 80;
        
        // Teste de conectividade TCP
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fsockopen -- Required for real-time stream connectivity test
        $socket = @fsockopen($host, $port, $errno, $errstr, 10);
        if (!$socket) {
            /* translators: %1$s: server hostname, %2$s: port number */
            return new \WP_Error('connection_error', sprintf( __( 'Server not reachable: %1$s:%2$s', 'lknwp-radio-browser' ), $host, $port ), array('status' => 502));
        }
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Required for real-time stream connectivity test
        fclose($socket);
        
        // Configurar contexto com headers mais completos e timeouts menores
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'timeout' => 15, // Timeout menor
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'header' => array(
                    'Accept: audio/mpeg, audio/*, */*',
                    'Connection: close', // Evitar keep-alive que pode dar problema
                    'Range: bytes=0-' // Suporte a range requests
                ),
                'ignore_errors' => true,
                'follow_location' => true,
                'max_redirects' => 3
            ),
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false
            )
        ));

        // Tentar conectar ao stream
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Required for real-time audio stream proxy
        $remote_stream = @fopen($stream_url, 'rb', false, $context);
        
        if (!$remote_stream) {
            $error = error_get_last();
            $error_msg = $error ? $error['message'] : __( 'Unknown error', 'lknwp-radio-browser' );
            
            // Tentar diagnóstico via wp_remote_head como fallback
            $response = wp_remote_head($stream_url, array(
                'timeout' => 10,
                'user-agent' => 'LKNWP Radio Browser Proxy/1.0',
                'redirection' => 3,
                'sslverify' => false
            ));
            
            if (is_wp_error($response)) {
                $error_msg = $response->get_error_message();
                /* translators: %s: error message from remote server */
                return new \WP_Error('remote_error', sprintf( __( 'Remote Error: %s', 'lknwp-radio-browser' ), $error_msg ), array('status' => 502));
            }
            
            return new \WP_Error('stream_error', "Stream inacessível: {$error_msg}", array('status' => 502));
        }

        // Capturar e replicar headers do stream original
        $stream_meta = stream_get_meta_data($remote_stream);
        if (isset($stream_meta['wrapper_data'])) {
            foreach ($stream_meta['wrapper_data'] as $header) {
                // Replicar headers importantes
                if (preg_match('/^(Content-Type|Content-Length|Accept-Ranges|Icy-)/i', $header)) {
                    header($header);
                }
            }
        }
        
        // Se não tiver Content-Type, assumir audio/mpeg
        if (!headers_sent()) {
            header('Content-Type: audio/mpeg');
        }

        // Stream direto para o cliente com chunks maiores
        $chunk_size = 16384; // 16KB chunks
        while (!feof($remote_stream)) {
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fread -- Required for real-time audio stream data
            $data = fread($remote_stream, $chunk_size);
            if ($data === false) {
                break;
            }
            
            // Flush mais agressivo para streaming
            if (ob_get_level()) {
                ob_flush();
            }
            flush();
            
            // Verificar se cliente desconectou
            if (connection_aborted()) {
                break;
            }
        }

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Required for real-time audio stream
        fclose($remote_stream);
        exit;
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
				// Radio not found in API - show debug info
				return '<div class="lkp-radio-error">
							<h3>' . __( 'Radio Not Found', 'lknwp-radio-browser' ) . '</h3>
							<p>' . 
							/* translators: %s: radio station name */
							sprintf( __( 'The radio "%s" was not found in our database.', 'lknwp-radio-browser' ), esc_html($radio_name_decoded) ) . '</p>
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
							<h3>' . __( 'No Radio Selected', 'lknwp-radio-browser' ) . '</h3>
							<p>' . __( 'Please select a radio to play.', 'lknwp-radio-browser' ) . '</p>
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
		$limit = isset($_GET['lrt_limit']) ? intval($_GET['lrt_limit']) : (isset($atts['limit']) ? intval($atts['limit']) : 20);
		$player_page = isset($atts['player_page']) ? sanitize_title($atts['player_page']) : 'player';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public shortcode for radio list, nonce not applicable
		$search = isset($_GET['lrt_radio_search']) ? sanitize_text_field(wp_unslash($_GET['lrt_radio_search'])) : '';
		$sort_options = [
			'clickcount' => __( 'Most Popular', 'lknwp-radio-browser' ),
			'name' => __( 'Name', 'lknwp-radio-browser' ),
			'random' => __( 'Random', 'lknwp-radio-browser' ),
			'bitrate' => __( 'Bitrate', 'lknwp-radio-browser' )
		];
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public shortcode for radio list, nonce not applicable
		$sort = isset($_GET['lrt_sort']) && isset($sort_options[sanitize_text_field(wp_unslash($_GET['lrt_sort']))]) ? sanitize_text_field(wp_unslash($_GET['lrt_sort'])) : (isset($atts['sort']) ? $atts['sort'] : 'clickcount');
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public shortcode for radio list, nonce not applicable
		$reverse = isset($_GET['lrt_reverse']) ? sanitize_text_field(wp_unslash($_GET['lrt_reverse'])) : '1'; // 1 = reverse active by default

		$atts = shortcode_atts([
			'countrycode' => $countrycode,
			'limit' => $limit,
			'player_page' => $player_page,
			'sort' => $sort, // Padrão clickcount, mas permite outras opções
			'reverse' => $reverse,
			'search' => $search,
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
			// Se tem busca, usa o endpoint de busca por nome, senão usa por país
			if (!empty($atts['search'])) {
				$api_url = $base_url . '/json/stations/byname/' . urlencode($atts['search']) . '?order=' . $atts['sort'];
				// Adiciona filtro de país se especificado
				if (!empty($atts['countrycode']) && $atts['countrycode'] !== 'ALL') {
					$api_url .= '&countrycode=' . urlencode($atts['countrycode']);
				}
			} else {
				$api_url = $base_url . '/json/stations/bycountrycodeexact/' . urlencode($atts['countrycode']) . '?order=' . $atts['sort'];
			}
			
			if ($atts['reverse'] === '1') {
				$api_url .= '&reverse=true';
			} else {
				$api_url .= '&reverse=false';
			}
			
			// Adiciona limite à URL da API
			$api_url .= '&limit=' . ($atts['limit'] * 2); // Pega mais para compensar possíveis filtros
			
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
		if (!$stations || !is_array($stations)) {
			return '<p>' . __( 'Error fetching radios from all servers.', 'lknwp-radio-browser' ) . '</p>';
		}

		// Prepare variables for template
		$plugin_url = defined('LKNWP_RADIO_BROWSER_PLUGIN_URL') ? LKNWP_RADIO_BROWSER_PLUGIN_URL : '';
		$default_img_url = defined('LKNWP_RADIO_BROWSER_PLUGIN_URL') ? LKNWP_RADIO_BROWSER_PLUGIN_URL . 'Includes/assets/images/default-radio.png' : './Includes/assets/images/default-radio.png';
		
		// Load template
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
		
		// Se não há cache, não faz nada no init (será criado no save_post)
		if ($cached_rules === false) {
			return;
		}
		
		// Só registra se há rules em cache
		if (is_array($cached_rules) && !empty($cached_rules)) {
			foreach ($cached_rules as $rule) {
				add_rewrite_rule($rule['regex'], $rule['redirect'], 'top');
			}
		}
	}

	/**
	 * Busca páginas com shortcode do player e gera regras de rewrite
	 * Compatível com Elementor, Divi, e outros page builders
	 */
	private function get_player_pages_rules() {
		global $wpdb;
		
		// Busca no post_content (Gutenberg, editor clássico, maioria dos page builders)
		$player_pages = $wpdb->get_results($wpdb->prepare("
			SELECT DISTINCT post_name 
			FROM {$wpdb->posts} 
			WHERE post_type = 'page' 
			AND post_status = 'publish' 
			AND post_content LIKE %s
		", '%[radio_browser_player%'));
		
		// Busca também no meta (Elementor e alguns outros page builders)
		$meta_pages = $wpdb->get_results($wpdb->prepare("
			SELECT DISTINCT p.post_name 
			FROM {$wpdb->posts} p
			INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
			WHERE p.post_type = 'page' 
			AND p.post_status = 'publish' 
			AND pm.meta_value LIKE %s
		", '%[radio_browser_player%'));
		
		// Combina resultados e remove duplicatas
		$all_pages = array_merge($player_pages, $meta_pages);
		$unique_slugs = array();
		foreach ($all_pages as $page) {
			if (!in_array($page->post_name, $unique_slugs)) {
				$unique_slugs[] = $page->post_name;
			}
		}
		
		$rules = array();
		foreach ($unique_slugs as $slug) {
			$rules[] = array(
				'regex' => "^{$slug}/([^/]+)/?$",
				'redirect' => "index.php?pagename={$slug}&radio_name=\$matches[1]"
			);
		}
		
		return $rules;
	}

	/**
	 * Atualiza rewrite rules quando página com shortcode é salva
	 * Compatível com Elementor, Divi, e outros page builders
	 */
	public function handle_player_page_changes($post_id, $post) {
		if ($post->post_type !== 'page') return;
		
		$has_shortcode = false;
		
		// Verifica no post_content (método padrão)
		if (has_shortcode($post->post_content, 'radio_browser_player')) {
			$has_shortcode = true;
		}
		
		// Verifica também nos metadados (Elementor, Divi, etc.)
		if (!$has_shortcode) {
			$meta_values = get_post_meta($post_id);
			foreach ($meta_values as $meta_value) {
				if (is_array($meta_value)) {
					foreach ($meta_value as $value) {
						if (is_string($value) && strpos($value, '[radio_browser_player') !== false) {
							$has_shortcode = true;
							break 2;
						}
					}
				} elseif (is_string($meta_value) && strpos($meta_value, '[radio_browser_player') !== false) {
					$has_shortcode = true;
					break;
				}
			}
		}
		
		if ($has_shortcode) {
			// Regenera cache das rules
			$new_rules = $this->get_player_pages_rules();
			update_option('lknwp_player_rewrite_rules', $new_rules);
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

}
