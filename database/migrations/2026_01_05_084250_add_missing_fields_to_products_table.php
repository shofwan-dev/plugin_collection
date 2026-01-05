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
            if (!Schema::hasColumn('products', 'version')) {
                $table->string('version')->nullable()->after('description');
            }
            if (!Schema::hasColumn('products', 'type')) {
                $table->string('type')->default('plugin')->after('version');
            }
            if (!Schema::hasColumn('products', 'file_path')) {
                $table->string('file_path')->nullable()->after('type');
            }
            if (!Schema::hasColumn('products', 'file_name')) {
                $table->string('file_name')->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('products', 'file_size')) {
                $table->bigInteger('file_size')->nullable()->after('file_name');
            }
            if (!Schema::hasColumn('products', 'changelog')) {
                $table->text('changelog')->nullable()->after('file_size');
            }
            if (!Schema::hasColumn('products', 'requirements')) {
                $table->text('requirements')->nullable()->after('changelog');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['version', 'type', 'file_path', 'file_name', 'file_size', 'changelog', 'requirements']);
        });
    }
};
