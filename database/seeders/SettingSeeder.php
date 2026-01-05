<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'site_name', 'value' => 'CF7 to WhatsApp Gateway', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Professional WordPress Plugin License Management', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_logo', 'value' => null, 'type' => 'string', 'group' => 'general'],
            ['key' => 'contact_email', 'value' => 'support@cf7whatsapp.com', 'type' => 'string', 'group' => 'general'],
            ['key' => 'contact_phone', 'value' => null, 'type' => 'string', 'group' => 'general'],
            
            // WhatsApp Settings
            ['key' => 'whatsapp_api_url', 'value' => null, 'type' => 'string', 'group' => 'whatsapp'],
            ['key' => 'whatsapp_api_key', 'value' => null, 'type' => 'string', 'group' => 'whatsapp'],
            ['key' => 'whatsapp_sender', 'value' => null, 'type' => 'string', 'group' => 'whatsapp'],
            ['key' => 'whatsapp_admin_number', 'value' => null, 'type' => 'string', 'group' => 'whatsapp'],
            ['key' => 'whatsapp_enabled', 'value' => 'false', 'type' => 'boolean', 'group' => 'whatsapp'],
            
            // Email Settings
            ['key' => 'email_from_address', 'value' => 'noreply@cf7whatsapp.com', 'type' => 'string', 'group' => 'email'],
            ['key' => 'email_from_name', 'value' => 'CF7 WhatsApp Gateway', 'type' => 'string', 'group' => 'email'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
