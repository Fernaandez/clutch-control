<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motorcycle_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('motorcycle_brands')->onDelete('cascade');
            $table->string('name');
            $table->integer('cc')->nullable();
            $table->integer('power_cv')->nullable();
            $table->integer('year_start')->nullable();
            $table->integer('year_end')->nullable();
            $table->string('type')->nullable(); // Naked, Sport, Trail, etc.
            $table->timestamps();

            $table->unique(['brand_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motorcycle_models');
    }
};
