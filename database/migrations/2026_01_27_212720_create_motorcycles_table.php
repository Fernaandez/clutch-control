<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motorcycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->string('brand');
            $table->string('model');
            $table->string('plate')->unique();
            $table->integer('year');
            $table->decimal('current_km', 10, 2)->default(0);
            $table->string('photo')->nullable();
            
            // --- NOUS CAMPS TÈCNICS (OPCIONALS) ---
            $table->integer('cc')->nullable(); // Cilindrada
            $table->integer('power_cv')->nullable(); // Cavalls de potència
            $table->string('license_type')->nullable(); // AM, A1, A2, A
            $table->string('type')->nullable(); // Naked, Sport, Trail, etc.
            $table->text('extras')->nullable(); // Extres instal·lats (escapament, cúpula...)
            
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('last_motorcycle_id')
                  ->references('id')
                  ->on('motorcycles')
                  ->nullOnDelete(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motorcycles');
    }
};