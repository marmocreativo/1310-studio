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
        Schema::create('variacion_skus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_producto')->constrained('productos')->cascadeOnDelete();
            $table->decimal('precio_lista', 10, 2)->nullable();
            $table->decimal('precio_venta', 10, 2);
            $table->boolean('estado')->default(true);
            $table->string('imagen')->nullable();
            $table->string('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variacion_skus');
    }
};
