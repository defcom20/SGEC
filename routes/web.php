<?php

use App\Http\Controllers\ModuloController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

// Página de bienvenida
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

// Rutas protegidas con autenticación
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Gestión de Módulos (solo admin)
    Route::get('/modulos', [ModuloController::class, 'index'])->name('modulos.index');
    Route::post('/modulos/{modulo}/toggle', [ModuloController::class, 'toggle'])->name('modulos.toggle');
    Route::post('/modulos/order', [ModuloController::class, 'updateOrder'])->name('modulos.updateOrder');

    // Rutas de recursos generadas por Blueprint
    Route::resource('rols', App\Http\Controllers\RolController::class);
    Route::resource('usuarios', App\Http\Controllers\UsuarioController::class);
    Route::resource('permisos', App\Http\Controllers\PermisoController::class);
    Route::resource('clientes', App\Http\Controllers\ClienteController::class);
    Route::resource('subcontratistas', App\Http\Controllers\SubcontratistaController::class);
    Route::resource('proveedors', App\Http\Controllers\ProveedorController::class);
    Route::resource('articulos', App\Http\Controllers\ArticuloController::class);
    Route::resource('servicios', App\Http\Controllers\ServicioController::class);
    Route::resource('presupuestos', App\Http\Controllers\PresupuestoController::class);
    Route::resource('presupuesto-detalles', App\Http\Controllers\PresupuestoDetalleController::class);
    Route::resource('orden-servicios', App\Http\Controllers\OrdenServicioController::class);
    Route::resource('orden-servicio-detalles', App\Http\Controllers\OrdenServicioDetalleController::class);
    Route::resource('factura-clientes', App\Http\Controllers\FacturaClienteController::class);
    Route::resource('pago-clientes', App\Http\Controllers\PagoClienteController::class);
    Route::resource('factura-subcontratistas', App\Http\Controllers\FacturaSubcontratistaController::class);
    Route::resource('pago-subcontratistas', App\Http\Controllers\PagoSubcontratistaController::class);
    Route::resource('empresas', App\Http\Controllers\EmpresaController::class);
    Route::resource('parametros', App\Http\Controllers\ParametroController::class);
});

// Rutas de configuración de perfil
require __DIR__ . '/settings.php';
