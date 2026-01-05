<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add Paddle fields to plans table
        Schema::table('plans', function (Blueprint $table) {
            $table->string('paddle_price_id')->nullable()->after('slug');
            $table->string('paddle_product_id')->nullable()->after('paddle_price_id');
        });

        // Add Paddle fields to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->string('paddle_transaction_id')->nullable()->after('order_number');
            $table->string('paddle_subscription_id')->nullable()->after('paddle_transaction_id');
            $table->dropColumn('stripe_session_id');
        });

        // Add Paddle customer ID to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('paddle_customer_id')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['paddle_price_id', 'paddle_product_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['paddle_transaction_id', 'paddle_subscription_id']);
            $table->string('stripe_session_id')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('paddle_customer_id');
        });
    }
};
