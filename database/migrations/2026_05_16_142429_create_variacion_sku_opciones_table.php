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
        Schema::create('variacion_sku_opciones', function (Blueprint $table) {
            $table->foreignId('id_sku')->constrained('variacion_skus')->cascadeOnDelete();
            $table->foreignId('id_opcion')->constrained('variacion_opciones')->cascadeOnDelete();
            $table->primary(['id_sku', 'id_opcion']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variacion_sku_opciones');
    }
};
