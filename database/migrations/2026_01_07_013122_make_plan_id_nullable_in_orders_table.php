<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['plan_id']);
            
            // Make plan_id nullable
            $table->foreignId('plan_id')->nullable()->change();
            
            // Re-add foreign key constraint
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['plan_id']);
            
            // Make plan_id not nullable again
            $table->foreignId('plan_id')->nullable(false)->change();
            
            // Re-add foreign key constraint with cascade
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
        });
    }
};
