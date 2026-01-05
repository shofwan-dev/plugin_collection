=== CF7 to WhatsApp Gateway ===
Contributors: yourname
Tags: contact form 7, whatsapp, notification, integration, mpwa
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Send Contact Form 7 submissions directly to WhatsApp via MPWA Gateway.

== Description ==

CF7 to WhatsApp Gateway seamlessly integrates Contact Form 7 with WhatsApp messaging through the MPWA (mpwa.mutekar.com) API. Automatically send form submissions to admin and optionally send confirmation messages to users via WhatsApp.

= Features =

* **Automatic WhatsApp Notifications** - Get instant WhatsApp messages when someone submits a Contact Form 7
* **Multiple Admin Numbers** - Add multiple admin numbers to receive notifications
* **Admin Notifications** - Receive all form submissions on your WhatsApp
* **User Confirmations** - Send automatic confirmation messages to users
* **Resend Failed Messages** - Resend failed messages directly from logs
* **Customizable Templates** - Create custom message templates with placeholders
* **Comprehensive Logging** - Track all sent messages with detailed logs
* **Easy Configuration** - Simple settings page for API and message configuration
* **Test Functionality** - Test your WhatsApp connection before going live
* **Statistics Dashboard** - View success rates and message statistics

= Requirements =

* Contact Form 7 plugin installed and activated
* MPWA API account (from mpwa.mutekar.com)
* API Key and Sender Number from MPWA

= How It Works =

1. Install and activate the plugin
2. Configure your MPWA API credentials in the settings
3. Set up your message templates
4. Enable admin and/or user notifications
5. Every Contact Form 7 submission will automatically send WhatsApp messages

= Message Template Placeholders =

* `{form_title}` - The title of the submitted form
* `{submission_date}` - Date and time of submission
* `{all_fields}` - All form fields formatted nicely
* `{field_name}` - Any specific field from your form (e.g., {name}, {email}, {phone})

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/cf7-to-whatsapp/` directory, or install through WordPress plugins screen
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to CF7 to WhatsApp → Settings
4. Enter your MPWA API Key and Sender Number
5. Configure your notification settings and message templates
6. Save settings and test the connection

== Frequently Asked Questions ==

= Do I need a MPWA account? =

Yes, you need an active MPWA account from mpwa.mutekar.com to use this plugin.

= Does this work with all Contact Form 7 forms? =

Yes, this plugin works with all Contact Form 7 forms on your website.

= Can I customize the WhatsApp messages? =

Yes, you can fully customize both admin and user message templates using placeholders.

= How do I send messages to users? =

Enable user notifications in settings and make sure your Contact Form 7 has a phone field. Set the field name in the plugin settings.

= Where can I view the message logs? =

Go to CF7 to WhatsApp → Logs to view all sent messages, their status, and responses.

= What phone number format should I use? =

Use international format without + sign. For example: 62888xxxx for Indonesia, 1555xxxx for USA.

= How many admin numbers can I add? =

You can add unlimited admin numbers. All admin numbers will receive notifications when a form is submitted.

= What happens if a message fails to send? =

Failed messages are logged with a "failed" status. You can resend them anytime from the Logs page by clicking the Resend button.

= Can I resend failed messages? =

Yes! Go to CF7 to WhatsApp → Logs, find the failed message, and click the Resend button in the Action column.

== Screenshots ==

1. Settings page - API Configuration
2. Settings page - Message Templates
3. Logs page with statistics
4. Test message functionality

== Changelog ==

= 1.0.0 =
* Initial release
* Admin notifications with multiple admin numbers support
* User confirmations
* Customizable message templates
* Comprehensive logging system
* Statistics dashboard
* Test message functionality
* Resend failed messages feature

== Upgrade Notice ==

= 1.0.0 =
Initial release of CF7 to WhatsApp Gateway plugin.

== Support ==

For support, please visit our support forum or contact us directly.

== Credits ==

Developed by: Mutekar Digital Solutions  
This plugin integrates with MPWA (mpwa.mutekar.com) WhatsApp Gateway API.
