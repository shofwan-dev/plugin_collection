<?php
/**
 * Admin Logs Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get statistics
$stats = CF7_To_WA_Logger::get_statistics();

// Pagination
$per_page = 20;
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$offset = ($current_page - 1) * $per_page;

// Filter
$status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';

// Get logs
$logs = CF7_To_WA_Logger::get_logs($per_page, $offset, $status_filter);
$total_logs = CF7_To_WA_Logger::get_logs_count($status_filter);
$total_pages = ceil($total_logs / $per_page);
?>

<div class="wrap cf7-to-wa-logs">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <?php settings_errors('cf7_to_wa_messages'); ?>
    
    <!-- Statistics -->
    <div class="cf7-to-wa-stats">
        <div class="cf7-to-wa-stat-box">
            <div class="stat-number"><?php echo number_format($stats['total']); ?></div>
            <div class="stat-label"><?php _e('Total Messages', 'cf7-to-whatsapp'); ?></div>
        </div>
        <div class="cf7-to-wa-stat-box success">
            <div class="stat-number"><?php echo number_format($stats['success']); ?></div>
            <div class="stat-label"><?php _e('Successful', 'cf7-to-whatsapp'); ?></div>
        </div>
        <div class="cf7-to-wa-stat-box failed">
            <div class="stat-number"><?php echo number_format($stats['failed']); ?></div>
            <div class="stat-label"><?php _e('Failed', 'cf7-to-whatsapp'); ?></div>
        </div>
        <div class="cf7-to-wa-stat-box">
            <div class="stat-number"><?php echo $stats['success_rate']; ?>%</div>
            <div class="stat-label"><?php _e('Success Rate', 'cf7-to-whatsapp'); ?></div>
        </div>
        <div class="cf7-to-wa-stat-box">
            <div class="stat-number"><?php echo number_format($stats['today']); ?></div>
            <div class="stat-label"><?php _e('Today', 'cf7-to-whatsapp'); ?></div>
        </div>
    </div>
    
    <!-- Filters and Actions -->
    <div class="tablenav top">
        <div class="alignleft actions">
            <form method="get" action="">
                <input type="hidden" name="page" value="cf7-to-whatsapp-logs">
                <select name="status" id="status-filter">
                    <option value=""><?php _e('All Statuses', 'cf7-to-whatsapp'); ?></option>
                    <option value="success" <?php selected($status_filter, 'success'); ?>><?php _e('Success', 'cf7-to-whatsapp'); ?></option>
                    <option value="failed" <?php selected($status_filter, 'failed'); ?>><?php _e('Failed', 'cf7-to-whatsapp'); ?></option>
                </select>
                <input type="submit" class="button" value="<?php _e('Filter', 'cf7-to-whatsapp'); ?>">
            </form>
        </div>
        <div class="alignright actions">
            <form method="post" action="" onsubmit="return confirm('<?php _e('Are you sure you want to clear all logs?', 'cf7-to-whatsapp'); ?>');">
                <?php wp_nonce_field('cf7_to_wa_clear_logs', 'cf7_to_wa_clear_logs_nonce'); ?>
                <input type="submit" name="cf7_to_wa_clear_logs" class="button button-secondary" value="<?php _e('Clear All Logs', 'cf7-to-whatsapp'); ?>">
            </form>
        </div>
    </div>
    
    <!-- Logs Table -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width: 50px;"><?php _e('ID', 'cf7-to-whatsapp'); ?></th>
                <th><?php _e('Form', 'cf7-to-whatsapp'); ?></th>
                <th><?php _e('Recipient', 'cf7-to-whatsapp'); ?></th>
                <th><?php _e('Message', 'cf7-to-whatsapp'); ?></th>
                <th><?php _e('Status', 'cf7-to-whatsapp'); ?></th>
                <th><?php _e('Response', 'cf7-to-whatsapp'); ?></th>
                <th><?php _e('Date', 'cf7-to-whatsapp'); ?></th>
                <th><?php _e('Action', 'cf7-to-whatsapp'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)) : ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">
                        <?php _e('No logs found.', 'cf7-to-whatsapp'); ?>
                    </td>
                </tr>
            <?php else : ?>
                <?php foreach ($logs as $log) : ?>
                    <tr>
                        <td><?php echo esc_html($log['id']); ?></td>
                        <td>
                            <strong><?php echo esc_html($log['form_title']); ?></strong>
                            <br>
                            <small>ID: <?php echo esc_html($log['form_id']); ?></small>
                        </td>
                        <td><?php echo esc_html($log['recipient_number']); ?></td>
                        <td>
                            <div class="cf7-to-wa-message-preview">
                                <?php echo esc_html(wp_trim_words($log['message'], 15)); ?>
                            </div>
                            <button type="button" class="button button-small cf7-to-wa-view-full" data-message="<?php echo esc_attr($log['message']); ?>">
                                <?php _e('View Full', 'cf7-to-whatsapp'); ?>
                            </button>
                        </td>
                        <td>
                            <span class="cf7-to-wa-status cf7-to-wa-status-<?php echo esc_attr($log['status']); ?>">
                                <?php echo esc_html(ucfirst($log['status'])); ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $response = json_decode($log['response'], true);
                            if ($response && isset($response['message'])) {
                                echo esc_html($response['message']);
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($log['created_at']))); ?>
                        </td>
                        <td>
                            <button type="button" 
                                    class="button button-small cf7-to-wa-resend" 
                                    data-log-id="<?php echo esc_attr($log['id']); ?>"
                                    data-recipient="<?php echo esc_attr($log['recipient_number']); ?>">
                                <span class="dashicons dashicons-update-alt" style="margin-top: 3px;"></span>
                                <?php _e('Resend', 'cf7-to-whatsapp'); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1) : ?>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <?php
                echo paginate_links(array(
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => __('&laquo;'),
                    'next_text' => __('&raquo;'),
                    'total' => $total_pages,
                    'current' => $current_page,
                ));
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal for viewing full message -->
<div id="cf7-to-wa-message-modal" class="cf7-to-wa-modal" style="display: none;">
    <div class="cf7-to-wa-modal-content">
        <span class="cf7-to-wa-modal-close">&times;</span>
        <h2><?php _e('Full Message', 'cf7-to-whatsapp'); ?></h2>
        <div id="cf7-to-wa-message-full"></div>
    </div>
</div>
