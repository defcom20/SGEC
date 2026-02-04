# 🔐 SISTEMA DE PERMISOS DINÁMICOS - SGEC

## 📋 RESUMEN

Tu sistema de módulos y permisos funciona de la siguiente manera:

### 🗄️ Base de Datos
```
users → rol_id → roles → permisos (many-to-many)
```

### ⚙️ Configuración
- **`config/modules.php`**: Define todos los módulos del sistema
- **`app/Helpers/PermissionHelper.php`**: Funciones helper para permisos
- **`app/Http/Middleware/CheckPermission.php`**: Middleware de protección

---

## 🎯 CÓMO FUNCIONA

### 1️⃣ **Backend: Proteger Rutas**

```php
// routes/web.php

use App\Http\Middleware\CheckPermission;

// Proteger una ruta específica
Route::get('/clientes', [ClienteController::class, 'index'])
    ->middleware(['auth', CheckPermission::class.':clientes,ver']);

// Proteger un grupo de rutas
Route::middleware(['auth'])->group(function () {
    
    // Clientes
    Route::get('/clientes', [ClienteController::class, 'index'])
        ->middleware(CheckPermission::class.':clientes,ver');
    
    Route::post('/clientes', [ClienteController::class, 'store'])
        ->middleware(CheckPermission::class.':clientes,crear');
    
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])
        ->middleware(CheckPermission::class.':clientes,editar');
    
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])
        ->middleware(CheckPermission::class.':clientes,eliminar');
});
```

### 2️⃣ **Backend: Verificar en Controladores**

```php
// app/Http/Controllers/ClienteController.php

public function index()
{
    // Verificar permiso manualmente
    if (!can_access('clientes', 'ver')) {
        abort(403, 'No tienes permiso para ver clientes');
    }
    
    $clientes = Cliente::with('presupuestos')->paginate(20);
    
    return Inertia::render('Clientes/Index', [
        'clientes' => $clientes,
        'can' => [
            'crear' => can_access('clientes', 'crear'),
            'editar' => can_access('clientes', 'editar'),
            'eliminar' => can_access('clientes', 'eliminar'),
        ],
    ]);
}
```

### 3️⃣ **Frontend: Menú Dinámico**

```vue
<!-- resources/js/Layouts/AppLayout.vue -->

<script setup>
import DynamicSidebar from '@/components/DynamicSidebar.vue'
</script>

<template>
    <div class="app-layout">
        <!-- Sidebar dinámico según permisos -->
        <DynamicSidebar />
        
        <main class="content">
            <slot />
        </main>
    </div>
</template>
```

### 4️⃣ **Frontend: Verificar Permisos en Componentes**

```vue
<!-- resources/js/pages/Clientes/Index.vue -->

<script setup>
import { usePermissions } from '@/composables/usePermissions'

const { can, isAdmin } = usePermissions()

const props = defineProps({
    clientes: Array,
})
</script>

<template>
    <div>
        <h1>Clientes</h1>
        
        <!-- Botón crear solo si tiene permiso -->
        <button v-if="can('clientes', 'crear')" @click="crear">
            Nuevo Cliente
        </button>
        
        <!-- Tabla de clientes -->
        <table>
            <tr v-for="cliente in clientes" :key="cliente.id">
                <td>{{ cliente.razon_social }}</td>
                <td>
                    <!-- Botones según permisos -->
                    <button v-if="can('clientes', 'editar')">
                        Editar
                    </button>
                    <button v-if="can('clientes', 'eliminar')">
                        Eliminar
                    </button>
                </td>
            </tr>
        </table>
        
        <!-- Sección solo para admin -->
        <div v-if="isAdmin" class="admin-section">
            <h2>Panel de Administrador</h2>
            <!-- Contenido exclusivo para admin -->
        </div>
    </div>
</template>
```

### 5️⃣ **Frontend: Verificar Múltiples Permisos**

