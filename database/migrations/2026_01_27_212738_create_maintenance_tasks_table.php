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
        Schema::create('maintenance_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motorcycle_id')->constrained()->onDelete('cascade'); //si s'esborra la moto, s'esborren les seves tasques
            $table->string('type'); // 'maintenance' o 'repair'
            $table->string('title');
            $table->text('location')->nullable();
            $table->integer('frequency_km')->nullable(); //interval en quilòmetres per a realitzar la tasca
            $table->integer('last_km_done')->default(0); //quilòmetres en què es va realitzar l'última vegada
            $table->date('last_date_done')->nullable(); //data en què es va realitzar l'última vegada
            $table->boolean('is_recurring')->default(true); //indica si la tasca es repeteix
            $table->string('affiliated_link')->nullable(); // enllaç a recanvis o eines necessàries
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_tasks');
    }
};
