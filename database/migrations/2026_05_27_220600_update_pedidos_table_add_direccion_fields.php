<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // Agregar campos nuevos
            $table->string('calle')->nullable()->after('direccion');
            $table->string('numero_ext', 20)->nullable()->after('calle');
            $table->string('numero_int', 20)->nullable()->after('numero_ext');
            $table->foreignId('id_estado')->nullable()->after('municipio')
                ->constrained('estados')->nullOnDelete();
            $table->foreignId('id_municipio')->nullable()->after('id_estado')
                ->constrained('municipios')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Estado::class, 'id_estado');
            $table->dropForeignIdFor(\App\Models\Municipio::class, 'id_municipio');
            $table->dropColumn(['calle', 'numero_ext', 'numero_int', 'id_estado', 'id_municipio']);
        });
    }
};
