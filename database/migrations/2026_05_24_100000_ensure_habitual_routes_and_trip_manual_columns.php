<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('habitual_routes')) {
            Schema::create('habitual_routes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('route_id')->constrained()->cascadeOnDelete();
                $table->foreignId('motorcycle_id')->constrained()->cascadeOnDelete();
                $table->string('label', 120)->nullable();
                $table->boolean('round_trip')->default(false);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('trips', 'manual_entry')) {
            Schema::table('trips', function (Blueprint $table) {
                $table->boolean('manual_entry')->default(false);
            });
        }

        if (! Schema::hasColumn('trips', 'notes')) {
            Schema::table('trips', function (Blueprint $table) {
                $table->string('notes', 500)->nullable();
            });
        }
    }

    public function down(): void
    {
        // No revertim: migració de seguretat per producció
    }
};
