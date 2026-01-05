<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Plan;
use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user only if not exists
        if (!User::where('email', 'admin@cf7whatsapp.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@cf7whatsapp.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'is_admin' => true,
            ]);
        }

        // Optionally create test customer user only if not exists (only in development)
        if (app()->environment('local') && !User::where('email', 'customer@example.com')->exists()) {
            User::create([
                'name' => 'Test Customer',
                'email' => 'customer@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'is_admin' => false,
            ]);
        }

        // Seed plans only if not exists
        if (Plan::count() === 0) {
            $this->call(PlanSeeder::class);
        }

        // Seed settings only if not exists
        if (Setting::count() === 0) {
            $this->call(SettingSeeder::class);
        }
    }
}
