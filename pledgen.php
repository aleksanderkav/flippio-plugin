<?php
/**
 * Plugin Name: Pledgen - Flippio Card Tracker
 * Plugin URI: https://github.com/aleksanderkav/flippio-plugin
 * Description: Display trading card data from Flippio API with searchable, filterable shortcodes and native WordPress styling.
 * Version: 1.0.0
 * Author: Aleksander Kavli
 * License: GPL v2 or later
 * Text Domain: pledgen
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PLEDGEN_VERSION', '1.0.0');
define('PLEDGEN_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PLEDGEN_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PLEDGEN_API_BASE', 'https://vercel-ny-front-flippio.vercel.app/api');

/**
 * Main Pledgen Plugin Class
 */
class Pledgen {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        // Register shortcodes
        add_shortcode('pledgen_cards', array($this, 'cards_shortcode'));
        add_shortcode('pledgen_card', array($this, 'card_shortcode'));
        
        // Admin hooks
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('wp_dashboard_setup', array($this, 'dashboard_widget'));
        
        // AJAX handlers
        add_action('wp_ajax_pledgen_get_cards', array($this, 'ajax_get_cards'));
        add_action('wp_ajax_nopriv_pledgen_get_cards', array($this, 'ajax_get_cards'));
        add_action('wp_ajax_pledgen_get_card', array($this, 'ajax_get_card'));
        add_action('wp_ajax_nopriv_pledgen_get_card', array($this, 'ajax_get_card'));
    }
    
    public function init() {
        load_plugin_textdomain('pledgen', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    public function enqueue_scripts() {
        wp_enqueue_style('pledgen-styles', PLEDGEN_PLUGIN_URL . 'assets/css/pledgen.css', array(), PLEDGEN_VERSION);
        wp_enqueue_script('pledgen-scripts', PLEDGEN_PLUGIN_URL . 'assets/js/pledgen.js', array('jquery'), PLEDGEN_VERSION, true);
        
        wp_localize_script('pledgen-scripts', 'pledgen_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('pledgen_nonce'),
            'api_base' => PLEDGEN_API_BASE
        ));
    }
    
    public function admin_enqueue_scripts($hook) {
        if ('toplevel_page_pledgen' === $hook) {
            wp_enqueue_style('pledgen-admin', PLEDGEN_PLUGIN_URL . 'assets/css/admin.css', array(), PLEDGEN_VERSION);
            wp_enqueue_script('pledgen-admin', PLEDGEN_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), PLEDGEN_VERSION, true);
        }
    }
    
    /**
     * Cards shortcode handler
     */
    public function cards_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 20,
            'category' => '',
            'search' => '',
            'sort_by' => 'created_at',
            'sort_order' => 'desc',
            'show_filters' => 'true',
            'show_pagination' => 'true'
        ), $atts);
        
        // Get cached data or fetch from API
        $cache_key = 'pledgen_cards_' . md5(serialize($atts));
        $cards_data = get_transient($cache_key);
        
        if (false === $cards_data) {
            $cards_data = $this->get_cards_from_api($atts);
            set_transient($cache_key, $cards_data, HOUR_IN_SECONDS);
        }
        
        ob_start();
        include PLEDGEN_PLUGIN_DIR . 'templates/cards-grid.php';
        return ob_get_clean();
    }
    
    /**
     * Single card shortcode handler
     */
    public function card_shortcode($atts) {
        $atts = shortcode_atts(array(
            'slug' => '',
            'id' => ''
        ), $atts);
        
        if (empty($atts['slug']) && empty($atts['id'])) {
            return '<p class="pledgen-error">' . __('Please provide either a slug or ID for the card.', 'pledgen') . '</p>';
        }
        
        // Get cached data or fetch from API
        $cache_key = 'pledgen_card_' . ($atts['slug'] ?: $atts['id']);
        $card_data = get_transient($cache_key);
        
        if (false === $card_data) {
            $card_data = $this->get_card_from_api($atts['slug'] ?: $atts['id']);
            set_transient($cache_key, $card_data, HOUR_IN_SECONDS);
        }
        
        if (!$card_data) {
            return '<p class="pledgen-error">' . __('Card not found.', 'pledgen') . '</p>';
        }
        
        ob_start();
        include PLEDGEN_PLUGIN_DIR . 'templates/single-card.php';
        return ob_get_clean();
    }
    
    /**
     * Fetch cards from API
     */
    private function get_cards_from_api($params) {
        $query_params = array('path' => 'cards');
        
        if (!empty($params['limit'])) {
            $query_params['limit'] = intval($params['limit']);
        }
        if (!empty($params['category'])) {
            $query_params['category'] = sanitize_text_field($params['category']);
        }
        if (!empty($params['search'])) {
            $query_params['search'] = sanitize_text_field($params['search']);
        }
        if (!empty($params['sort_by'])) {
            $query_params['sortBy'] = sanitize_text_field($params['sort_by']);
        }
        if (!empty($params['sort_order'])) {
            $query_params['sortOrder'] = sanitize_text_field($params['sort_order']);
        }
        
        $url = PLEDGEN_API_BASE . '?' . http_build_query($query_params);
        
        $response = wp_remote_get($url, array(
            'timeout' => 30,
            'headers' => array(
                'Accept' => 'application/json'
            )
        ));
        
        if (is_wp_error($response)) {
            error_log('Pledgen API Error: ' . $response->get_error_message());
            return false;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $content_type = wp_remote_retrieve_header($response, 'content-type');
        $body = wp_remote_retrieve_body($response);
        
        // Check if we got HTML instead of JSON (API not working)
        if (strpos($content_type, 'text/html') !== false || strpos($body, '<!doctype html>') !== false) {
            error_log('Pledgen API Error: Received HTML instead of JSON. API endpoints may not be configured correctly.');
            return false;
        }
        
        // Check for 401 or other error status codes
        if ($status_code !== 200) {
            error_log('Pledgen API Error: HTTP ' . $status_code . ' - ' . $body);
            return false;
        }
        
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('Pledgen API Error: Invalid JSON response - ' . json_last_error_msg());
            return false;
        }
        
        return $data['success'] ? $data : false;
    }
    
    /**
     * Fetch single card from API
     */
    private function get_card_from_api($identifier) {
        // Try slug endpoint first, then ID endpoint
        $url = PLEDGEN_API_BASE . '/cards/slug/' . urlencode($identifier);
        
        $response = wp_remote_get($url, array(
            'timeout' => 30,
            'headers' => array(
                'Accept' => 'application/json'
            )
        ));
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if ($data['success']) {
            return $data;
        }
        
        // If slug failed, try ID endpoint
        $url = PLEDGEN_API_BASE . '/cards/' . urlencode($identifier);
        
        $response = wp_remote_get($url, array(
            'timeout' => 30,
            'headers' => array(
                'Accept' => 'application/json'
            )
        ));
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        return $data['success'] ? $data : false;
    }
    
    /**
     * AJAX handler for getting cards
     */
    public function ajax_get_cards() {
        check_ajax_referer('pledgen_nonce', 'nonce');
        
        $params = array(
            'limit' => isset($_POST['limit']) ? intval($_POST['limit']) : 20,
            'offset' => isset($_POST['offset']) ? intval($_POST['offset']) : 0,
            'category' => isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '',
            'search' => isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '',
            'sort_by' => isset($_POST['sort_by']) ? sanitize_text_field($_POST['sort_by']) : 'created_at',
            'sort_order' => isset($_POST['sort_order']) ? sanitize_text_field($_POST['sort_order']) : 'desc'
        );
        
        $data = $this->get_cards_from_api($params);
        
        if ($data) {
            wp_send_json_success($data);
        } else {
            wp_send_json_error(__('Failed to fetch cards.', 'pledgen'));
        }
    }
    
    /**
     * AJAX handler for getting single card
     */
    public function ajax_get_card() {
        check_ajax_referer('pledgen_nonce', 'nonce');
        
        $identifier = isset($_POST['identifier']) ? sanitize_text_field($_POST['identifier']) : '';
        
        if (empty($identifier)) {
            wp_send_json_error(__('Card identifier is required.', 'pledgen'));
        }
        
        $data = $this->get_card_from_api($identifier);
        
        if ($data) {
            wp_send_json_success($data);
        } else {
            wp_send_json_error(__('Card not found.', 'pledgen'));
        }
    }
    
    /**
     * Admin menu
     */
    public function admin_menu() {
        add_menu_page(
            __('Pledgen', 'pledgen'),
            __('Pledgen', 'pledgen'),
            'manage_options',
            'pledgen',
            array($this, 'admin_page'),
            'dashicons-admin-generic',
            30
        );
    }
    
    /**
     * Admin page
     */
    public function admin_page() {
        include PLEDGEN_PLUGIN_DIR . 'templates/admin-page.php';
    }
    
    /**
     * Dashboard widget
     */
    public function dashboard_widget() {
        wp_add_dashboard_widget(
            'pledgen_stats_widget',
            __('Pledgen Card Stats', 'pledgen'),
            array($this, 'dashboard_widget_content')
        );
    }
    
    /**
     * Dashboard widget content
     */
    public function dashboard_widget_content() {
        $stats = $this->get_stats_from_api();
        
        if ($stats) {
            echo '<div class="pledgen-stats-widget">';
            echo '<p><strong>' . __('Total Cards:', 'pledgen') . '</strong> ' . number_format($stats['totalCards']) . '</p>';
            echo '<p><strong>' . __('Total Value:', 'pledgen') . '</strong> $' . number_format($stats['totalValue'], 2) . '</p>';
            echo '<p><strong>' . __('Average Price:', 'pledgen') . '</strong> $' . number_format($stats['averagePrice'], 2) . '</p>';
            echo '<p><strong>' . __('Last Updated:', 'pledgen') . '</strong> ' . date('M j, Y g:i A', strtotime($stats['lastUpdated'])) . '</p>';
            echo '</div>';
        } else {
            echo '<p>' . __('Unable to fetch stats.', 'pledgen') . '</p>';
        }
    }
    
    /**
     * Get stats from API
     */
    private function get_stats_from_api() {
        $cache_key = 'pledgen_stats';
        $stats = get_transient($cache_key);
        
        if (false === $stats) {
            $url = PLEDGEN_API_BASE . '?path=stats';
            
            $response = wp_remote_get($url, array(
                'timeout' => 30,
                'headers' => array(
                    'Accept' => 'application/json'
                )
            ));
            
            if (is_wp_error($response)) {
                return false;
            }
            
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            
            if ($data['success']) {
                $stats = $data['data'];
                set_transient($cache_key, $stats, HOUR_IN_SECONDS);
            } else {
                return false;
            }
        }
        
        return $stats;
    }
}

// Initialize the plugin
Pledgen::get_instance();

// Activation hook
register_activation_hook(__FILE__, 'pledgen_activate');
function pledgen_activate() {
    // Clear any existing transients
    delete_transient('pledgen_stats');
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'pledgen_deactivate');
function pledgen_deactivate() {
    // Clear transients
    delete_transient('pledgen_stats');
} 