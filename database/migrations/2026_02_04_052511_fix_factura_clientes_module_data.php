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
        // Ensure the module exists and has the correct route/slug
        $finanzas = \App\Models\Modulo::where('slug', 'finanzas')->first();

        if ($finanzas) {
            \App\Models\Modulo::updateOrCreate(
                ['slug' => 'factura-clientes'], // Use new slug as identifier if it exists, roughly
                [
                    'nombre' => 'Facturas Clientes',
                    'slug' => 'factura-clientes', // Updated to match URL style (hyphens)
                    'icono' => 'Receipt',
                    'ruta' => 'factura-clientes.index',
                    'descripcion' => 'Cuentas por cobrar',
                    'orden' => 1,
                    'activo' => true,
                    'visible_menu' => true,
                    'nivel' => 2,
                    'parent_id' => $finanzas->id,
                    'acciones' => ['ver', 'crear', 'editar', 'eliminar'],
                ]
            );

            // Also delete the old one if it exists separately (to avoid duplicates if slug changed)
            \App\Models\Modulo::where('slug', 'facturas_clientes')->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old slug if needed, but not strictly necessary for this fix
    }
};
