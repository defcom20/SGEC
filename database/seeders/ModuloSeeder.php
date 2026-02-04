<?php

namespace Database\Seeders;

use App\Models\Modulo;
use Illuminate\Database\Seeder;

class ModuloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. CREAR MÓDULOS PADRE (nivel 1)
        $principal = Modulo::create([
            'nombre' => 'Principal',
            'slug' => 'principal',
            'icono' => 'LayoutGrid',
            'emoji' => '🏠',
            'ruta' => '#',
            'descripcion' => 'Módulo principal del sistema',
            'orden' => 1,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 1,
            'parent_id' => null,
            'acciones' => ['ver'],
        ]);

        $comercial = Modulo::create([
            'nombre' => 'Comercial',
            'slug' => 'comercial',
            'icono' => 'Briefcase',
            'emoji' => '💼',
            'ruta' => '#',
            'descripcion' => 'Gestión comercial y ventas',
            'orden' => 2,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 1,
            'parent_id' => null,
            'acciones' => ['ver'],
        ]);

        $operaciones = Modulo::create([
            'nombre' => 'Operaciones',
            'slug' => 'operaciones',
            'icono' => 'ClipboardList',
            'emoji' => '⚙️',
            'ruta' => '#',
            'descripcion' => 'Gestión de operaciones y proyectos',
            'orden' => 3,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 1,
            'parent_id' => null,
            'acciones' => ['ver'],
        ]);

        $finanzas = Modulo::create([
            'nombre' => 'Finanzas',
            'slug' => 'finanzas',
            'icono' => 'DollarSign',
            'emoji' => '💰',
            'ruta' => '#',
            'descripcion' => 'Gestión financiera y contable',
            'orden' => 4,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 1,
            'parent_id' => null,
            'acciones' => ['ver'],
        ]);

        $reportes = Modulo::create([
            'nombre' => 'Reportes',
            'slug' => 'reportes',
            'icono' => 'BarChart3',
            'emoji' => '📊',
            'ruta' => '#',
            'descripcion' => 'Reportes y análisis',
            'orden' => 5,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 1,
            'parent_id' => null,
            'acciones' => ['ver'],
        ]);

        $configuracion = Modulo::create([
            'nombre' => 'Configuración',
            'slug' => 'configuracion',
            'icono' => 'Settings',
            'emoji' => '🔧',
            'ruta' => '#',
            'descripcion' => 'Configuración del sistema',
            'orden' => 6,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 1,
            'parent_id' => null,
            'acciones' => ['ver'],
        ]);

        // 2. CREAR SUBMÓDULOS (nivel 2)

        // Principal > Dashboard
        Modulo::create([
            'nombre' => 'Dashboard',
            'slug' => 'dashboard',
            'icono' => 'LayoutGrid',
            'ruta' => 'dashboard',
            'descripcion' => 'Panel principal del sistema',
            'orden' => 1,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 2,
            'parent_id' => $principal->id,
            'acciones' => ['ver'],
        ]);

        // Comercial > Clientes
        Modulo::create([
            'nombre' => 'Clientes',
            'slug' => 'clientes',
            'icono' => 'Users',
            'ruta' => 'clientes.index',
            'descripcion' => 'Gestión de clientes',
            'orden' => 1,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 2,
            'parent_id' => $comercial->id,
            'acciones' => ['ver', 'crear', 'editar', 'eliminar'],
        ]);

        // Comercial > Servicios
        Modulo::create([
            'nombre' => 'Servicios',
            'slug' => 'servicios',
            'icono' => 'Briefcase',
            'ruta' => 'servicios.index',
            'descripcion' => 'Catálogo de servicios',
            'orden' => 2,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 2,
            'parent_id' => $comercial->id,
            'acciones' => ['ver', 'crear', 'editar', 'eliminar'],
        ]);

        // Comercial > Presupuestos
        Modulo::create([
            'nombre' => 'Presupuestos',
            'slug' => 'presupuestos',
            'icono' => 'FileText',
            'ruta' => 'presupuestos.index',
            'descripcion' => 'Gestión de presupuestos',
            'orden' => 3,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 2,
            'parent_id' => $comercial->id,
            'acciones' => ['ver', 'crear', 'editar', 'eliminar', 'aprobar'],
        ]);

        // Operaciones > Subcontratistas
        Modulo::create([
            'nombre' => 'Subcontratistas',
            'slug' => 'subcontratistas',
            'icono' => 'Users',
            'ruta' => 'subcontratistas.index',
            'descripcion' => 'Gestión de subcontratistas',
            'orden' => 1,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 2,
            'parent_id' => $operaciones->id,
            'acciones' => ['ver', 'crear', 'editar', 'eliminar'],
        ]);

        // Operaciones > Proveedores
        Modulo::create([
            'nombre' => 'Proveedores',
            'slug' => 'proveedores',
            'icono' => 'Package',
            'ruta' => 'proveedors.index',
            'descripcion' => 'Gestión de proveedores',
            'orden' => 2,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 2,
            'parent_id' => $operaciones->id,
            'acciones' => ['ver', 'crear', 'editar', 'eliminar'],
        ]);

        // Operaciones > Artículos
        Modulo::create([
            'nombre' => 'Artículos',
            'slug' => 'articulos',
            'icono' => 'Package',
            'ruta' => 'articulos.index',
            'descripcion' => 'Inventario de artículos',
            'orden' => 3,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 2,
            'parent_id' => $operaciones->id,
            'acciones' => ['ver', 'crear', 'editar', 'eliminar'],
        ]);

        // Operaciones > Órdenes de Servicio
        Modulo::create([
            'nombre' => 'Órdenes de Servicio',
            'slug' => 'ordenes_servicio',
            'icono' => 'ClipboardList',
            'ruta' => 'orden-servicios.index',
            'descripcion' => 'Gestión de órdenes de servicio',
            'orden' => 4,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 2,
            'parent_id' => $operaciones->id,
            'acciones' => ['ver', 'crear', 'editar', 'eliminar', 'aprobar'],
        ]);

        // Finanzas > Facturas Clientes
        Modulo::create([
            'nombre' => 'Facturas Clientes',
            'slug' => 'factura-clientes',
            'icono' => 'Receipt',
            'ruta' => 'factura-clientes.index',
            'descripcion' => 'Cuentas por cobrar',
            'orden' => 1,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 2,
            'parent_id' => $finanzas->id,
            'acciones' => ['ver', 'crear', 'editar', 'eliminar'],
        ]);

        // Finanzas > Pagos Clientes
        Modulo::create([
            'nombre' => 'Pagos Clientes',
            'slug' => 'pagos_clientes',
            'icono' => 'CreditCard',
            'ruta' => 'pago-clientes.index',
            'descripcion' => 'Registro de pagos de clientes',
            'orden' => 2,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 2,
            'parent_id' => $finanzas->id,
            'acciones' => ['ver', 'crear', 'editar', 'eliminar'],
        ]);

        // Finanzas > Facturas Subcontratistas
        Modulo::create([
            'nombre' => 'Facturas Subcontratistas',
            'slug' => 'facturas_subcontratistas',
            'icono' => 'Receipt',
            'ruta' => 'factura-subcontratistas.index',
            'descripcion' => 'Cuentas por pagar',
            'orden' => 3,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 2,
            'parent_id' => $finanzas->id,
            'acciones' => ['ver', 'crear', 'editar', 'eliminar'],
        ]);

        // Finanzas > Pagos Subcontratistas
        Modulo::create([
            'nombre' => 'Pagos Subcontratistas',
            'slug' => 'pagos_subcontratistas',
            'icono' => 'CreditCard',
            'ruta' => 'pago-subcontratistas.index',
            'descripcion' => 'Registro de pagos a subcontratistas',
            'orden' => 4,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 2,
            'parent_id' => $finanzas->id,
            'acciones' => ['ver', 'crear', 'editar', 'eliminar'],
        ]);

        // Reportes > Reportes
        Modulo::create([
            'nombre' => 'Reportes',
            'slug' => 'reportes_sistema',
            'icono' => 'BarChart3',
            'ruta' => 'reportes.index',
            'descripcion' => 'Reportes y estadísticas',
            'orden' => 1,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 2,
            'parent_id' => $reportes->id,
            'acciones' => ['ver', 'exportar'],
        ]);

        // Configuración > Roles y Permisos
        Modulo::create([
            'nombre' => 'Roles y Permisos',
            'slug' => 'roles_permisos',
            'icono' => 'Shield',
            'ruta' => 'rols.index',
            'descripcion' => 'Gestión de roles y permisos',
            'orden' => 1,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 2,
            'parent_id' => $configuracion->id,
            'acciones' => ['ver', 'crear', 'editar', 'eliminar'],
        ]);

        // Configuración > Usuarios
        Modulo::create([
            'nombre' => 'Usuarios',
            'slug' => 'usuarios',
            'icono' => 'Users',
            'ruta' => 'usuarios.index',
            'descripcion' => 'Gestión de usuarios del sistema',
            'orden' => 2,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 2,
            'parent_id' => $configuracion->id,
            'acciones' => ['ver', 'crear', 'editar', 'eliminar'],
        ]);

        // Configuración > Gestión de Módulos
        Modulo::create([
            'nombre' => 'Gestión de Módulos',
            'slug' => 'gestion_modulos',
            'icono' => 'Settings',
            'ruta' => 'modulos.index',
            'descripcion' => 'Activar/desactivar módulos del sistema',
            'orden' => 3,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 2,
            'parent_id' => $configuracion->id,
            'acciones' => ['ver', 'editar'],
        ]);

        // Configuración > Configuración General
        Modulo::create([
            'nombre' => 'Configuración General',
            'slug' => 'configuracion_general',
            'icono' => 'Settings',
            'ruta' => 'empresas.index',
            'descripcion' => 'Configuración general del sistema',
            'orden' => 4,
            'activo' => true,
            'visible_menu' => true,
            'nivel' => 2,
            'parent_id' => $configuracion->id,
            'acciones' => ['ver', 'editar'],
        ]);

        $this->command->info('✅ Módulos jerárquicos creados correctamente');
        $this->command->info('   - 6 módulos padre');
        $this->command->info('   - 18 submódulos');
    }
}
