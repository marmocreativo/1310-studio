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
        Schema::create('pedido_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pedido')->constrained('pedidos')->cascadeOnDelete();
            $table->foreignId('id_producto')->nullable()->constrained('productos')->nullOnDelete();
            $table->foreignId('id_sku')->nullable()->constrained('variacion_skus')->nullOnDelete();
            $table->string('nombre_snapshot');
            $table->decimal('precio_snapshot', 10, 2);
            $table->unsignedInteger('cantidad')->default(1);
            $table->json('opciones_snapshot')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_items');
    }
};
