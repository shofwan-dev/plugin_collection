<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('homepage_settings');
        
        Schema::create('homepage_settings', function (Blueprint $table) {
            $table->id();
            
            // Hero Section
            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_cta_text', 100)->nullable();
            $table->string('hero_cta_link')->nullable();
            $table->string('hero_secondary_cta_text', 100)->nullable();
            $table->string('hero_secondary_cta_link')->nullable();
            
            // Stats Section
            $table->string('stats_products_label')->nullable();
            $table->string('stats_downloads_label')->nullable();
            $table->string('stats_uptime_label')->nullable();
            $table->string('stats_support_label')->nullable();
            
            // Features Section
            $table->string('features_title')->nullable();
            $table->text('features_subtitle')->nullable();
            $table->json('features_items')->nullable(); // Extended JSON for icons/colors/desc
            
            // Products Section
            $table->string('products_title')->nullable();
            $table->text('products_subtitle')->nullable();
            
            // CTA Section
            $table->string('cta_title')->nullable();
            $table->text('cta_subtitle')->nullable();
            $table->string('cta_primary_text', 100)->nullable();
            $table->string('cta_primary_link')->nullable();
            $table->string('cta_secondary_text', 100)->nullable();
            $table->string('cta_secondary_link')->nullable();
            
            // Global Settings
            $table->json('featured_product_ids')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_settings');
    }
};
