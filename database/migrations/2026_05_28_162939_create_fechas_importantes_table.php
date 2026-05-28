<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fechas_importantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('dia');
            $table->unsignedTinyInteger('mes');
            $table->string('etiqueta', 100);
            $table->timestamps();

            $table->index(['id_usuario', 'mes', 'dia']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fechas_importantes');
    }
};
