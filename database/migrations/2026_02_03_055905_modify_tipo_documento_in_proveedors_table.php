<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cambiamos la columna tipo_documento de ENUM('RUC') a VARCHAR(20)
        // Usamos DB::statement para ser explícitos y evitar issues con doctrine/dbal en enums
        DB::statement("ALTER TABLE proveedors MODIFY COLUMN tipo_documento VARCHAR(20) NOT NULL DEFAULT 'RUC'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir es complicado porque podríamos tener datos inválidos para el ENUM original.
        // Lo dejaremos como VARCHAR ya que es una mejora estructural, o intentamos revertir a ENUM
        // DB::statement("ALTER TABLE proveedors MODIFY COLUMN tipo_documento ENUM('RUC') NOT NULL DEFAULT 'RUC'");
    }
};
