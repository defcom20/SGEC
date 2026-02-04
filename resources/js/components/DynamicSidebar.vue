<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()

// Obtener módulos accesibles del usuario
const userModules = computed(() => page.props.permissions?.modules || [])

// Verificar si el usuario tiene permiso
const can = (modulo, accion = 'ver') => {
    const user = page.props.auth?.user
    
    if (!user) return false
    
    // Admin tiene acceso a todo
    if (user.rol?.nombre === 'admin') return true
    
    // Verificar en los módulos del usuario
    const module = userModules.value.find(m => m.slug === modulo)
    return module?.acciones?.includes(accion) || false
}

// Agrupar módulos por categoría (opcional)
const modulesByCategory = computed(() => {
    return {
        'Comercial': userModules.value.filter(m => 
            ['clientes', 'presupuestos', 'servicios'].includes(m.slug)
        ),
        'Operaciones': userModules.value.filter(m => 
            ['ordenes_servicio', 'subcontratistas', 'proveedores', 'articulos'].includes(m.slug)
        ),
        'Finanzas': userModules.value.filter(m => 
            ['facturas_clientes', 'pagos_clientes', 'facturas_subcontratistas', 'pagos_subcontratistas'].includes(m.slug)
        ),
        'Reportes': userModules.value.filter(m => 
            ['reportes'].includes(m.slug)
        ),
        'Configuración': userModules.value.filter(m => 
            ['roles', 'usuarios', 'configuracion'].includes(m.slug)
        ),
    }
})
</script>

<template>
    <nav class="sidebar">
        <!-- Dashboard siempre visible -->
        <Link 
            v-if="can('dashboard')" 
            :href="route('dashboard')" 
            class="nav-item"
        >
            <span class="icon">🏠</span>
            <span>Dashboard</span>
        </Link>

        <!-- Módulos agrupados por categoría -->
        <div 
            v-for="(modules, category) in modulesByCategory" 
            :key="category"
            v-show="modules.length > 0"
            class="nav-category"
        >
            <div class="category-title">{{ category }}</div>
            
            <Link
                v-for="module in modules"
                :key="module.slug"
                :href="route(module.ruta)"
                class="nav-item"
            >
                <span class="icon">{{ getIcon(module.icono) }}</span>
                <span>{{ module.nombre }}</span>
            </Link>
        </div>
    </nav>
</template>

<script>
// Helper para iconos (puedes usar Heroicons o cualquier librería)
function getIcon(iconName) {
    const icons = {
        'HomeIcon': '🏠',
        'UsersIcon': '👥',
        'BriefcaseIcon': '💼',
        'TruckIcon': '🚚',
        'PackageIcon': '📦',
        'WrenchIcon': '🔧',
        'DocumentTextIcon': '📄',
        'ClipboardListIcon': '📋',
        'ReceiptIcon': '🧾',
        'CashIcon': '💵',
        'DocumentIcon': '📃',
        'CreditCardIcon': '💳',
        'ChartBarIcon': '📊',
        'ShieldCheckIcon': '🛡️',
        'UserGroupIcon': '👨‍👩‍👧‍👦',
        'CogIcon': '⚙️',
    }
    return icons[iconName] || '📌'
}
</script>

<style scoped>
.sidebar {
    width: 250px;
    background: #1f2937;
    color: white;
    padding: 1rem;
    height: 100vh;
    overflow-y: auto;
}

.nav-category {
    margin-bottom: 1.5rem;
}

.category-title {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #9ca3af;
    margin-bottom: 0.5rem;
    padding: 0 0.75rem;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: 0.5rem;
    color: #d1d5db;
    text-decoration: none;
    transition: all 0.2s;
}

.nav-item:hover {
    background: #374151;
    color: white;
}

.nav-item.active {
    background: #3b82f6;
    color: white;
}

.icon {
    font-size: 1.25rem;
}
</style>
