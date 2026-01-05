# CF7 to WhatsApp Gateway Plugin

WordPress plugin to send Contact Form 7 submissions to WhatsApp using MPWA Gateway API.

## Features

✅ **Admin Notifications** - Receive automatic WhatsApp messages when forms are submitted  
✅ **Multiple Admin Numbers** - Add multiple admin numbers to receive notifications  
✅ **User Confirmations** - Send confirmation messages to users via WhatsApp  
✅ **Message Templates** - Customize message templates with dynamic placeholders  
✅ **Comprehensive Logging** - Track all sent messages with detailed logs  
✅ **Resend Feature** - Resend failed messages directly from logs  
✅ **Statistics Dashboard** - View message delivery statistics  
✅ **Test Messaging** - Test WhatsApp connection before going live  

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- Contact Form 7 plugin installed and active
- MPWA account from [mpwa.mutekar.com](https://mpwa.mutekar.com)
- API Key and Sender Number from MPWA

## Installation

1. **Upload Plugin**
   - Upload the `cf7-to-whatsapp` folder to `/wp-content/plugins/` directory
   - Or install via WordPress admin → Plugins → Add New → Upload Plugin

2. **Activation**
   - Activate the plugin through the 'Plugins' menu in WordPress admin

3. **Configuration**
   - Go to **CF7 to WhatsApp** → **Settings**
   - Enter your **API Key** and **Sender Number** from MPWA
   - Configure notification settings
   - Customize message templates as needed
   - Click **Save Settings**

4. **Test Connection**
   - Use the **Test Message** feature in the sidebar
   - Enter a test WhatsApp number
   - Click **Send Test Message**

## Configuration

### API Configuration

- **API Key**: Your API key from MPWA account
- **Sender Number**: WhatsApp number registered in MPWA (format: 62888xxxx)

### Notification Settings

- **Admin Notification**: Enable/disable notifications to admin
- **Admin WhatsApp Numbers**: WhatsApp numbers to receive admin notifications (supports multiple numbers)
- **User Notification**: Enable/disable confirmations to users
- **User Phone Field Name**: Name of the phone field in Contact Form 7 (default: `phone`)

### Message Templates

Use the following placeholders in your message templates:

- `{form_title}` - Title of the submitted form
- `{submission_date}` - Date and time of submission
- `{all_fields}` - All form fields formatted nicely
- `{field_name}` - Specific field from the form (e.g., `{name}`, `{email}`, `{phone}`)

**Admin Template Example:**
```
New form submission received:

{all_fields}

Form: {form_title}
Submitted at: {submission_date}
```

**User Template Example:**
```
Thank you {name}!

We have received your information and will contact you soon.

Your submitted data:
{all_fields}

Best regards,
Support Team
```

## Usage

1. **Ensure Contact Form 7 is installed**

2. **Create or edit a Contact Form 7**
   - If you want to send confirmations to users, ensure there's a phone field
   - Example: `[tel* phone placeholder "WhatsApp Number"]`

3. **Plugin works automatically**
   - Every submission will send WhatsApp messages according to your configuration
   - Check logs at **CF7 to WhatsApp** → **Logs**

## Phone Number Format

Use international format **without the + sign**:

- Indonesia: `62888xxxx` (not `08888xxxx` or `+62888xxxx`)
- USA: `1555xxxx`
- Malaysia: `60123xxxx`

The plugin automatically cleans numbers from spaces, dashes, etc.

## Logs & Monitoring

Access **CF7 to WhatsApp** → **Logs** to:

- View all sent messages
- Filter by status (success/failed)
- View delivery statistics
- See detailed API responses
- **Resend failed messages**

**Statistics displayed:**
- Total Messages
- Successful
- Failed
- Success Rate
- Today's Messages

## Resend Feature

If a message fails due to gateway issues, you can resend it:

1. Go to **CF7 to WhatsApp** → **Logs**
2. Find the failed message
3. Click the **Resend** button in the Action column
4. Confirm the dialog
5. The message will be resent and the log updated

## Multiple Admin Numbers

Add multiple admin numbers to receive notifications:

1. Go to **CF7 to WhatsApp** → **Settings**
2. Scroll to **Admin WhatsApp Numbers**
3. Enter the first admin number
4. Click **+ Add Admin Number** to add more
5. Enter additional admin numbers
6. Click **Remove** to delete a number (if more than 1)
7. Click **Save Settings**

All admin numbers will receive notifications when a form is submitted.

## Troubleshooting

### Messages not being sent

1. **Check API Configuration**
   - Ensure API Key and Sender Number are correct
   - Test connection using Test Message feature

2. **Check Phone Numbers**
   - Ensure number format is correct (62888xxxx)
   - Number must be registered on WhatsApp

3. **Check Logs**
   - View error messages in the Logs page
   - Check API response details

### Users not receiving confirmations

1. **Ensure User Notification is enabled**
2. **Check phone field name**
   - Field name in CF7 must match "User Phone Field Name" setting
   - Default: `phone`
3. **Ensure users enter numbers correctly**

## API Documentation

This plugin uses the MPWA API. Full documentation:
- [MPWA API Docs](https://mpwa.mutekar.com)

### Endpoints used:

1. **Send Text Message**
   - Endpoint: `https://mpwa.mutekar.com/send-message`
   - Method: POST
   - For sending text messages

2. **Send Media Message** (for future development)
   - Endpoint: `https://mpwa.mutekar.com/send-media`
   - Method: POST
   - For sending images, videos, documents

3. **Check Number** (for future development)
   - Endpoint: `https://mpwa.mutekar.com/check-number`
   - Method: POST
   - For WhatsApp number validation

## File Structure

```
cf7-to-whatsapp/
├── cf7-to-whatsapp.php          # Main plugin file
├── readme.txt                    # WordPress plugin readme
├── README.md                     # Documentation (Indonesian)
├── README-EN.md                  # Documentation (English)
├── includes/
│   ├── class-cf7-to-wa-admin.php    # Admin settings class
│   ├── class-cf7-to-wa-api.php      # WhatsApp API integration
│   ├── class-cf7-to-wa-handler.php  # CF7 submission handler
│   └── class-cf7-to-wa-logger.php   # Logging system
├── templates/
│   ├── admin-settings.php       # Settings page template
│   └── admin-logs.php          # Logs page template
└── assets/
    ├── css/
    │   └── admin.css           # Admin styles
    └── js/
        └── admin.js            # Admin scripts
```

## Changelog

### Version 1.0.0
- Initial release
- Admin notifications
- Multiple admin numbers support
- User confirmations
- Customizable message templates
- Comprehensive logging system
- Statistics dashboard
- Test message functionality
- Resend failed messages feature

## Support

For help and support:
- Email: support@mutekar.com
- Website: https://mutekar.com

## License

GPL v2 or later

## Credits

Developed by: Mutekar Digital Solutions  
WhatsApp Gateway: [MPWA](https://mpwa.mutekar.com)

## Frequently Asked Questions

### Can I use this with any Contact Form 7 form?
Yes, the plugin works with all Contact Form 7 forms on your website.

### How many admin numbers can I add?
There's no limit. You can add as many admin numbers as needed.

### What happens if a message fails?
The message is logged with a "failed" status. You can resend it anytime from the Logs page using the Resend button.

### Can I customize the message format?
Yes, you can fully customize both admin and user message templates using placeholders.

### Does this work with file uploads?
Currently, the plugin sends text messages only. Media support is planned for future versions.

### Is the plugin compatible with GDPR?
Yes, the plugin only sends data that users submit through forms. You should inform users in your privacy policy that their data may be sent via WhatsApp.

### Can I disable logging?
Yes, you can disable logging in the plugin settings under "Other Settings".

### How do I export logs?
Currently, logs can be viewed in the admin panel. Export functionality is planned for future versions.

## Advanced Usage

### Custom Message Formatting

You can use any field from your Contact Form 7 in the message template:

```
New inquiry from {name}

Email: {email}
Phone: {phone}
Subject: {subject}
Message: {message}

Form: {form_title}
Date: {submission_date}
```

### Conditional Notifications

While the plugin doesn't have built-in conditional logic, you can use multiple Contact Form 7 forms with different configurations to achieve conditional notifications.

### Integration with Other Plugins

The plugin works independently but can coexist with other Contact Form 7 extensions like:
- CF7 Database Addon
- Flamingo
- CF7 Conditional Fields

## Performance

- Lightweight plugin with minimal impact on site performance
- Asynchronous message sending doesn't block form submission
- Efficient database queries with proper indexing
- Optional logging to reduce database usage

## Security

- Nonce verification for all form submissions
- Capability checks for admin functions
- Data sanitization and validation
- SQL injection prevention
- XSS protection

## Roadmap

Future features planned:
- [ ] Media file support (images, documents)
- [ ] Bulk resend functionality
- [ ] Export logs to CSV
- [ ] Per-form configuration
- [ ] Conditional sending based on field values
- [ ] WhatsApp number validation before sending
- [ ] Scheduled message cleanup
- [ ] Integration with WhatsApp Business API
- [ ] Multi-gateway support
