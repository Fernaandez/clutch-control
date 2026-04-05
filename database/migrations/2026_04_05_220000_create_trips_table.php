<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('motorcycle_id')->nullable()->constrained()->onDelete('set null');
            // Vinculat a una Ruta si el recorregut s'ha iniciat des de Show.vue
            $table->foreignId('route_id')->nullable()->constrained()->onDelete('set null');

            $table->decimal('distance_km', 10, 2)->nullable();
            $table->integer('duration_seconds')->nullable();

            // Punt d'inici (per centrar el mapa)
            $table->decimal('starting_lat', 11, 8)->nullable();
            $table->decimal('starting_lng', 11, 8)->nullable();

            // Tots els punts GPS crus — mai editables
            $table->json('waypoints');

            // Quan va sortir realment (no quan va sincronitzar)
            $table->timestamp('started_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
