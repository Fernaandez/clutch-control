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
        Schema::create('event_routes', function (Blueprint $table) {
        $table->foreignId('event_id')->constrained()->onDelete('cascade');
        $table->foreignId('route_id')->constrained()->onDelete('cascade');
        $table->integer('day_order')->default(1);
        $table->primary(['event_id', 'route_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_routes');
    }
};
