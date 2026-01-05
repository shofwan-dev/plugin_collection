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
        Schema::table('licenses', function (Blueprint $table) {
            $table->enum('purchase_source', ['paddle', 'envato'])->default('paddle')->after('license_key');
            $table->string('envato_purchase_code')->nullable()->unique()->after('purchase_source');
            $table->string('envato_buyer_username')->nullable()->after('envato_purchase_code');
            $table->timestamp('envato_verified_at')->nullable()->after('envato_buyer_username');
            $table->json('envato_purchase_data')->nullable()->after('envato_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropColumn([
                'purchase_source',
                'envato_purchase_code',
                'envato_buyer_username',
                'envato_verified_at',
                'envato_purchase_data'
            ]);
        });
    }
};
