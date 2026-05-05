<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos_categorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_producto')
                  ->constrained('productos')
                  ->cascadeOnDelete();
            $table->foreignId('id_categoria')
                  ->constrained('categorias')
                  ->cascadeOnDelete();

            $table->unique(['id_producto', 'id_categoria']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos_categorias');
    }
};