# 🎛️ GESTIÓN DINÁMICA DE MÓDULOS - SGEC

## ✅ SOLUCIÓN IMPLEMENTADA

Como **administrador**, ahora puedes **activar/desactivar módulos desde la interfaz web** sin tocar código.

---

## 📋 CÓMO FUNCIONA

### 1️⃣ **Base de Datos**
Los módulos se almacenan en la tabla `modulos`:

```sql
CREATE TABLE modulos (
    id BIGINT PRIMARY KEY,
    uuid CHAR(36) UNIQUE,
    nombre VARCHAR(100),           -- "Clientes"
    slug VARCHAR(100) UNIQUE,      -- "clientes"
    icono VARCHAR(50),             -- "UsersIcon"
    ruta VARCHAR(255),             -- "clientes.index"
    descripcion TEXT,
    orden INT,
    activo BOOLEAN DEFAULT TRUE,   -- ← ACTIVAR/DESACTIVAR
    visible_menu BOOLEAN,
    categoria VARCHAR(100),        -- "Comercial", "Operaciones", etc.
    acciones JSON,                 -- ["ver", "crear", "editar", "eliminar"]
    ...
);
```

### 2️⃣ **Interfaz de Administración**

**Ruta:** `/modulos`

**Funcionalidades:**
- ✅ Ver todos los módulos del sistema
- ✅ Activar/desactivar con un switch
- ✅ Agrupados por categoría
- ✅ Ver acciones disponibles de cada módulo
- ✅ Cambios en tiempo real

---

## 🎯 USO COMO ADMINISTRADOR

### **Desactivar un módulo:**

1. Ir a **Configuración → Gestión de Módulos**
2. Buscar el módulo que quieres desactivar
3. Hacer clic en el **switch** para desactivarlo
4. ✅ **¡Listo!** El módulo desaparece del menú para TODOS los usuarios

### **Reactivar un módulo:**

1. Ir a **Configuración → Gestión de Módulos**
2. Buscar el módulo desactivado (aparece gris)
3. Hacer clic en el **switch** para activarlo
4. ✅ **¡Listo!** El módulo vuelve a estar disponible

---

## 🔒 SEGURIDAD

### **¿Qué pasa cuando desactivas un módulo?**

1. **Desaparece del menú** para todos los usuarios
2. **Las rutas se bloquean** automáticamente
3. **Nadie puede acceder**, ni siquiera con la URL directa
4. **Los datos NO se eliminan**, solo se oculta el acceso

### **Validación en Backend:**

```php
// El helper can_access() verifica automáticamente
function can_access(string $modulo, string $accion = 'ver'): bool
{
    // 1. Verificar si el módulo está activo
    $moduloActivo = Modulo::where('slug', $modulo)
        ->where('activo', true)
        ->exists();
    
    if (!$moduloActivo) {
        return false; // ← Módulo desactivado
    }
    
    // 2. Verificar permisos del usuario
    // ...
}
```

---

## 📁 ARCHIVOS CREADOS

### **Backend:**
1. ✅ `database/migrations/..._create_modulos_table.php` - Tabla de módulos
2. ✅ `app/Models/Modulo.php` - Modelo Modulo
3. ✅ `database/seeders/ModuloSeeder.php` - Datos iniciales
4. ✅ `app/Http/Controllers/ModuloController.php` - Controlador
5. ✅ `app/Helpers/PermissionHelper.php` - Actualizado para leer de BD

### **Frontend:**
6. ✅ `resources/js/pages/Modulos/Index.vue` - Interfaz de gestión

### **Eliminados (basura):**
7. ❌ `config/modules.php` - **ELIMINADO** (ya no se usa)

---

## 🚀 PRÓXIMOS PASOS

### **1. Ejecutar Migraciones**
```bash
php artisan migrate
```

### **2. Poblar Módulos Iniciales**
```bash
php artisan db:seed --class=ModuloSeeder
```

### **3. Agregar Ruta**
```php
// routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::get('/modulos', [ModuloController::class, 'index'])
        ->name('modulos.index');
    
    Route::post('/modulos/{modulo}/toggle', [ModuloController::class, 'toggle'])
        ->name('modulos.toggle');
});
```

### **4. Acceder a la Interfaz**
```
https://sgec.test/modulos
```

---

## 💡 EJEMPLO DE USO

### **Escenario: Desactivar módulo de Reportes temporalmente**

**Antes:**
- Todos los usuarios ven "Reportes" en el menú
- Pueden acceder a `/reportes`

**Acción del Admin:**
1. Ir a `/modulos`
2. Buscar "Reportes"
3. Desactivar el switch

**Después:**
- ❌ "Reportes" desaparece del menú
- ❌ Nadie puede acceder a `/reportes` (403 Forbidden)
- ✅ Los datos de reportes siguen en la BD
- ✅ Cuando reactives, todo vuelve a funcionar

---

## 🎨 CATEGORÍAS DE MÓDULOS

Los módulos están organizados en:

- **Principal**: Dashboard
- **Comercial**: Clientes, Servicios, Presupuestos
- **Operaciones**: Subcontratistas, Proveedores, Artículos, Órdenes de Servicio
- **Finanzas**: Facturas, Pagos (Clientes y Subcontratistas)
- **Reportes**: Reportes y análisis
- **Configuración**: Roles, Usuarios, Módulos, Configuración General

---

## ✅ VENTAJAS

1. **Sin tocar código**: Todo desde la interfaz web
2. **Sin reiniciar servidor**: Cambios instantáneos
3. **Seguro**: Validación en backend
4. **Reversible**: Puedes reactivar en cualquier momento
5. **Auditable**: Se registra quién modificó qué
6. **Flexible**: Puedes cambiar orden, visibilidad, etc.

---

## 🔐 PERMISOS REQUERIDOS

Solo usuarios con permiso `modulos:editar` pueden gestionar módulos.

Por defecto, solo el rol **admin** tiene este permiso.

---

¡Ahora tienes control total sobre los módulos del sistema sin depender del programador! 🎉
