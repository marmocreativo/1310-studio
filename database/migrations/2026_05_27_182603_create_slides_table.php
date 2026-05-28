<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slides', function (Blueprint $table) {
            $table->id();
            $table->string('titulo')->nullable();
            $table->string('tipo')->default('imagen'); // imagen | video | capas
            $table->string('caption')->nullable();
            $table->string('texto_boton')->nullable();
            $table->string('enlace_boton')->nullable();

            // Imagen de fondo (todos los tipos) — 1920x1080 webp
            $table->string('imagen_fondo')->nullable();

            // Logo (todos los tipos)
            $table->string('logo')->nullable();

            // Video (tipo: video)
            $table->string('video')->nullable();           // path local mp4
            $table->string('video_youtube')->nullable();   // URL youtube

            // Overlay (tipo: capas) — 1080x1080 webp con transparencia
            $table->string('imagen_overlay')->nullable();

            $table->boolean('estado')->default(true);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slides');
    }
};