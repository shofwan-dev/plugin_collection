<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Benefits/Features (JSON array)
            $table->json('benefits')->nullable()->after('description');
            
            // Testimonials (JSON array)
            $table->json('testimonials')->nullable()->after('benefits');
            
            // Meta tags for SEO
            $table->string('meta_title')->nullable()->after('testimonials');
            $table->text('meta_description')->nullable()->after('meta_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['benefits', 'testimonials', 'meta_title', 'meta_description']);
        });
    }
};
