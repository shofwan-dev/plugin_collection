<?php
/**
 * WhatsApp API Integration Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class CF7_To_WA_API {
    
    private $api_url = 'https://mpwa.mutekar.com';
    private $api_key;
    private $sender_number;
    
    public function __construct() {
        $this->api_key = get_option('cf7_to_wa_api_key', '');
        $this->sender_number = get_option('cf7_to_wa_sender_number', '');
    }
    
    /**
     * Send text message via WhatsApp
     *
     * @param string $number Recipient number
     * @param string $message Message content
     * @param string $footer Footer text (optional)
     * @param string $msgid Message ID to reply to (optional)
     * @return array Response array with status and message
     */
    public function send_text_message($number, $message, $footer = '', $msgid = '') {
        if (empty($this->api_key) || empty($this->sender_number)) {
            return array(
                'status' => false,
                'message' => __('API Key or Sender Number not configured', 'cf7-to-whatsapp')
            );
        }
        
        // Clean phone number (remove spaces, dashes, etc.)
        $number = $this->clean_phone_number($number);
        
        // Validate phone number
        if (!$this->is_valid_phone_number($number)) {
            return array(
                'status' => false,
                'message' => __('Invalid phone number format', 'cf7-to-whatsapp')
            );
        }
        
        $endpoint = $this->api_url . '/send-message';
        
        $body = array(
            'api_key' => $this->api_key,
            'sender' => $this->sender_number,
            'number' => $number,
            'message' => $message,
        );
        
        if (!empty($footer)) {
            $body['footer'] = $footer;
        }
        
        if (!empty($msgid)) {
            $body['msgid'] = $msgid;
        }
        
        $response = wp_remote_post($endpoint, array(
            'method' => 'POST',
            'timeout' => 30,
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode($body),
        ));
        
        return $this->process_response($response);
    }
    
    /**
     * Send media message via WhatsApp
     *
     * @param string $number Recipient number
     * @param string $media_type Type of media (image, video, audio, document)
     * @param string $url Direct URL to media file
     * @param string $caption Caption/message (optional)
     * @param string $footer Footer text (optional)
     * @param string $msgid Message ID to reply to (optional)
     * @return array Response array with status and message
     */
    public function send_media_message($number, $media_type, $url, $caption = '', $footer = '', $msgid = '') {
        if (empty($this->api_key) || empty($this->sender_number)) {
            return array(
                'status' => false,
                'message' => __('API Key or Sender Number not configured', 'cf7-to-whatsapp')
            );
        }
        
        // Clean phone number
        $number = $this->clean_phone_number($number);
        
        // Validate phone number
        if (!$this->is_valid_phone_number($number)) {
            return array(
                'status' => false,
                'message' => __('Invalid phone number format', 'cf7-to-whatsapp')
            );
        }
        
        // Validate media type
        $allowed_types = array('image', 'video', 'audio', 'document');
        if (!in_array($media_type, $allowed_types)) {
            return array(
                'status' => false,
                'message' => __('Invalid media type', 'cf7-to-whatsapp')
            );
        }
        
        $endpoint = $this->api_url . '/send-media';
        
        $body = array(
            'api_key' => $this->api_key,
            'sender' => $this->sender_number,
            'number' => $number,
            'media_type' => $media_type,
            'url' => $url,
        );
        
        if (!empty($caption)) {
            $body['caption'] = $caption;
        }
        
        if (!empty($footer)) {
            $body['footer'] = $footer;
        }
        
        if (!empty($msgid)) {
            $body['msgid'] = $msgid;
        }
        
        $response = wp_remote_post($endpoint, array(
            'method' => 'POST',
            'timeout' => 30,
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode($body),
        ));
        
        return $this->process_response($response);
    }
    
    /**
     * Check if a phone number is registered on WhatsApp
     *
     * @param string $number Phone number to check
     * @return array Response array with status and data
     */
    public function check_number($number) {
        if (empty($this->api_key) || empty($this->sender_number)) {
            return array(
                'status' => false,
                'message' => __('API Key or Sender Number not configured', 'cf7-to-whatsapp')
            );
        }
        
        // Clean phone number
        $number = $this->clean_phone_number($number);
        
        $endpoint = $this->api_url . '/check-number';
        
        $body = array(
            'api_key' => $this->api_key,
            'sender' => $this->sender_number,
            'number' => $number,
        );
        
        $response = wp_remote_post($endpoint, array(
            'method' => 'POST',
            'timeout' => 30,
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode($body),
        ));
        
        return $this->process_response($response);
    }
    
    /**
     * Process API response
     *
     * @param array|WP_Error $response WordPress HTTP API response
     * @return array Processed response
     */
    private function process_response($response) {
        if (is_wp_error($response)) {
            return array(
                'status' => false,
                'message' => $response->get_error_message(),
                'data' => null
            );
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return array(
                'status' => false,
                'message' => __('Invalid JSON response from API', 'cf7-to-whatsapp'),
                'data' => $body
            );
        }
        
        return array(
            'status' => isset($data['status']) ? $data['status'] : false,
            'message' => isset($data['msg']) ? $data['msg'] : (isset($data['message']) ? $data['message'] : __('Unknown error', 'cf7-to-whatsapp')),
            'data' => isset($data['data']) ? $data['data'] : $data
        );
    }
    
    /**
     * Clean phone number (remove spaces, dashes, parentheses, etc.)
     *
     * @param string $number Phone number
     * @return string Cleaned phone number
     */
    private function clean_phone_number($number) {
        // Remove all non-numeric characters except +
        $number = preg_replace('/[^0-9+]/', '', $number);
        
        // Remove leading zeros and replace with country code if needed
        $number = ltrim($number, '0');
        
        // If number doesn't start with +, assume it needs country code
        if (substr($number, 0, 1) !== '+' && substr($number, 0, 2) !== '62') {
            // Default to Indonesia country code (62) - you can make this configurable
            $number = '62' . $number;
        }
        
        // Remove + sign as API expects number without it
        $number = str_replace('+', '', $number);
        
        return $number;
    }
    
    /**
     * Validate phone number format
     *
     * @param string $number Phone number
     * @return bool True if valid, false otherwise
     */
    private function is_valid_phone_number($number) {
        // Basic validation: should be numeric and have reasonable length
        return preg_match('/^[0-9]{10,15}$/', $number);
    }
}
