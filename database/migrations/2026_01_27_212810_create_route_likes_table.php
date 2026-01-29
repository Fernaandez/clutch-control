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
        Schema::create('route_likes', function (Blueprint $table) {
            $table->foreignId('route_id')->constrained()->onDelete('cascade'); //si s'esborra la ruta, s'esborren els seus likes
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); //si s'esborra l'usuari, s'esborren els seus likes
            $table->primary(['route_id', 'user_id']); //clau primària composta per evitar duplicats
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_likes');
    }
};
