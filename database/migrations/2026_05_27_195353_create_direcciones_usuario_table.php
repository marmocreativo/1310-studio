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
        Schema::create('direcciones_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('users')->cascadeOnDelete();
            $table->string('alias')->default('Casa'); // Casa, Trabajo, etc.
            $table->string('nombre_contacto');
            $table->string('telefono', 20);
            $table->string('calle');
            $table->string('numero_ext', 20);
            $table->string('numero_int', 20)->nullable();
            $table->string('colonia');
            $table->string('cp', 10);
            $table->foreignId('id_estado')->constrained('estados');
            $table->foreignId('id_municipio')->constrained('municipios');
            $table->string('referencias')->nullable();
            $table->boolean('predeterminada')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direcciones_usuario');
    }
};
