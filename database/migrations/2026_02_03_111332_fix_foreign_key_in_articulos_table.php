<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('articulos', function (Blueprint $table) {
            // Primero eliminamos la clave foránea incorrecta
            // El nombre por defecto suele ser tabla_columna_foreign
            $table->dropForeign(['proveedor_id']);

            // Luego creamos la correcta apuntando a 'proveedors'
            $table->foreign('proveedor_id')
                ->references('id')
                ->on('proveedors')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articulos', function (Blueprint $table) {
            $table->dropForeign(['proveedor_id']);

            // Revertir a la incorrecta (o a proveedores si existiera)
            // Esto es solo para rollback, idealmente no revertiríamos a un estado roto
            $table->foreign('proveedor_id')
                ->references('id')
                ->on('proveedores') // La tabla original incorrecta
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }
};
