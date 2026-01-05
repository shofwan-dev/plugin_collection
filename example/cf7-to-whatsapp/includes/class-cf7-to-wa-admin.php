<?php
/**
 * Admin Settings Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class CF7_To_WA_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // AJAX handlers
        add_action('wp_ajax_cf7_to_wa_resend_message', array($this, 'ajax_resend_message'));
    }
    
    public function add_admin_menu() {
        add_menu_page(
            __('CF7 to WhatsApp', 'cf7-to-whatsapp'),
            __('CF7 to WhatsApp', 'cf7-to-whatsapp'),
            'manage_options',
            'cf7-to-whatsapp',
            array($this, 'render_settings_page'),
            'dashicons-whatsapp',
            30
        );
        
        add_submenu_page(
            'cf7-to-whatsapp',
            __('Settings', 'cf7-to-whatsapp'),
            __('Settings', 'cf7-to-whatsapp'),
            'manage_options',
            'cf7-to-whatsapp',
            array($this, 'render_settings_page')
        );
        
        add_submenu_page(
            'cf7-to-whatsapp',
            __('Logs', 'cf7-to-whatsapp'),
            __('Logs', 'cf7-to-whatsapp'),
            'manage_options',
            'cf7-to-whatsapp-logs',
            array($this, 'render_logs_page')
        );
        
        add_submenu_page(
            'cf7-to-whatsapp',
            __('License', 'cf7-to-whatsapp'),
            __('License', 'cf7-to-whatsapp'),
            'manage_options',
            'cf7-to-whatsapp-license',
            array($this, 'render_license_page')
        );
    }
    
    public function register_settings() {
        // API Settings
        register_setting('cf7_to_wa_settings', 'cf7_to_wa_api_key');
        register_setting('cf7_to_wa_settings', 'cf7_to_wa_sender_number');
        register_setting('cf7_to_wa_settings', 'cf7_to_wa_admin_number');
        
        // Notification Settings
        register_setting('cf7_to_wa_settings', 'cf7_to_wa_enable_admin_notification');
        register_setting('cf7_to_wa_settings', 'cf7_to_wa_enable_user_notification');
        register_setting('cf7_to_wa_settings', 'cf7_to_wa_user_phone_field');
        
        // Message Templates
        register_setting('cf7_to_wa_settings', 'cf7_to_wa_admin_message_template');
        register_setting('cf7_to_wa_settings', 'cf7_to_wa_user_message_template');
        register_setting('cf7_to_wa_settings', 'cf7_to_wa_footer_text');
        
        // Other Settings
        register_setting('cf7_to_wa_settings', 'cf7_to_wa_enable_logging');
    }
    
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'cf7-to-whatsapp') === false) {
            return;
        }
        
        wp_enqueue_style('cf7-to-wa-admin', CF7_TO_WA_PLUGIN_URL . 'assets/css/admin.css', array(), CF7_TO_WA_VERSION);
        wp_enqueue_script('cf7-to-wa-admin', CF7_TO_WA_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), CF7_TO_WA_VERSION, true);
        
        wp_localize_script('cf7-to-wa-admin', 'cf7ToWaAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cf7_to_wa_nonce'),
        ));
    }
    
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Handle test message
        if (isset($_POST['cf7_to_wa_test_message']) && check_admin_referer('cf7_to_wa_test', 'cf7_to_wa_test_nonce')) {
            $this->send_test_message();
        }
        
        include CF7_TO_WA_PLUGIN_DIR . 'templates/admin-settings.php';
    }
    
    public function render_logs_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Handle log deletion
        if (isset($_POST['cf7_to_wa_clear_logs']) && check_admin_referer('cf7_to_wa_clear_logs', 'cf7_to_wa_clear_logs_nonce')) {
            $this->clear_logs();
        }
        
        $logs = CF7_To_WA_Logger::get_logs();
        include CF7_TO_WA_PLUGIN_DIR . 'templates/admin-logs.php';
    }
    
    public function render_license_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Handle license activation
        if (isset($_POST['cf7_to_wa_activate']) && check_admin_referer('cf7_to_wa_activate_license', 'cf7_to_wa_license_nonce')) {
            $this->activate_license();
        }
        
        // Handle license deactivation
        if (isset($_POST['cf7_to_wa_deactivate']) && check_admin_referer('cf7_to_wa_deactivate_license', 'cf7_to_wa_license_nonce')) {
            $this->deactivate_license();
        }
        
        // Handle license validation
        if (isset($_POST['cf7_to_wa_validate']) && check_admin_referer('cf7_to_wa_deactivate_license', 'cf7_to_wa_license_nonce')) {
            $this->validate_license();
        }
        
        include CF7_TO_WA_PLUGIN_DIR . 'templates/admin-license.php';
    }
    
    private function send_test_message() {
        $api = new CF7_To_WA_API();
        $test_number = sanitize_text_field($_POST['test_number']);
        
        $result = $api->send_text_message(
            $test_number,
            'This is a test message from CF7 to WhatsApp plugin.',
            'Test Message'
        );
        
        if ($result['status']) {
            add_settings_error(
                'cf7_to_wa_messages',
                'cf7_to_wa_message',
                __('Test message sent successfully!', 'cf7-to-whatsapp'),
                'success'
            );
        } else {
            add_settings_error(
                'cf7_to_wa_messages',
                'cf7_to_wa_message',
                sprintf(__('Failed to send test message: %s', 'cf7-to-whatsapp'), $result['message']),
                'error'
            );
        }
    }
    
    public function ajax_resend_message() {
        check_ajax_referer('cf7_to_wa_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'cf7-to-whatsapp')));
        }
        
        $log_id = isset($_POST['log_id']) ? intval($_POST['log_id']) : 0;
        
        if (!$log_id) {
            wp_send_json_error(array('message' => __('Invalid log ID', 'cf7-to-whatsapp')));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'cf7_to_wa_logs';
        
        // Get log entry
        $log = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $log_id
        ), ARRAY_A);
        
        if (!$log) {
            wp_send_json_error(array('message' => __('Log not found', 'cf7-to-whatsapp')));
        }
        
        // Resend message
        $api = new CF7_To_WA_API();
        $footer = get_option('cf7_to_wa_footer_text', '');
        
        $result = $api->send_text_message(
            $log['recipient_number'],
            $log['message'],
            $footer
        );
        
        // Update log
        $status = isset($result['status']) && $result['status'] ? 'success' : 'failed';
        
        $wpdb->update(
            $table_name,
            array(
                'response' => json_encode($result),
                'status' => $status,
                'created_at' => current_time('mysql'),
            ),
            array('id' => $log_id),
            array('%s', '%s', '%s'),
            array('%d')
        );
        
        if ($result['status']) {
            wp_send_json_success(array(
                'message' => __('Message resent successfully!', 'cf7-to-whatsapp'),
                'status' => $status
            ));
        } else {
            wp_send_json_error(array(
                'message' => sprintf(__('Failed to resend: %s', 'cf7-to-whatsapp'), $result['message']),
                'status' => $status
            ));
        }
    }
    
    private function clear_logs() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cf7_to_wa_logs';
        $wpdb->query("TRUNCATE TABLE $table_name");
        
        add_settings_error(
            'cf7_to_wa_messages',
            'cf7_to_wa_message',
            __('Logs cleared successfully!', 'cf7-to-whatsapp'),
            'success'
        );
    }
    
    private function activate_license() {
        $license_key = isset($_POST['license_key']) ? sanitize_text_field($_POST['license_key']) : '';
        
        $license_manager = CF7_To_WA_License::get_instance();
        $result = $license_manager->activate_license($license_key);
        
        if ($result['success']) {
            add_settings_error(
                'cf7_to_wa_license_messages',
                'cf7_to_wa_license_message',
                $result['message'],
                'success'
            );
        } else {
            add_settings_error(
                'cf7_to_wa_license_messages',
                'cf7_to_wa_license_message',
                $result['message'],
                'error'
            );
        }
    }
    
    private function deactivate_license() {
        $license_manager = CF7_To_WA_License::get_instance();
        $result = $license_manager->deactivate_license();
        
        if ($result['success']) {
            add_settings_error(
                'cf7_to_wa_license_messages',
                'cf7_to_wa_license_message',
                $result['message'],
                'success'
            );
        } else {
            add_settings_error(
                'cf7_to_wa_license_messages',
                'cf7_to_wa_license_message',
                $result['message'],
                'error'
            );
        }
    }
    
    private function validate_license() {
        $license_manager = CF7_To_WA_License::get_instance();
        $result = $license_manager->validate_license();
        
        if ($result['valid']) {
            add_settings_error(
                'cf7_to_wa_license_messages',
                'cf7_to_wa_license_message',
                $result['message'],
                'success'
            );
        } else {
            add_settings_error(
                'cf7_to_wa_license_messages',
                'cf7_to_wa_license_message',
                $result['message'],
                'error'
            );
        }
    }
}
