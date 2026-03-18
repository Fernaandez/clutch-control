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
        Schema::table('sale_listings', function (Blueprint $table) {
            $table->string('state')->default('actiu');
        });

        // Migrate existing data (if any)
        DB::statement("UPDATE sale_listings SET state = 'venuda' WHERE is_sold = 1");
        DB::statement("UPDATE sale_listings SET state = 'actiu' WHERE is_active = 1 AND is_sold = 0");

        Schema::table('sale_listings', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'is_sold']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_listings', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
            $table->boolean('is_sold')->default(false);
            $table->dropColumn('state');
        });
    }
};
