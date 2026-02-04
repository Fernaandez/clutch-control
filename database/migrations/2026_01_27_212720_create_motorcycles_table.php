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
        Schema::create('motorcycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); //si esborres l'usuari, s'esborren les seves motos
            $table->string('brand');
            $table->string('model');
            $table->string('plate')->unique();
            $table->integer('year');
            $table->decimal('current_km', 10, 2)->default(0);
            $table->string('photo')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('last_motorcycle_id')
                  ->references('id')
                  ->on('motorcycles')
                  ->nullOnDelete(); // Si esborrem la moto, l'usuari deixa de tenir preferida
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorcycles');
    }
};
