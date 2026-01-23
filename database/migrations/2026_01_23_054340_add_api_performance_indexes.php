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
        // Add index for product active status filtering
        Schema::table('products', function (Blueprint $table) {
            $table->index(['slug', 'is_active'], 'products_slug_is_active_index');
        });

        // Add composite index for Nalda CSV upload queries (license_id + domain + created_at for ordering)
        Schema::table('nalda_csv_uploads', function (Blueprint $table) {
            $table->index(['license_id', 'domain', 'created_at'], 'nalda_csv_uploads_license_domain_created_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_slug_is_active_index');
        });

        Schema::table('nalda_csv_uploads', function (Blueprint $table) {
            $table->dropIndex('nalda_csv_uploads_license_domain_created_index');
        });
    }
};
