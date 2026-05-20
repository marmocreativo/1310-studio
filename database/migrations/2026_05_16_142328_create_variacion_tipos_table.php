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
        Schema::create('variacion_tipos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_producto')->constrained('productos')->cascadeOnDelete();
            $table->foreignId('id_tipo_default')->nullable()->constrained('variacion_tipos_default')->nullOnDelete();
            $table->string('nombre');
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variacion_tipos');
    }
};
