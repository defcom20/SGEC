# CONFIGURACIÓN HÍBRIDA: ID + UUID

## 📋 Estructura de cada modelo

Todos los modelos tendrán:
- **`id`**: BIGINT auto-incremental (PRIMARY KEY) - Para performance en joins
- **`uuid`**: UUID único indexado - Para URLs públicas y seguridad

---

## 🔧 PASO 1: Después de ejecutar `blueprint:build`

Blueprint generará las migraciones y modelos. Necesitarás hacer algunos ajustes:

---

## 📝 PASO 2: Configurar los Modelos

En cada modelo generado (ejemplo: `app/Models/Cliente.php`), agrega:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Cliente extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'tipo_documento',
        'numero_documento',
        'razon_social',
        // ... resto de campos
    ];

    /**
     * Boot del modelo - Genera UUID automáticamente
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Usar UUID en las rutas en lugar de ID
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Buscar por UUID
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('uuid', $value)->firstOrFail();
    }
}
```

---

## 🌐 PASO 3: Rutas

Las rutas automáticamente usarán UUID:

```php
// routes/web.php
Route::resource('clientes', ClienteController::class);

// Generará URLs como:
// GET  /clientes/{uuid}           - show
// GET  /clientes/{uuid}/edit      - edit
// PUT  /clientes/{uuid}           - update
// DELETE /clientes/{uuid}         - destroy
```

---

## 🎯 PASO 4: Uso en Controladores

```php
// ClienteController.php

public function show(Cliente $cliente)
{
    // Laravel automáticamente busca por UUID
    // $cliente ya está cargado
    
    return Inertia::render('Clientes/Show', [
        'cliente' => $cliente
    ]);
}

public function update(Request $request, Cliente $cliente)
{
    $cliente->update($request->validated());
    
    return redirect()->route('clientes.show', $cliente);
    // Redirige a /clientes/{uuid}
}
```

---

## 🔍 PASO 5: Queries Internas (Performance)

Cuando hagas queries internas, usa el ID numérico:

```php
// ✅ RÁPIDO - Usa ID numérico para joins
$presupuestos = Presupuesto::where('cliente_id', $cliente->id)
    ->with('detalles')
    ->get();

// ✅ RÁPIDO - Joins internos usan IDs
$query = DB::table('presupuestos')
    ->join('clientes', 'presupuestos.cliente_id', '=', 'clientes.id')
    ->where('clientes.id', $clienteId)
    ->get();

// ❌ EVITAR - Más lento con UUIDs
// $presupuestos = Presupuesto::where('cliente_uuid', $uuid)->get();
```

---

## 📊 PASO 6: En Vue.js (Frontend)

```vue
<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
    cliente: Object
})

// Navegar usando UUID (seguro)
const editarCliente = () => {
    router.visit(`/clientes/${props.cliente.uuid}/edit`)
}

// Mostrar ID interno solo en debug/admin
console.log('ID interno:', props.cliente.id)
console.log('UUID público:', props.cliente.uuid)
</script>

<template>
    <div>
        <h1>{{ cliente.razon_social }}</h1>
        
        <!-- URL pública usa UUID -->
        <a :href="`/clientes/${cliente.uuid}`">
            Ver detalles
        </a>
        
        <!-- ID interno NO se muestra al usuario -->
        <span v-if="$page.props.auth.user.is_admin">
            ID: {{ cliente.id }}
        </span>
    </div>
</template>
```

---

## 🔐 VENTAJAS DE ESTE ENFOQUE

### URLs Públicas (UUID):
```
✅ /clientes/a3f2b4c5-1234-5678-90ab-cdef12345678
✅ /presupuestos/f7e8d9c0-5678-1234-abcd-ef1234567890
```

### Queries Internas (ID):
```sql
✅ SELECT * FROM presupuestos WHERE cliente_id = 123
✅ SELECT * FROM pagos WHERE factura_id = 456
```

### Debugging:
```
✅ "El cliente ID 5 tiene un problema"
✅ "Revisar presupuesto ID 42"
```

---

## 📌 RESUMEN

| Aspecto | Usa | Ejemplo |
|---------|-----|---------|
| **URLs públicas** | UUID | `/clientes/{uuid}` |
| **Foreign Keys** | ID numérico | `cliente_id = 123` |
| **Joins/Queries** | ID numérico | `WHERE cliente_id = 123` |
| **Debugging** | ID numérico | "Cliente ID 5" |
| **APIs externas** | UUID | `{"uuid": "a3f2..."}` |
| **Route Model Binding** | UUID | `Route::get('/clientes/{cliente}')` |

---

## ✅ PRÓXIMOS PASOS

1. ✅ Ejecutar `php artisan blueprint:build`
2. ⏳ Agregar el método `boot()` a cada modelo
3. ⏳ Agregar `getRouteKeyName()` a cada modelo
4. ⏳ Ejecutar migraciones
5. ⏳ Probar creación de registros (UUID se genera automáticamente)
