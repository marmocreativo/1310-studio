<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variacion_sku_galeria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sku')
                ->constrained('variacion_skus')
                ->cascadeOnDelete();
            $table->string('imagen');
            $table->boolean('estado')->default(true);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variacion_sku_galeria');
    }
};