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
        Schema::create('variacion_opciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tipo')->constrained('variacion_tipos')->cascadeOnDelete();
            $table->foreignId('id_opcion_default')->nullable()->constrained('variacion_opciones_default')->nullOnDelete();
            $table->string('nombre');
            $table->string('imagen')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variacion_opciones');
    }
};
