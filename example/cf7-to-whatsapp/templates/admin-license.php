<?php
/**
 * Admin License Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$license_manager = CF7_To_WA_License::get_instance();
$license_status = $license_manager->get_license_status();
$license_data = $license_manager->get_license_data();
?>

<div class="wrap cf7-to-wa-license">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <?php settings_errors('cf7_to_wa_license_messages'); ?>
    
    <div class="cf7-to-wa-container">
        <div class="cf7-to-wa-main">
            
            <!-- License Status Card -->
            <div class="cf7-to-wa-section">
                <h2><?php _e('License Status', 'cf7-to-whatsapp'); ?></h2>
                
                <div class="cf7-to-wa-license-status cf7-to-wa-license-status-<?php echo esc_attr($license_status['color']); ?>">
                    <div class="license-status-icon">
                        <?php if ($license_status['status'] === 'active') : ?>
                            <span class="dashicons dashicons-yes-alt"></span>
                        <?php elseif ($license_status['status'] === 'expired') : ?>
                            <span class="dashicons dashicons-clock"></span>
                        <?php else : ?>
                            <span class="dashicons dashicons-warning"></span>
                        <?php endif; ?>
                    </div>
                    <div class="license-status-info">
                        <h3><?php echo esc_html($license_status['message']); ?></h3>
                        <?php if (isset($license_status['plan'])) : ?>
                            <p><strong><?php _e('Plan:', 'cf7-to-whatsapp'); ?></strong> <?php echo esc_html($license_status['plan']); ?></p>
                        <?php endif; ?>
                        <?php if (isset($license_status['expires_at'])) : ?>
                            <p><strong><?php _e('Expires:', 'cf7-to-whatsapp'); ?></strong> <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($license_status['expires_at']))); ?></p>
                        <?php endif; ?>
                        <?php if ($license_data && isset($license_data['domain'])) : ?>
                            <p><strong><?php _e('Domain:', 'cf7-to-whatsapp'); ?></strong> <?php echo esc_html($license_data['domain']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <?php if (!$license_manager->is_licensed()) : ?>
                <!-- Activate License Form -->
                <div class="cf7-to-wa-section">
                    <h2><?php _e('Activate License', 'cf7-to-whatsapp'); ?></h2>
                    <p class="description"><?php _e('Enter your license key to activate the plugin.', 'cf7-to-whatsapp'); ?></p>
                    
                    <form method="post" action="">
                        <?php wp_nonce_field('cf7_to_wa_activate_license', 'cf7_to_wa_license_nonce'); ?>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="license_key"><?php _e('License Key', 'cf7-to-whatsapp'); ?> <span class="required">*</span></label>
                                </th>
                                <td>
                                    <input type="text" 
                                           id="license_key" 
                                           name="license_key" 
                                           value="" 
                                           class="regular-text" 
                                           placeholder="XXXX-XXXX-XXXX-XXXX"
                                           required>
                                    <p class="description">
                                        <?php _e('Enter the license key you received after purchase.', 'cf7-to-whatsapp'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                        
                        <p class="submit">
                            <button type="submit" name="cf7_to_wa_activate" class="button button-primary">
                                <?php _e('Activate License', 'cf7-to-whatsapp'); ?>
                            </button>
                        </p>
                    </form>
                </div>
                
                <!-- Purchase License -->
                <div class="cf7-to-wa-section">
                    <h2><?php _e('Don\'t Have a License?', 'cf7-to-whatsapp'); ?></h2>
                    <p><?php _e('Purchase a license to unlock all features and receive updates.', 'cf7-to-whatsapp'); ?></p>
                    <p>
                        <a href="https://cf7whatsapp.com/pricing" target="_blank" class="button button-secondary">
                            <?php _e('Purchase License', 'cf7-to-whatsapp'); ?>
                        </a>
                    </p>
                </div>
                
            <?php else : ?>
                <!-- Deactivate License Form -->
                <div class="cf7-to-wa-section">
                    <h2><?php _e('Manage License', 'cf7-to-whatsapp'); ?></h2>
                    
                    <?php if ($license_data && isset($license_data['key'])) : ?>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php _e('License Key', 'cf7-to-whatsapp'); ?></th>
                                <td>
                                    <code class="cf7-to-wa-license-key"><?php echo esc_html($license_data['key']); ?></code>
                                    <button type="button" class="button button-small cf7-to-wa-copy-license" data-license="<?php echo esc_attr($license_data['key']); ?>">
                                        <?php _e('Copy', 'cf7-to-whatsapp'); ?>
                                    </button>
                                </td>
                            </tr>
                            <?php if (isset($license_data['activated_at'])) : ?>
                                <tr>
                                    <th scope="row"><?php _e('Activated On', 'cf7-to-whatsapp'); ?></th>
                                    <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($license_data['activated_at']))); ?></td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    <?php endif; ?>
                    
                    <form method="post" action="" onsubmit="return confirm('<?php _e('Are you sure you want to deactivate this license?', 'cf7-to-whatsapp'); ?>');">
                        <?php wp_nonce_field('cf7_to_wa_deactivate_license', 'cf7_to_wa_license_nonce'); ?>
                        
                        <p class="submit">
                            <button type="submit" name="cf7_to_wa_deactivate" class="button button-secondary">
                                <?php _e('Deactivate License', 'cf7-to-whatsapp'); ?>
                            </button>
                            <button type="submit" name="cf7_to_wa_validate" class="button">
                                <?php _e('Validate License', 'cf7-to-whatsapp'); ?>
                            </button>
                        </p>
                    </form>
                    
                    <p class="description">
                        <?php _e('Deactivating the license will disable the plugin on this domain. You can reactivate it later or use it on a different domain.', 'cf7-to-whatsapp'); ?>
                    </p>
                </div>
            <?php endif; ?>
            
        </div>
        
        <!-- Sidebar -->
        <div class="cf7-to-wa-sidebar">
            <!-- License Info -->
            <div class="cf7-to-wa-widget">
                <h3><?php _e('License Information', 'cf7-to-whatsapp'); ?></h3>
                <p><?php _e('A valid license is required to use this plugin. Each license can be activated on a specific number of domains depending on your plan.', 'cf7-to-whatsapp'); ?></p>
                <ul>
                    <li><strong><?php _e('Single Site:', 'cf7-to-whatsapp'); ?></strong> 1 domain</li>
                    <li><strong><?php _e('5 Sites:', 'cf7-to-whatsapp'); ?></strong> 5 domains</li>
                    <li><strong><?php _e('Unlimited:', 'cf7-to-whatsapp'); ?></strong> Unlimited domains</li>
                </ul>
            </div>
            
            <!-- Support -->
            <div class="cf7-to-wa-widget">
                <h3><?php _e('Need Help?', 'cf7-to-whatsapp'); ?></h3>
                <p><?php _e('If you have any issues with your license, please contact our support team.', 'cf7-to-whatsapp'); ?></p>
                <p>
                    <a href="https://cf7whatsapp.com/support" target="_blank" class="button button-secondary">
                        <?php _e('Contact Support', 'cf7-to-whatsapp'); ?>
                    </a>
                </p>
            </div>
            
            <!-- Documentation -->
            <div class="cf7-to-wa-widget">
                <h3><?php _e('Documentation', 'cf7-to-whatsapp'); ?></h3>
                <ul>
                    <li><a href="https://cf7whatsapp.com/docs/installation" target="_blank"><?php _e('Installation Guide', 'cf7-to-whatsapp'); ?></a></li>
                    <li><a href="https://cf7whatsapp.com/docs/license-activation" target="_blank"><?php _e('License Activation', 'cf7-to-whatsapp'); ?></a></li>
                    <li><a href="https://cf7whatsapp.com/docs/troubleshooting" target="_blank"><?php _e('Troubleshooting', 'cf7-to-whatsapp'); ?></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Copy license key
    $('.cf7-to-wa-copy-license').on('click', function() {
        var license = $(this).data('license');
        var $temp = $('<input>');
        $('body').append($temp);
        $temp.val(license).select();
        document.execCommand('copy');
        $temp.remove();
        
        $(this).text('<?php _e('Copied!', 'cf7-to-whatsapp'); ?>');
        var $btn = $(this);
        setTimeout(function() {
            $btn.text('<?php _e('Copy', 'cf7-to-whatsapp'); ?>');
        }, 2000);
    });
});
</script>
