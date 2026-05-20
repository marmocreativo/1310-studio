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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('id_usuario')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('id_carrito')->nullable()->constrained('carritos')->nullOnDelete();

            // Datos del cliente
            $table->string('nombre');
            $table->string('email');
            $table->string('telefono');

            // Entrega
            $table->enum('tipo_entrega', ['envio', 'tienda'])->default('envio');
            $table->foreignId('id_zona')->nullable()->constrained('zonas_envio')->nullOnDelete();
            $table->string('direccion')->nullable();
            $table->string('colonia')->nullable();
            $table->string('municipio')->nullable();
            $table->string('cp', 10)->nullable();
            $table->text('referencias')->nullable();
            $table->date('fecha_entrega')->nullable();
            $table->enum('bloque_entrega', ['manana', 'tarde', 'noche'])->nullable();

            // Destinatario (puede ser diferente al comprador)
            $table->string('destinatario_nombre')->nullable();
            $table->string('destinatario_telefono')->nullable();
            $table->text('mensaje_tarjeta')->nullable();

            // Totales
            $table->decimal('subtotal', 10, 2);
            $table->decimal('costo_envio', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Estado
            $table->enum('estado', [
                'pendiente',
                'pagado',
                'preparando',
                'enviado',
                'entregado',
                'cancelado',
            ])->default('pendiente');

            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
