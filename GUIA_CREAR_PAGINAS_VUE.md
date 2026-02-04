# 📘 GUÍA: Crear Páginas Vue con Sidebar

## 🎯 PROBLEMA RESUELTO

Cuando abres un módulo (Clientes, Presupuestos, etc.), debe mostrarse **dentro del layout con sidebar**, no como página completa.

---

## ✅ SOLUCIÓN: Usar AppLayout

Todas las páginas de módulos deben envolver su contenido con `<AppLayout>`.

---

## 📝 PLANTILLA PARA NUEVAS PÁGINAS

### **Estructura básica:**

```vue
<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

// Props que recibes del controlador
const props = defineProps({
    // tus datos aquí
    clientes: Array,
    // etc...
})

// Breadcrumbs para navegación
const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Clientes', href: '/clientes' },
]
</script>

<template>
    <!-- Título de la página (aparece en la pestaña del navegador) -->
    <Head title="Clientes" />
    
    <!-- Layout con sidebar -->
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- TU CONTENIDO AQUÍ -->
        <div class="p-6">
            <h1>Lista de Clientes</h1>
            
            <!-- Tu tabla, formularios, etc. -->
        </div>
    </AppLayout>
</template>

<style scoped>
/* Tus estilos aquí */
</style>
```

---

## 🔧 COMPONENTES DISPONIBLES

### **1. AppLayout (Principal)**
- Muestra el sidebar dinámico
- Muestra el header con breadcrumbs
- Envuelve todo el contenido

```vue
<AppLayout :breadcrumbs="breadcrumbs">
    <!-- contenido -->
</AppLayout>
```

### **2. Head (Título)**
- Define el título de la página
- Aparece en la pestaña del navegador

```vue
<Head title="Nombre del Módulo" />
```

### **3. Breadcrumbs (Navegación)**
- Muestra la ruta de navegación
- Formato: Array de objetos con `title` y `href`

```javascript
const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Módulo Actual', href: '/ruta-actual' },
]
```

---

## 📂 EJEMPLOS POR MÓDULO

### **Ejemplo 1: Clientes**

```vue
<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps({
    clientes: Array,
})

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Clientes', href: '/clientes' },
]
</script>

<template>
    <Head title="Clientes" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">Gestión de Clientes</h1>
            
            <!-- Tabla de clientes -->
            <div class="bg-white rounded-lg shadow">
                <!-- contenido -->
            </div>
        </div>
    </AppLayout>
</template>
```

### **Ejemplo 2: Presupuestos**

```vue
<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps({
    presupuestos: Array,
})

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Presupuestos', href: '/presupuestos' },
]
</script>

<template>
    <Head title="Presupuestos" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">Gestión de Presupuestos</h1>
            
            <!-- Tabla de presupuestos -->
        </div>
    </AppLayout>
</template>
```

---

## ⚠️ ERRORES COMUNES

### ❌ **SIN Layout (Pantalla completa sin sidebar)**
```vue
<template>
    <div>
        <!-- contenido -->
    </div>
</template>
```

### ✅ **CON Layout (Con sidebar y navegación)**
```vue
<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div>
            <!-- contenido -->
        </div>
    </AppLayout>
</template>
```

---

## 🎨 ESTILOS RECOMENDADOS

### **Contenedor principal:**
```vue
<div class="p-6">
    <!-- padding de 1.5rem en todos los lados -->
</div>
```

### **Tarjetas/Cards:**
```vue
<div class="bg-white rounded-lg shadow p-6">
    <!-- fondo blanco, bordes redondeados, sombra -->
</div>
```

### **Títulos:**
```vue
<h1 class="text-2xl font-bold mb-4">Título</h1>
<h2 class="text-xl font-semibold mb-3">Subtítulo</h2>
```

---

## 🚀 PRÓXIMOS PASOS

Para cada módulo que crees:

1. ✅ Importa `Head` y `AppLayout`
2. ✅ Define `breadcrumbs`
3. ✅ Envuelve con `<AppLayout>`
4. ✅ Agrega `<Head title="..." />`
5. ✅ Desarrolla tu contenido dentro

---

## 📋 CHECKLIST

Antes de considerar una página completa:

- [ ] ¿Importé `AppLayout`?
- [ ] ¿Definí `breadcrumbs`?
- [ ] ¿Envolví el contenido con `<AppLayout>`?
- [ ] ¿Agregué `<Head title="..." />`?
- [ ] ¿El sidebar se muestra correctamente?
- [ ] ¿Los breadcrumbs funcionan?

---

## 💡 TIPS

1. **Reutiliza componentes** - Crea componentes para tablas, formularios, etc.
2. **Usa Tailwind CSS** - Para estilos consistentes
3. **Verifica permisos** - Usa `usePermissions()` para mostrar/ocultar acciones
4. **Mantén consistencia** - Todas las páginas deben verse similares

---

**Fecha**: 2026-02-01  
**Proyecto**: SGEC - Sistema de Gestión Empresarial para Contratistas
