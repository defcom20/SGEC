<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Rol de Administrador (todos los permisos)
        $admin = Rol::firstOrCreate(
            ['nombre' => 'Administrador'],
            ['descripcion' => 'Acceso completo a todas las funcionalidades del sistema']
        );

        // Asignar todos los permisos al administrador
        $todosLosPermisos = Permiso::all()->pluck('id');
        $admin->permisos()->sync($todosLosPermisos);

        // Rol de Supervisor
        $supervisor = Rol::firstOrCreate(
            ['nombre' => 'Supervisor'],
            ['descripcion' => 'Puede ver y editar la mayoría de módulos, pero no eliminar']
        );

        // Permisos de supervisor (ver y editar, sin eliminar)
        $permisosSupervisor = Permiso::whereIn('accion', ['ver', 'editar', 'crear', 'aprobar'])
            ->get()
            ->pluck('id');
        $supervisor->permisos()->sync($permisosSupervisor);

        // Rol de Operador
        $operador = Rol::firstOrCreate(
            ['nombre' => 'Operador'],
            ['descripcion' => 'Acceso limitado para operaciones diarias']
        );

        // Permisos de operador (solo ver y crear en módulos operativos)
        $modulosOperativos = [
            'clientes',
            'presupuestos',
            'orden-servicios',
            'facturas-clientes',
            'pagos-clientes',
        ];

        $permisosOperador = Permiso::whereIn('modulo', $modulosOperativos)
            ->whereIn('accion', ['ver', 'crear'])
            ->get()
            ->pluck('id');
        $operador->permisos()->sync($permisosOperador);

        // Rol de Consulta
        $consulta = Rol::firstOrCreate(
            ['nombre' => 'Consulta'],
            ['descripcion' => 'Solo puede ver información, sin modificar']
        );

        // Permisos de consulta (solo ver)
        $permisosConsulta = Permiso::where('accion', 'ver')
            ->get()
            ->pluck('id');
        $consulta->permisos()->sync($permisosConsulta);

        $this->command->info('✅ Roles creados con sus permisos asignados');
    }
}
