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
        Schema::create('variacion_opciones_default', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tipo_default')->constrained('variacion_tipos_default')->cascadeOnDelete();
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
        Schema::dropIfExists('variacion_opciones_default');
    }
};
