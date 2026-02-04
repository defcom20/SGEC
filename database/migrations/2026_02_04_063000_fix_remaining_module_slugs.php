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
        // Fix Pagos Clientes
        DB::table('modulos')
            ->where('slug', 'pagos_clientes')
            ->update([
                'slug' => 'pago-clientes',
                'ruta' => 'pago-clientes.index'
            ]);

        // Fix Facturas Subcontratistas
        DB::table('modulos')
            ->where('slug', 'facturas_subcontratistas')
            ->update([
                'slug' => 'factura-subcontratistas',
                'ruta' => 'factura-subcontratistas.index'
            ]);

        // Fix Pagos Subcontratistas
        DB::table('modulos')
            ->where('slug', 'pagos_subcontratistas')
            ->update([
                'slug' => 'pago-subcontratistas',
                'ruta' => 'pago-subcontratistas.index'
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert is complex without knowing original IDs, but strictly speaking we could just flip them back if needed.
        // For now, we consider this a one-way fix.
    }
};
