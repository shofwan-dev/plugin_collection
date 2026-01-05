<?php
/**
 * Logger Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class CF7_To_WA_Logger {
    
    /**
     * Log a WhatsApp message attempt
     *
     * @param int $form_id Form ID
     * @param string $form_title Form title
     * @param string $recipient_number Recipient phone number
     * @param string $message Message content
     * @param array $response API response
     * @param string $type Message type (admin or user)
     */
    public static function log($form_id, $form_title, $recipient_number, $message, $response, $type = 'admin') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cf7_to_wa_logs';
        
        $status = isset($response['status']) && $response['status'] ? 'success' : 'failed';
        
        $wpdb->insert(
            $table_name,
            array(
                'form_id' => $form_id,
                'form_title' => $form_title,
                'recipient_number' => $recipient_number,
                'message' => $message,
                'response' => json_encode($response),
                'status' => $status,
                'created_at' => current_time('mysql'),
            ),
            array('%d', '%s', '%s', '%s', '%s', '%s', '%s')
        );
    }
    
    /**
     * Get logs from database
     *
     * @param int $limit Number of logs to retrieve
     * @param int $offset Offset for pagination
     * @param string $status Filter by status (success, failed, or empty for all)
     * @return array Array of log entries
     */
    public static function get_logs($limit = 50, $offset = 0, $status = '') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cf7_to_wa_logs';
        
        $where = '';
        if (!empty($status)) {
            $where = $wpdb->prepare(" WHERE status = %s", $status);
        }
        
        $query = "SELECT * FROM $table_name{$where} ORDER BY created_at DESC LIMIT %d OFFSET %d";
        $results = $wpdb->get_results($wpdb->prepare($query, $limit, $offset), ARRAY_A);
        
        return $results;
    }
    
    /**
     * Get total count of logs
     *
     * @param string $status Filter by status (success, failed, or empty for all)
     * @return int Total count
     */
    public static function get_logs_count($status = '') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cf7_to_wa_logs';
        
        $where = '';
        if (!empty($status)) {
            $where = $wpdb->prepare(" WHERE status = %s", $status);
        }
        
        $query = "SELECT COUNT(*) FROM $table_name{$where}";
        return (int) $wpdb->get_var($query);
    }
    
    /**
     * Delete old logs
     *
     * @param int $days Delete logs older than X days
     * @return int Number of deleted rows
     */
    public static function delete_old_logs($days = 30) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cf7_to_wa_logs';
        
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $wpdb->query(
            $wpdb->prepare("DELETE FROM $table_name WHERE created_at < %s", $date)
        );
    }
    
    /**
     * Get statistics
     *
     * @return array Statistics data
     */
    public static function get_statistics() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cf7_to_wa_logs';
        
        $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $success = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'success'");
        $failed = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'failed'");
        
        $today = date('Y-m-d');
        $today_total = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE DATE(created_at) = %s",
            $today
        ));
        
        return array(
            'total' => (int) $total,
            'success' => (int) $success,
            'failed' => (int) $failed,
            'today' => (int) $today_total,
            'success_rate' => $total > 0 ? round(($success / $total) * 100, 2) : 0,
        );
    }
}
