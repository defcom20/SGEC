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
        Schema::table('pago_clientes', function (Blueprint $table) {
            // Drop the incorrect foreign key
            // Note: We wrap in try-catch or check existence ideally, but schema builder handles dropForeign by name usually.
            // If the name is strictly 'pago_clientes_factura_cliente_id_foreign' as per error.
            $table->dropForeign('pago_clientes_factura_cliente_id_foreign');

            // Add the correct foreign key
            $table->foreign('factura_cliente_id')
                ->references('id')
                ->on('factura_clientes') // Correct table name
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pago_clientes', function (Blueprint $table) {
            $table->dropForeign(['factura_cliente_id']);

            // Revert to the incorrect one (technically strictly 'facturas_clientes') if we wanted true rollback
            $table->foreign('factura_cliente_id', 'pago_clientes_factura_cliente_id_foreign')
                ->references('id')
                ->on('facturas_clientes')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }
};
