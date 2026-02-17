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

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('motorcycle_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained('route_categories')->onDelete('set null');

            $table->string('title');
            $table->text('description')->nullable();
            
            // --- CANVIS AQUÍ ---
            $table->decimal('planned_distance_km', 10, 2)->nullable(); // Distància Teòrica
            $table->decimal('distance_km', 10, 2)->nullable();         // Distància Real (GPS)
            $table->integer('duration_seconds')->nullable();           // Temps Real en segons
            
            $table->longText('geo_json')->nullable(); // El camí sencer (null al principi)
            
            // Coordenades inici (per centrar el mapa)
            $table->decimal('starting_lat', 11, 8)->nullable();
            $table->decimal('starting_lng', 11, 8)->nullable();
            $table->string('location_city')->nullable();

            $table->boolean('is_public')->default(true);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->integer('likes_count')->default(0);
            $table->string('photo')->nullable(); 
            
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
