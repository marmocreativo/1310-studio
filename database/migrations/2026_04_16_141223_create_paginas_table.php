<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paginas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->enum('categoria', ['general', 'legal'])->default('general');
            $table->text('resumen')->nullable();
            $table->longText('contenido')->nullable();
            $table->string('imagen')->nullable();
            $table->enum('estado', ['publicado', 'borrador'])->default('borrador');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paginas');
    }
};
