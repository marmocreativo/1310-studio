<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galeria_flores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_flor')
                  ->constrained('directorio_floral')
                  ->cascadeOnDelete();
            $table->string('imagen');
            $table->boolean('estado')->default(true);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galeria_flores');
    }
};