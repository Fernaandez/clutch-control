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
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motorcycle_id')->constrained()->onDelete('cascade'); //si s'esborra la moto, s'esborren els seus registres
            $table->foreignId('maintenance_task_id')->constrained()->onDelete('cascade'); //si s'esborra la tasca, s'esborren els seus registres
            $table->text('description');
            $table->date('date');
            $table->decimal('km_at_moment', 10, 2);
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('invoice_photo')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};
