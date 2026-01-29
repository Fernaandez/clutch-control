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
    Schema::create('routes', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')->constrained()->onDelete('cascade'); //si s'esborra l'usuari, s'esborren les seves rutes
        $table->foreignId('motorcycle_id')->nullable()->constrained()->onDelete('set null'); //si s'esborra la moto, es posa a null
        $table->foreignId('category_id')->nullable()->constrained('route_categories')->onDelete('set null'); //si s'esborra la categoria, es posa a null

        $table->string('title');
        $table->text('description')->nullable();
        
        $table->decimal('distance_km', 10, 2);
        $table->time('estimated_time')->nullable(); // Format HH:MM:SS
        
        $table->longText('geo_json'); // El camí sencer
        
        // Coordenades inici
        $table->decimal('starting_lat', 11, 8);
        $table->decimal('starting_lng', 11, 8);
        $table->string('location_city')->nullable();

        $table->boolean('is_public')->default(true);
        $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
        $table->integer('likes_count')->default(0);
        $table->string('photo')->nullable(); // Foto portada ruta
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