```vue
<script setup>
import { usePermissions } from '@/composables/usePermissions'

const { can, canAny, canAll } = usePermissions()
</script>

<template>
    <!-- Mostrar si tiene AL MENOS UNO de los permisos -->
    <div v-if="canAny([
        ['clientes', 'ver'],
        ['presupuestos', 'ver']
    ])">
        Tienes acceso a clientes o presupuestos
    </div>
    
    <!-- Mostrar si tiene TODOS los permisos -->
    <div v-if="canAll([
        ['clientes', 'editar'],
        ['clientes', 'eliminar']
    ])">
        Puedes editar y eliminar clientes
    </div>
</template>
```

---

## 🛠️ GESTIÓN DE ROLES Y PERMISOS

### Crear Roles y Asignar Permisos

```php
// database/seeders/RoleSeeder.php

use App\Models\Rol;
use App\Models\Permiso;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Crear roles
        $admin = Rol::create([
            'nombre' => 'admin',
            'descripcion' => 'Administrador del sistema',
        ]);
        
        $supervisor = Rol::create([
            'nombre' => 'supervisor',
            'descripcion' => 'Supervisor de proyectos',
        ]);
        
        $operador = Rol::create([
            'nombre' => 'operador',
            'descripcion' => 'Operador básico',
        ]);
        
        // Crear permisos
        $permisos = [
            // Clientes
            ['modulo' => 'clientes', 'accion' => 'ver'],
            ['modulo' => 'clientes', 'accion' => 'crear'],
            ['modulo' => 'clientes', 'accion' => 'editar'],
            ['modulo' => 'clientes', 'accion' => 'eliminar'],
            
            // Presupuestos
            ['modulo' => 'presupuestos', 'accion' => 'ver'],
            ['modulo' => 'presupuestos', 'accion' => 'crear'],
            ['modulo' => 'presupuestos', 'accion' => 'editar'],
            ['modulo' => 'presupuestos', 'accion' => 'aprobar'],
            
            // ... más permisos
        ];
        
        foreach ($permisos as $permiso) {
            Permiso::create($permiso);
        }
        
        // Admin tiene TODOS los permisos
        $admin->permisos()->attach(Permiso::all());
        
        // Supervisor tiene permisos limitados
        $supervisor->permisos()->attach(
            Permiso::whereIn('modulo', ['clientes', 'presupuestos', 'ordenes_servicio'])
                ->get()
        );
        
        // Operador solo puede ver
        $operador->permisos()->attach(
            Permiso::where('accion', 'ver')->get()
        );
    }
}
```

---

## 📊 FLUJO COMPLETO

```
1. Usuario inicia sesión
   ↓
2. Sistema carga su rol y permisos
   ↓
3. Inertia comparte permisos con Vue
   ↓
4. Sidebar muestra solo módulos permitidos
   ↓
5. Usuario navega a un módulo
   ↓
6. Middleware verifica permiso en backend
   ↓
7. Componente Vue muestra/oculta botones según permisos
   ↓
8. Usuario intenta una acción
   ↓
9. Backend valida permiso nuevamente
   ↓
10. Acción se ejecuta o se deniega
```

---

## ✅ VENTAJAS DE ESTE SISTEMA

1. **Dinámico**: Los módulos se muestran según el rol del usuario
2. **Seguro**: Validación en backend Y frontend
3. **Escalable**: Fácil agregar nuevos módulos en `config/modules.php`
4. **Flexible**: Permisos granulares (ver, crear, editar, eliminar, etc.)
5. **Centralizado**: Una sola fuente de verdad para módulos
6. **Reutilizable**: Composable `usePermissions()` en cualquier componente

---

## 🎯 PRÓXIMOS PASOS

1. ✅ Ejecutar migraciones
2. ⏳ Crear seeders para roles y permisos
3. ⏳ Implementar página de gestión de roles
4. ⏳ Implementar página de gestión de usuarios
5. ⏳ Crear componentes Vue para cada módulo
