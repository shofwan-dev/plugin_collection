<?php
/**
 * Plugin Name: CF7 to WhatsApp Gateway
 * Plugin URI: https://mutekar.com
 * Description: Send Contact Form 7 submissions to WhatsApp via MPWA Gateway
 * Version: 1.0.0
 * Author: Mutekar Digital Solutions
 * Author URI: https://mutekar.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cf7-to-whatsapp
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CF7_TO_WA_VERSION', '1.0.0');
define('CF7_TO_WA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CF7_TO_WA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CF7_TO_WA_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Include required files
require_once CF7_TO_WA_PLUGIN_DIR . 'includes/class-cf7-to-wa-admin.php';
require_once CF7_TO_WA_PLUGIN_DIR . 'includes/class-cf7-to-wa-api.php';
require_once CF7_TO_WA_PLUGIN_DIR . 'includes/class-cf7-to-wa-handler.php';
require_once CF7_TO_WA_PLUGIN_DIR . 'includes/class-cf7-to-wa-logger.php';
require_once CF7_TO_WA_PLUGIN_DIR . 'includes/class-cf7-to-wa-license.php';
require_once CF7_TO_WA_PLUGIN_DIR . 'includes/class-cf7-to-wa-updater.php';

/**
 * Main Plugin Class
 */
class CF7_To_WhatsApp {
    
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
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Initialize admin
        if (is_admin()) {
            CF7_To_WA_Admin::get_instance();
            // Initialize updater for automatic updates
            CF7_To_WA_Updater::get_instance();
        }
        
        // Initialize handler
        CF7_To_WA_Handler::get_instance();
        
        // Load plugin textdomain
        add_action('plugins_loaded', array($this, 'load_textdomain'));
    }
    
    public function activate() {
        // Create database table for logs
        global $wpdb;
        $table_name = $wpdb->prefix . 'cf7_to_wa_logs';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            form_id bigint(20) NOT NULL,
            form_title varchar(255) NOT NULL,
            recipient_number varchar(20) NOT NULL,
            message text NOT NULL,
            response text,
            status varchar(20) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY form_id (form_id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Set default options
        $default_options = array(
            'api_key' => '',
            'sender_number' => '',
            'admin_number' => '',
            'enable_admin_notification' => '1',
            'enable_user_notification' => '0',
            'user_phone_field' => 'phone',
            'message_template' => "New form submission:\n\n{all_fields}\n\nSubmitted at: {submission_date}",
            'admin_message_template' => "New form submission received:\n\n{all_fields}\n\nForm: {form_title}\nSubmitted at: {submission_date}",
            'user_message_template' => "Thank you for your submission!\n\nWe have received your information and will contact you soon.",
            'footer_text' => 'Sent via CF7 to WhatsApp',
            'enable_logging' => '1',
        );
        
        foreach ($default_options as $key => $value) {
            if (get_option('cf7_to_wa_' . $key) === false) {
                add_option('cf7_to_wa_' . $key, $value);
            }
        }
    }
    
    public function deactivate() {
        // Cleanup if needed
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('cf7-to-whatsapp', false, dirname(CF7_TO_WA_PLUGIN_BASENAME) . '/languages');
    }
}

// Initialize the plugin
function cf7_to_wa_init() {
    return CF7_To_WhatsApp::get_instance();
}

// Start the plugin
cf7_to_wa_init();
