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
        Schema::table('routes', function (Blueprint $table) {
            $table->string('share_token', 64)->nullable()->unique()->after('is_recorded');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->string('share_token', 64)->nullable()->unique()->after('is_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropColumn('share_token');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('share_token');
        });
    }
};
