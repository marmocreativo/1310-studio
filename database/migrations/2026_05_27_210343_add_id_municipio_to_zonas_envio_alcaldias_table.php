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
        Schema::table('zonas_envio_alcaldias', function (Blueprint $table) {
            $table->foreignId('id_municipio')
                ->nullable()
                ->after('nombre')
                ->constrained('municipios')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('zonas_envio_alcaldias', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Municipio::class, 'id_municipio');
            $table->dropColumn('id_municipio');
        });
    }
};
