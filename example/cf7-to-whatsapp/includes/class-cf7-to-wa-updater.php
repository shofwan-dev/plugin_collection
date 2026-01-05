<?php
/**
 * Plugin Updater Class
 * Handles automatic updates from license server
 */

if (!defined('ABSPATH')) {
    exit;
}

class CF7_To_WA_Updater {
    
    private static $instance = null;
    private $api_url = 'https://your-domain.com/api/v1/license'; // Update this with your actual domain
    private $plugin_slug = 'cf7-to-whatsapp';
    private $plugin_file;
    private $version;
    private $license_manager;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->plugin_file = CF7_TO_WA_PLUGIN_BASENAME;
        $this->version = CF7_TO_WA_VERSION;
        $this->license_manager = CF7_To_WA_License::get_instance();
        
        // Hook into WordPress update system
        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));
        add_filter('plugins_api', array($this, 'plugin_info'), 20, 3);
    }
    
    /**
     * Check for plugin updates
     *
     * @param object $transient Update transient
     * @return object Modified transient
     */
    public function check_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }
        
        // Get license data
        $license = $this->license_manager->get_license_data();
        
        if (!$license || !isset($license['key'])) {
            return $transient;
        }
        
        // Check for updates
        $update_data = $this->get_update_data($license['key']);
        
        if ($update_data && isset($update_data['update_available']) && $update_data['update_available']) {
            $plugin_data = array(
                'slug' => $this->plugin_slug,
                'new_version' => $update_data['latest_version'],
                'url' => 'https://your-domain.com',
                'package' => $update_data['download_url'],
                'tested' => '6.4',
                'requires_php' => '7.4',
            );
            
            $transient->response[$this->plugin_file] = (object) $plugin_data;
        }
        
        return $transient;
    }
    
    /**
     * Get update data from API
     *
     * @param string $license_key License key
     * @return array|false Update data or false on failure
     */
    private function get_update_data($license_key) {
        $response = wp_remote_post($this->api_url . '/check-update', array(
            'method' => 'POST',
            'timeout' => 30,
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode(array(
                'license_key' => $license_key,
                'current_version' => $this->version,
                'product_slug' => $this->plugin_slug,
            )),
        ));
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['success']) || !$data['success']) {
            return false;
        }
        
        return $data;
    }
    
    /**
     * Provide plugin information for update screen
     *
     * @param false|object|array $result The result object or array
     * @param string $action The type of information being requested
     * @param object $args Plugin API arguments
     * @return false|object Modified result
     */
    public function plugin_info($result, $action, $args) {
        if ($action !== 'plugin_information') {
            return $result;
        }
        
        if (!isset($args->slug) || $args->slug !== $this->plugin_slug) {
            return $result;
        }
        
        // Get license data
        $license = $this->license_manager->get_license_data();
        
        if (!$license || !isset($license['key'])) {
            return $result;
        }
        
        // Get update data
        $update_data = $this->get_update_data($license['key']);
        
        if (!$update_data) {
            return $result;
        }
        
        $plugin_info = new stdClass();
        $plugin_info->name = 'CF7 to WhatsApp Gateway';
        $plugin_info->slug = $this->plugin_slug;
        $plugin_info->version = $update_data['latest_version'];
        $plugin_info->author = '<a href="https://your-domain.com">Your Company</a>';
        $plugin_info->homepage = 'https://your-domain.com';
        $plugin_info->requires = '5.0';
        $plugin_info->tested = '6.4';
        $plugin_info->requires_php = '7.4';
        $plugin_info->download_link = $update_data['download_url'];
        $plugin_info->sections = array(
            'description' => 'Send Contact Form 7 submissions to WhatsApp via MPWA Gateway',
            'changelog' => isset($update_data['changelog']) ? nl2br($update_data['changelog']) : '',
        );
        
        return $plugin_info;
    }
}
