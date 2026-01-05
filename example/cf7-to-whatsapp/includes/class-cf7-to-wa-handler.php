<?php
/**
 * Contact Form 7 Submission Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class CF7_To_WA_Handler {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Hook into Contact Form 7 submission
        add_action('wpcf7_mail_sent', array($this, 'handle_cf7_submission'));
    }
    
    /**
     * Handle Contact Form 7 submission
     *
     * @param WPCF7_ContactForm $contact_form CF7 form object
     */
    public function handle_cf7_submission($contact_form) {
        // Get submission data
        $submission = WPCF7_Submission::get_instance();
        
        if (!$submission) {
            return;
        }
        
        $posted_data = $submission->get_posted_data();
        $form_id = $contact_form->id();
        $form_title = $contact_form->title();
        
        // Send admin notification
        if (get_option('cf7_to_wa_enable_admin_notification', '1') === '1') {
            $this->send_admin_notification($form_id, $form_title, $posted_data);
        }
        
        // Send user notification
        if (get_option('cf7_to_wa_enable_user_notification', '0') === '1') {
            $this->send_user_notification($form_id, $form_title, $posted_data);
        }
    }
    
    /**
     * Send notification to admin
     *
     * @param int $form_id Form ID
     * @param string $form_title Form title
     * @param array $posted_data Submitted form data
     */
    private function send_admin_notification($form_id, $form_title, $posted_data) {
        $admin_numbers = get_option('cf7_to_wa_admin_number', '');
        
        // Convert to array if it's a single number (backward compatibility)
        if (!is_array($admin_numbers)) {
            $admin_numbers = !empty($admin_numbers) ? array($admin_numbers) : array();
        }
        
        // Remove empty values
        $admin_numbers = array_filter($admin_numbers);
        
        if (empty($admin_numbers)) {
            return;
        }
        
        $message = $this->format_message($posted_data, $form_title, 'admin');
        $footer = get_option('cf7_to_wa_footer_text', '');
        
        $api = new CF7_To_WA_API();
        
        // Send to each admin number
        foreach ($admin_numbers as $admin_number) {
            $admin_number = trim($admin_number);
            
            if (empty($admin_number)) {
                continue;
            }
            
            $result = $api->send_text_message($admin_number, $message, $footer);
            
            // Log the result
            if (get_option('cf7_to_wa_enable_logging', '1') === '1') {
                CF7_To_WA_Logger::log(
                    $form_id,
                    $form_title,
                    $admin_number,
                    $message,
                    $result,
                    'admin'
                );
            }
        }
    }
    
    /**
     * Send notification to user
     *
     * @param int $form_id Form ID
     * @param string $form_title Form title
     * @param array $posted_data Submitted form data
     */
    private function send_user_notification($form_id, $form_title, $posted_data) {
        $phone_field = get_option('cf7_to_wa_user_phone_field', 'phone');
        
        // Get user phone number from submitted data
        $user_number = '';
        if (isset($posted_data[$phone_field])) {
            $user_number = $posted_data[$phone_field];
        }
        
        if (empty($user_number)) {
            return;
        }
        
        $message = $this->format_message($posted_data, $form_title, 'user');
        $footer = get_option('cf7_to_wa_footer_text', '');
        
        $api = new CF7_To_WA_API();
        $result = $api->send_text_message($user_number, $message, $footer);
        
        // Log the result
        if (get_option('cf7_to_wa_enable_logging', '1') === '1') {
            CF7_To_WA_Logger::log(
                $form_id,
                $form_title,
                $user_number,
                $message,
                $result,
                'user'
            );
        }
    }
    
    /**
     * Format message from template
     *
     * @param array $posted_data Submitted form data
     * @param string $form_title Form title
     * @param string $type Message type (admin or user)
     * @return string Formatted message
     */
    private function format_message($posted_data, $form_title, $type = 'admin') {
        $template_option = $type === 'admin' ? 'cf7_to_wa_admin_message_template' : 'cf7_to_wa_user_message_template';
        $template = get_option($template_option, '');
        
        if (empty($template)) {
            $template = "New form submission:\n\n{all_fields}\n\nSubmitted at: {submission_date}";
        }
        
        // Replace placeholders
        $message = $template;
        
        // Replace {form_title}
        $message = str_replace('{form_title}', $form_title, $message);
        
        // Replace {submission_date}
        $message = str_replace('{submission_date}', current_time('Y-m-d H:i:s'), $message);
        
        // Replace {all_fields}
        $all_fields = $this->format_all_fields($posted_data);
        $message = str_replace('{all_fields}', $all_fields, $message);
        
        // Replace individual field placeholders like {field_name}
        foreach ($posted_data as $key => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $message = str_replace('{' . $key . '}', $value, $message);
        }
        
        return $message;
    }
    
    /**
     * Format all fields for display
     *
     * @param array $posted_data Submitted form data
     * @return string Formatted fields
     */
    private function format_all_fields($posted_data) {
        $fields = array();
        
        // Skip internal CF7 fields
        $skip_fields = array('_wpcf7', '_wpcf7_version', '_wpcf7_locale', '_wpcf7_unit_tag', '_wpcf7_container_post', '_wpcf7_posted_data_hash');
        
        foreach ($posted_data as $key => $value) {
            // Skip internal fields and empty values
            if (in_array($key, $skip_fields) || (empty($value) && $value !== '0')) {
                continue;
            }
            
            // Convert array values to comma-separated string
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            
            // Format field name (convert underscore/dash to space and capitalize)
            $field_name = ucwords(str_replace(array('_', '-'), ' ', $key));
            
            $fields[] = "*{$field_name}:* {$value}";
        }
        
        return implode("\n", $fields);
    }
}
