<?php
/**
 * Admin Settings Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap cf7-to-wa-admin">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <?php settings_errors('cf7_to_wa_messages'); ?>
    
    <div class="cf7-to-wa-container">
        <div class="cf7-to-wa-main">
            <form method="post" action="options.php">
                <?php
                settings_fields('cf7_to_wa_settings');
                ?>
                
                <!-- API Configuration -->
                <div class="cf7-to-wa-section">
                    <h2><?php _e('API Configuration', 'cf7-to-whatsapp'); ?></h2>
                    <p class="description"><?php _e('Configure your WhatsApp Gateway API credentials.', 'cf7-to-whatsapp'); ?></p>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="cf7_to_wa_api_key"><?php _e('API Key', 'cf7-to-whatsapp'); ?> <span class="required">*</span></label>
                            </th>
                            <td>
                                <input type="text" 
                                       id="cf7_to_wa_api_key" 
                                       name="cf7_to_wa_api_key" 
                                       value="<?php echo esc_attr(get_option('cf7_to_wa_api_key')); ?>" 
                                       class="regular-text" 
                                       required>
                                <p class="description"><?php _e('Your MPWA API key from mpwa.mutekar.com', 'cf7-to-whatsapp'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="cf7_to_wa_sender_number"><?php _e('Sender Number', 'cf7-to-whatsapp'); ?> <span class="required">*</span></label>
                            </th>
                            <td>
                                <input type="text" 
                                       id="cf7_to_wa_sender_number" 
                                       name="cf7_to_wa_sender_number" 
                                       value="<?php echo esc_attr(get_option('cf7_to_wa_sender_number')); ?>" 
                                       class="regular-text" 
                                       placeholder="62888xxxx"
                                       required>
                                <p class="description"><?php _e('Your WhatsApp number (without + sign, e.g., 62888xxxx)', 'cf7-to-whatsapp'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Notification Settings -->
                <div class="cf7-to-wa-section">
                    <h2><?php _e('Notification Settings', 'cf7-to-whatsapp'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="cf7_to_wa_enable_admin_notification"><?php _e('Admin Notification', 'cf7-to-whatsapp'); ?></label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" 
                                           id="cf7_to_wa_enable_admin_notification" 
                                           name="cf7_to_wa_enable_admin_notification" 
                                           value="1" 
                                           <?php checked(get_option('cf7_to_wa_enable_admin_notification', '1'), '1'); ?>>
                                    <?php _e('Send notification to admin when form is submitted', 'cf7-to-whatsapp'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="cf7_to_wa_admin_numbers"><?php _e('Admin WhatsApp Numbers', 'cf7-to-whatsapp'); ?></label>
                            </th>
                            <td>
                                <div id="cf7-to-wa-admin-numbers-wrapper">
                                    <?php
                                    $admin_numbers = get_option('cf7_to_wa_admin_number', '');
                                    
                                    // Convert old single number to array format
                                    if (!empty($admin_numbers) && !is_array($admin_numbers)) {
                                        $admin_numbers = array($admin_numbers);
                                    } elseif (empty($admin_numbers)) {
                                        $admin_numbers = array('');
                                    }
                                    
                                    foreach ($admin_numbers as $index => $number) :
                                    ?>
                                        <div class="cf7-to-wa-admin-number-row" style="margin-bottom: 10px;">
                                            <input type="text" 
                                                   name="cf7_to_wa_admin_number[]" 
                                                   value="<?php echo esc_attr($number); ?>" 
                                                   class="regular-text" 
                                                   placeholder="62888xxxx">
                                            <button type="button" class="button cf7-to-wa-remove-number" style="<?php echo $index === 0 ? 'display:none;' : ''; ?>">
                                                <?php _e('Remove', 'cf7-to-whatsapp'); ?>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" id="cf7-to-wa-add-number" class="button" style="margin-top: 5px;">
                                    <?php _e('+ Add Admin Number', 'cf7-to-whatsapp'); ?>
                                </button>
                                <p class="description"><?php _e('WhatsApp numbers to receive admin notifications. You can add multiple numbers.', 'cf7-to-whatsapp'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="cf7_to_wa_enable_user_notification"><?php _e('User Notification', 'cf7-to-whatsapp'); ?></label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" 
                                           id="cf7_to_wa_enable_user_notification" 
                                           name="cf7_to_wa_enable_user_notification" 
                                           value="1" 
                                           <?php checked(get_option('cf7_to_wa_enable_user_notification', '0'), '1'); ?>>
                                    <?php _e('Send confirmation to user via WhatsApp', 'cf7-to-whatsapp'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="cf7_to_wa_user_phone_field"><?php _e('User Phone Field Name', 'cf7-to-whatsapp'); ?></label>
                            </th>
                            <td>
                                <input type="text" 
                                       id="cf7_to_wa_user_phone_field" 
                                       name="cf7_to_wa_user_phone_field" 
                                       value="<?php echo esc_attr(get_option('cf7_to_wa_user_phone_field', 'phone')); ?>" 
                                       class="regular-text" 
                                       placeholder="phone">
                                <p class="description"><?php _e('Name of the phone field in your Contact Form 7 (e.g., phone, whatsapp, mobile)', 'cf7-to-whatsapp'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Message Templates -->
                <div class="cf7-to-wa-section">
                    <h2><?php _e('Message Templates', 'cf7-to-whatsapp'); ?></h2>
                    <p class="description">
                        <?php _e('Available placeholders:', 'cf7-to-whatsapp'); ?>
                        <code>{form_title}</code>, 
                        <code>{submission_date}</code>, 
                        <code>{all_fields}</code>, 
                        <code>{field_name}</code>
                    </p>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="cf7_to_wa_admin_message_template"><?php _e('Admin Message Template', 'cf7-to-whatsapp'); ?></label>
                            </th>
                            <td>
                                <textarea id="cf7_to_wa_admin_message_template" 
                                          name="cf7_to_wa_admin_message_template" 
                                          rows="8" 
                                          class="large-text code"><?php echo esc_textarea(get_option('cf7_to_wa_admin_message_template', "New form submission received:\n\n{all_fields}\n\nForm: {form_title}\nSubmitted at: {submission_date}")); ?></textarea>
                                <p class="description"><?php _e('Message template for admin notifications', 'cf7-to-whatsapp'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="cf7_to_wa_user_message_template"><?php _e('User Message Template', 'cf7-to-whatsapp'); ?></label>
                            </th>
                            <td>
                                <textarea id="cf7_to_wa_user_message_template" 
                                          name="cf7_to_wa_user_message_template" 
                                          rows="8" 
                                          class="large-text code"><?php echo esc_textarea(get_option('cf7_to_wa_user_message_template', "Thank you for your submission!\n\nWe have received your information and will contact you soon.")); ?></textarea>
                                <p class="description"><?php _e('Message template for user confirmations', 'cf7-to-whatsapp'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="cf7_to_wa_footer_text"><?php _e('Footer Text', 'cf7-to-whatsapp'); ?></label>
                            </th>
                            <td>
                                <input type="text" 
                                       id="cf7_to_wa_footer_text" 
                                       name="cf7_to_wa_footer_text" 
                                       value="<?php echo esc_attr(get_option('cf7_to_wa_footer_text', 'Sent via CF7 to WhatsApp')); ?>" 
                                       class="regular-text">
                                <p class="description"><?php _e('Footer text displayed under messages (optional)', 'cf7-to-whatsapp'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Other Settings -->
                <div class="cf7-to-wa-section">
                    <h2><?php _e('Other Settings', 'cf7-to-whatsapp'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="cf7_to_wa_enable_logging"><?php _e('Enable Logging', 'cf7-to-whatsapp'); ?></label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" 
                                           id="cf7_to_wa_enable_logging" 
                                           name="cf7_to_wa_enable_logging" 
                                           value="1" 
                                           <?php checked(get_option('cf7_to_wa_enable_logging', '1'), '1'); ?>>
                                    <?php _e('Log all WhatsApp message attempts', 'cf7-to-whatsapp'); ?>
                                </label>
                                <p class="description"><?php _e('Keep track of all messages sent via WhatsApp', 'cf7-to-whatsapp'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <?php submit_button(__('Save Settings', 'cf7-to-whatsapp')); ?>
            </form>
        </div>
        
        <!-- Sidebar -->
        <div class="cf7-to-wa-sidebar">
            <!-- Test Message -->
            <div class="cf7-to-wa-widget">
                <h3><?php _e('Test Message', 'cf7-to-whatsapp'); ?></h3>
                <form method="post" action="">
                    <?php wp_nonce_field('cf7_to_wa_test', 'cf7_to_wa_test_nonce'); ?>
                    <p>
                        <label for="test_number"><?php _e('Test Number', 'cf7-to-whatsapp'); ?></label>
                        <input type="text" 
                               id="test_number" 
                               name="test_number" 
                               class="widefat" 
                               placeholder="62888xxxx" 
                               required>
                    </p>
                    <p>
                        <button type="submit" name="cf7_to_wa_test_message" class="button button-secondary">
                            <?php _e('Send Test Message', 'cf7-to-whatsapp'); ?>
                        </button>
                    </p>
                </form>
            </div>
            
            <!-- Documentation -->
            <div class="cf7-to-wa-widget">
                <h3><?php _e('Documentation', 'cf7-to-whatsapp'); ?></h3>
                <ul>
                    <li><a href="https://mpwa.mutekar.com" target="_blank"><?php _e('MPWA API Documentation', 'cf7-to-whatsapp'); ?></a></li>
                    <li><a href="#" target="_blank"><?php _e('Plugin Documentation', 'cf7-to-whatsapp'); ?></a></li>
                </ul>
            </div>
            
            <!-- Support -->
            <div class="cf7-to-wa-widget">
                <h3><?php _e('Support', 'cf7-to-whatsapp'); ?></h3>
                <p><?php _e('Need help? Contact us for support.', 'cf7-to-whatsapp'); ?></p>
            </div>
        </div>
    </div>
</div>
