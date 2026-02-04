<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolPermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener roles
        $admin = Rol::where('nombre', 'admin')->first();
        $supervisor = Rol::where('nombre', 'supervisor')->first();
        $operador = Rol::where('nombre', 'operador')->first();
        $contador = Rol::where('nombre', 'contador')->first();
        $vendedor = Rol::where('nombre', 'vendedor')->first();

        // ADMIN: Todos los permisos
        $todosLosPermisos = Permiso::all();
        $admin->permisos()->attach($todosLosPermisos);
        $this->command->info("✅ Admin: {$todosLosPermisos->count()} permisos asignados");

        // SUPERVISOR: Módulos operacionales y comerciales
        $permisosSupervisor = Permiso::whereIn('modulo', [
            'dashboard',
            'clientes',
            'servicios',
            'presupuestos',
            'subcontratistas',
            'proveedores',
            'articulos',
            'ordenes_servicio',
            'reportes',
        ])->get();
        $supervisor->permisos()->attach($permisosSupervisor);
        $this->command->info("✅ Supervisor: {$permisosSupervisor->count()} permisos asignados");

        // CONTADOR: Módulos financieros y reportes
        $permisosContador = Permiso::whereIn('modulo', [
            'dashboard',
            'clientes',
            'presupuestos',
            'facturas_clientes',
            'pagos_clientes',
            'facturas_subcontratistas',
            'pagos_subcontratistas',
            'reportes',
        ])->get();
        $contador->permisos()->attach($permisosContador);
        $this->command->info("✅ Contador: {$permisosContador->count()} permisos asignados");

        // VENDEDOR: Módulos comerciales
        $permisosVendedor = Permiso::whereIn('modulo', [
            'dashboard',
            'clientes',
            'servicios',
            'presupuestos',
        ])->whereIn('accion', ['ver', 'crear', 'editar'])->get();
        $vendedor->permisos()->attach($permisosVendedor);
        $this->command->info("✅ Vendedor: {$permisosVendedor->count()} permisos asignados");

        // OPERADOR: Solo visualización
        $permisosOperador = Permiso::where('accion', 'ver')->get();
        $operador->permisos()->attach($permisosOperador);
        $this->command->info("✅ Operador: {$permisosOperador->count()} permisos asignados");
    }
}
