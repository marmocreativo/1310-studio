<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos_flores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_producto')
                  ->constrained('productos')
                  ->cascadeOnDelete();
            $table->foreignId('id_flor')
                  ->constrained('directorio_floral')
                  ->cascadeOnDelete();

            $table->unique(['id_producto', 'id_flor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos_flores');
    }
};