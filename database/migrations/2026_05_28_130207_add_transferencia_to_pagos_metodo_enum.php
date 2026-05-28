<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `pagos` MODIFY `metodo` ENUM('tarjeta','efectivo','transferencia','contra_entrega') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `pagos` MODIFY `metodo` ENUM('tarjeta','efectivo','contra_entrega') NOT NULL");
    }
};
