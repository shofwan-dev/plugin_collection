jQuery(document).ready(function ($) {
    'use strict';

    // Initialize remove buttons visibility on page load
    updateRemoveButtons();

    // View full message modal
    $('.cf7-to-wa-view-full').on('click', function () {
        var message = $(this).data('message');
        $('#cf7-to-wa-message-full').text(message);
        $('#cf7-to-wa-message-modal').fadeIn();
    });

    // Close modal
    $('.cf7-to-wa-modal-close').on('click', function () {
        $('#cf7-to-wa-message-modal').fadeOut();
    });

    // Close modal when clicking outside
    $(window).on('click', function (event) {
        if ($(event.target).is('#cf7-to-wa-message-modal')) {
            $('#cf7-to-wa-message-modal').fadeOut();
        }
    });

    // Close modal on ESC key
    $(document).on('keydown', function (event) {
        if (event.key === 'Escape') {
            $('#cf7-to-wa-message-modal').fadeOut();
        }
    });

    // Confirm before clearing logs
    $('input[name="cf7_to_wa_clear_logs"]').on('click', function (e) {
        if (!confirm('Are you sure you want to clear all logs? This action cannot be undone.')) {
            e.preventDefault();
            return false;
        }
    });

    // Resend message
    $(document).on('click', '.cf7-to-wa-resend', function () {
        var $button = $(this);
        var logId = $button.data('log-id');
        var recipient = $button.data('recipient');

        if (!confirm('Resend message to ' + recipient + '?')) {
            return;
        }

        $button.prop('disabled', true).html('<span class="dashicons dashicons-update-alt" style="margin-top: 3px; animation: spin 1s linear infinite;"></span> Sending...');

        $.ajax({
            url: cf7ToWaAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cf7_to_wa_resend_message',
                nonce: cf7ToWaAdmin.nonce,
                log_id: logId
            },
            success: function (response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                    $button.prop('disabled', false).html('<span class="dashicons dashicons-update-alt" style="margin-top: 3px;"></span> Resend');
                }
            },
            error: function () {
                alert('An error occurred. Please try again.');
                $button.prop('disabled', false).html('<span class="dashicons dashicons-update-alt" style="margin-top: 3px;"></span> Resend');
            }
        });
    });

    // Add admin number field
    $(document).on('click', '#cf7-to-wa-add-number', function (e) {
        e.preventDefault();
        console.log('Add number button clicked');

        var $wrapper = $('#cf7-to-wa-admin-numbers-wrapper');
        var $newRow = $('<div class="cf7-to-wa-admin-number-row" style="margin-bottom: 10px;">' +
            '<input type="text" name="cf7_to_wa_admin_number[]" value="" class="regular-text" placeholder="62888xxxx"> ' +
            '<button type="button" class="button cf7-to-wa-remove-number">Remove</button>' +
            '</div>');
        $wrapper.append($newRow);
        updateRemoveButtons();
        console.log('New field added');
    });

    // Remove admin number field
    $(document).on('click', '.cf7-to-wa-remove-number', function (e) {
        e.preventDefault();
        $(this).closest('.cf7-to-wa-admin-number-row').remove();
        updateRemoveButtons();
    });

    // Update remove buttons visibility
    function updateRemoveButtons() {
        var $rows = $('.cf7-to-wa-admin-number-row');
        console.log('Total rows: ' + $rows.length);
        if ($rows.length === 1) {
            $rows.find('.cf7-to-wa-remove-number').hide();
        } else {
            $rows.find('.cf7-to-wa-remove-number').show();
        }
    }

    // Auto-hide success messages after 5 seconds
    setTimeout(function () {
        $('.notice.is-dismissible').fadeOut();
    }, 5000);
});
