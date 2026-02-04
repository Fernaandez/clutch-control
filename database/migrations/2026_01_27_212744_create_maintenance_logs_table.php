<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('motorcycle_id')->constrained()->onDelete('cascade');
                        
            // A) Primer creem la columna dient que és un BigInteger i que pot ser NULL
            $table->unsignedBigInteger('maintenance_task_id')->nullable();
            
            // B) Després creem la connexió manualment
            $table->foreign('maintenance_task_id')
                  ->references('id')
                  ->on('maintenance_tasks')
                  ->onDelete('set null');

            $table->string('task_title'); // Snapshot del nom
            $table->text('location')->nullable(); 
            $table->date('date');
            $table->decimal('km_at_moment', 10, 2); 
            $table->decimal('cost', 10, 2);
            $table->string('invoice_photo')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};