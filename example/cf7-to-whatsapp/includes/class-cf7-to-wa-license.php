<?php
/**
 * License Management Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class CF7_To_WA_License {
    
    private static $instance = null;
    private $api_url = 'https://your-domain.com/api/v1/license'; // Update this with your actual domain
    private $option_name = 'cf7_to_wa_license';
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Check license status periodically
        add_action('admin_init', array($this, 'check_license_validity'));
    }
    
    /**
     * Get current domain
     *
     * @return string
     */
    private function get_domain() {
        $domain = parse_url(home_url(), PHP_URL_HOST);
        // Remove www. prefix
        $domain = preg_replace('/^www\./', '', $domain);
        return $domain;
    }
    
    /**
     * Get license data from database
     *
     * @return array|false
     */
    public function get_license_data() {
        return get_option($this->option_name, false);
    }
    
    /**
     * Save license data to database
     *
     * @param array $data License data
     * @return bool
     */
    private function save_license_data($data) {
        return update_option($this->option_name, $data);
    }
    
    /**
     * Delete license data from database
     *
     * @return bool
     */
    private function delete_license_data() {
        return delete_option($this->option_name);
    }
    
    /**
     * Check if plugin is licensed
     *
     * @return bool
     */
    public function is_licensed() {
        $license = $this->get_license_data();
        
        if (!$license || !isset($license['key']) || !isset($license['status'])) {
            return false;
        }
        
        if ($license['status'] !== 'active') {
            return false;
        }
        
        // Check if license is expired
        if (isset($license['expires_at'])) {
            $expires = strtotime($license['expires_at']);
            if ($expires && $expires < time()) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Activate license key
     *
     * @param string $license_key License key to activate
     * @return array Result array with success status and message
     */
    public function activate_license($license_key) {
        $license_key = sanitize_text_field($license_key);
        $domain = $this->get_domain();
        
        if (empty($license_key)) {
            return array(
                'success' => false,
                'message' => __('Please enter a license key.', 'cf7-to-whatsapp')
            );
        }
        
        // Call API to activate license
        $response = wp_remote_post($this->api_url . '/activate', array(
            'method' => 'POST',
            'timeout' => 30,
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode(array(
                'license_key' => $license_key,
                'domain' => $domain,
                'plugin_version' => CF7_TO_WA_VERSION,
            )),
        ));
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => sprintf(__('Connection error: %s', 'cf7-to-whatsapp'), $response->get_error_message())
            );
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return array(
                'success' => false,
                'message' => __('Invalid response from license server.', 'cf7-to-whatsapp')
            );
        }
        
        if (!isset($data['success']) || !$data['success']) {
            return array(
                'success' => false,
                'message' => isset($data['message']) ? $data['message'] : __('License activation failed.', 'cf7-to-whatsapp')
            );
        }
        
        // Save license data
        $license_data = array(
            'key' => $license_key,
            'status' => 'active',
            'domain' => $domain,
            'activated_at' => current_time('mysql'),
            'expires_at' => isset($data['expires_at']) ? $data['expires_at'] : null,
            'plan' => isset($data['plan']) ? $data['plan'] : 'unknown',
            'activation_id' => isset($data['activation_id']) ? $data['activation_id'] : null,
        );
        
        $this->save_license_data($license_data);
        
        return array(
            'success' => true,
            'message' => __('License activated successfully!', 'cf7-to-whatsapp'),
            'data' => $license_data
        );
    }
    
    /**
     * Deactivate license
     *
     * @return array Result array with success status and message
     */
    public function deactivate_license() {
        $license = $this->get_license_data();
        
        if (!$license || !isset($license['key'])) {
            return array(
                'success' => false,
                'message' => __('No active license found.', 'cf7-to-whatsapp')
            );
        }
        
        $domain = $this->get_domain();
        
        // Call API to deactivate license
        $response = wp_remote_post($this->api_url . '/deactivate', array(
            'method' => 'POST',
            'timeout' => 30,
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode(array(
                'license_key' => $license['key'],
                'domain' => $domain,
            )),
        ));
        
        if (is_wp_error($response)) {
            // Even if API call fails, remove local license data
            $this->delete_license_data();
            
            return array(
                'success' => true,
                'message' => __('License deactivated locally. Server may not be reachable.', 'cf7-to-whatsapp')
            );
        }
        
        // Remove local license data
        $this->delete_license_data();
        
        return array(
            'success' => true,
            'message' => __('License deactivated successfully!', 'cf7-to-whatsapp')
        );
    }
    
    /**
     * Validate license with server
     *
     * @return array Validation result
     */
    public function validate_license() {
        $license = $this->get_license_data();
        
        if (!$license || !isset($license['key'])) {
            return array(
                'valid' => false,
                'message' => __('No license key found.', 'cf7-to-whatsapp')
            );
        }
        
        $domain = $this->get_domain();
        
        // Call API to validate license
        $response = wp_remote_post($this->api_url . '/verify', array(
            'method' => 'POST',
            'timeout' => 30,
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode(array(
                'license_key' => $license['key'],
                'domain' => $domain,
            )),
        ));
        
        if (is_wp_error($response)) {
            return array(
                'valid' => false,
                'message' => sprintf(__('Connection error: %s', 'cf7-to-whatsapp'), $response->get_error_message())
            );
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return array(
                'valid' => false,
                'message' => __('Invalid response from license server.', 'cf7-to-whatsapp')
            );
        }
        
        if (isset($data['valid']) && $data['valid']) {
            // Update local license data
            $license['status'] = 'active';
            $license['last_checked'] = current_time('mysql');
            if (isset($data['expires_at'])) {
                $license['expires_at'] = $data['expires_at'];
            }
            $this->save_license_data($license);
            
            return array(
                'valid' => true,
                'message' => __('License is valid.', 'cf7-to-whatsapp'),
                'data' => $data
            );
        }
        
        // License is invalid, update status
        $license['status'] = 'invalid';
        $this->save_license_data($license);
        
        return array(
            'valid' => false,
            'message' => isset($data['message']) ? $data['message'] : __('License is invalid.', 'cf7-to-whatsapp')
        );
    }
    
    /**
     * Check license validity periodically
     */
    public function check_license_validity() {
        $license = $this->get_license_data();
        
        if (!$license || !isset($license['key'])) {
            return;
        }
        
        // Check once per day
        $last_checked = isset($license['last_checked']) ? strtotime($license['last_checked']) : 0;
        $check_interval = 24 * 60 * 60; // 24 hours
        
        if (time() - $last_checked < $check_interval) {
            return;
        }
        
        // Validate license in background
        $this->validate_license();
    }
    
    /**
     * Get license status for display
     *
     * @return array License status information
     */
    public function get_license_status() {
        $license = $this->get_license_data();
        
        if (!$license) {
            return array(
                'status' => 'inactive',
                'message' => __('No license activated', 'cf7-to-whatsapp'),
                'color' => 'gray'
            );
        }
        
        if ($license['status'] === 'active') {
            $expires_at = isset($license['expires_at']) ? strtotime($license['expires_at']) : null;
            
            if ($expires_at && $expires_at < time()) {
                return array(
                    'status' => 'expired',
                    'message' => __('License expired', 'cf7-to-whatsapp'),
                    'color' => 'red',
                    'expires_at' => $license['expires_at']
                );
            }
            
            return array(
                'status' => 'active',
                'message' => __('License active', 'cf7-to-whatsapp'),
                'color' => 'green',
                'expires_at' => isset($license['expires_at']) ? $license['expires_at'] : null,
                'plan' => isset($license['plan']) ? $license['plan'] : 'Unknown'
            );
        }
        
        return array(
            'status' => 'invalid',
            'message' => __('License invalid', 'cf7-to-whatsapp'),
            'color' => 'red'
        );
    }
}
