<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps({
    rol: Object,
    modulos: Array,
    rolePermissions: Array
})

const form = useForm({
    nombre: props.rol.nombre,
    descripcion: props.rol.descripcion || '',
    permisos: props.rolePermissions || []
})

// Mapeo visual de módulos
const modulosMapping = {
    'clientes': { label: 'Gestión de Clientes', icon: 'users' },
    'roles': { label: 'Roles y Permisos', icon: 'shield-check', accent: true },
    'usuarios': { label: 'Usuarios del Sistema', icon: 'user-cog' },
    'configuracion': { label: 'Configuración Global', icon: 'settings' }
}

const getModuloInfo = (slug) => {
    return modulosMapping[slug] || { label: slug.charAt(0).toUpperCase() + slug.slice(1), icon: 'box' }
}

// Lógica de Selección de Permisos
const togglePermiso = (permisoId) => {
    const index = form.permisos.indexOf(permisoId)
    if (index === -1) {
        form.permisos.push(permisoId)
    } else {
        form.permisos.splice(index, 1)
    }
}

const toggleModulo = (moduloNombre) => {
    const modulo = props.modulos.find(m => m.nombre === moduloNombre)
    if (!modulo) return

    const permisoIds = modulo.permisos.map(p => p.id)
    const todosSeleccionados = permisoIds.every(id => form.permisos.includes(id))

    if (todosSeleccionados) {
        form.permisos = form.permisos.filter(id => !permisoIds.includes(id))
    } else {
        // Agregar los que faltan
        const nuevos = permisoIds.filter(id => !form.permisos.includes(id))
        form.permisos.push(...nuevos)
    }
}

const isModuloSelected = (moduloNombre) => {
    const modulo = props.modulos.find(m => m.nombre === moduloNombre)
    if (!modulo) return false
    return modulo.permisos.every(p => form.permisos.includes(p.id))
}

const submit = () => {
    form.put(`/rols/${props.rol.uuid}`, {
        preserveScroll: true,
    })
}

const isPermisoSelected = (id) => form.permisos.includes(id)

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Roles', href: '/rols' },
    { title: 'Editar Rol', href: '#' },
]
</script>

<template>
    <Head title="Editar Rol" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="form-container">
            <div class="header">
                <div>
                    <h1>Editar Rol: {{ rol.nombre }}</h1>
                    <p class="subtitle">Modifica los permisos y accesos de este perfil</p>
                </div>
                <div class="actions">
                    <Link href="/rols" class="btn btn-secondary">Cancelar</Link>
                    <button @click="submit" class="btn btn-primary" :disabled="form.processing">
                        <span v-if="form.processing">Guardando...</span>
                        <span v-else>Actualizar Rol</span>
                    </button>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid-layout">
                <!-- Sidebar: Info Básica -->
                <div class="sidebar">
                    <div class="card">
                        <div class="card-header">
                            <h3>Información Básica</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nombre del Rol <span class="required">*</span></label>
                                <input
                                    v-model="form.nombre"
                                    type="text"
                                    class="input-control"
                                    placeholder="Ej: Supervisor de Ventas"
                                >
                                <span v-if="form.errors.nombre" class="error-msg">{{ form.errors.nombre }}</span>
                            </div>

                            <div class="form-group">
                                <label>Descripción</label>
                                <textarea
                                    v-model="form.descripcion"
                                    class="input-control"
                                    rows="3"
                                    placeholder="Breve descripción del rol..."
                                ></textarea>
                                <span v-if="form.errors.descripcion" class="error-msg">{{ form.errors.descripcion }}</span>
                            </div>

                            <div class="form-group">
                                <label>Total Permisos</label>
                                <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">
                                    {{ form.permisos.length }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content: Permisos -->
                <div class="main-content">
                    <div class="permisos-container">
                        <div v-for="modulo in modulos" :key="modulo.nombre" class="modulo-group">
                            <div class="modulo-header">
                                <div class="modulo-title">
                                    {{ getModuloInfo(modulo.nombre).label }}
                                </div>
                                <div class="switch">
                                    <label class="label-text" style="font-size: 0.7rem; color: var(--muted-foreground); margin-right: 0.25rem;">Todo</label>
                                    <input 
                                        type="checkbox" 
                                        :checked="isModuloSelected(modulo.nombre)" 
                                        @change="toggleModulo(modulo.nombre)"
                                        :id="'switch-' + modulo.nombre"
                                    >
                                    <label :for="'switch-' + modulo.nombre" class="slider"></label>
                                </div>
                            </div>
                            
                            <div class="permisos-grid">
                                <button 
                                    v-for="permiso in modulo.permisos" 
                                    :key="permiso.id"
                                    type="button"
                                    class="permiso-key"
                                    :class="{ 'active': isPermisoSelected(permiso.id) }"
                                    @click="togglePermiso(permiso.id)"
                                >
                                    <div class="custom-checkbox">
                                        <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </div>
                                    <span class="permiso-label">{{ permiso.descripcion || `${permiso.modulo}.${permiso.accion}` }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Ultra Compact Styles */
.form-container {
    max-width: 100%;
    margin: 0;
    padding: 0.5rem;
}

/* Header */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border);
}

.header h1 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--foreground);
    margin: 0;
    line-height: 1.1;
}

.subtitle {
    color: var(--muted-foreground);
    font-size: 0.75rem;
    margin: 0;
}

.actions {
    display: flex;
    gap: 0.5rem;
}

/* Base Layout */
.grid-layout {
    display: grid;
    grid-template-columns: 260px 1fr; /* Sidebar muy estrecha */
    gap: 0.75rem;
    align-items: start;
}

/* Cards */
.card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
}

.card-header {
    padding: 0.4rem 0.75rem;
    background: var(--muted);
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h3 {
    margin: 0;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--foreground);
    text-transform: uppercase;
    letter-spacing: 0.02em;
}

.card-body {
    padding: 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

/* Inputs Compactos */
.form-group label {
    display: block;
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--muted-foreground);
    margin-bottom: 0.15rem;
    text-transform: uppercase;
}

.required { color: var(--destructive); }

.input-control {
    width: 100%;
    padding: 0.35rem 0.6rem;
    border: 1px solid var(--input);
    background: var(--background);
    color: var(--foreground);
    border-radius: 4px;
    font-size: 0.8rem;
    transition: box-shadow 0.1s;
}

.input-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 1px var(--primary);
}

textarea.input-control {
    min-height: 60px;
    resize: vertical;
}

.error-msg {
    color: var(--destructive);
    font-size: 0.7rem;
    margin-top: 0.1rem;
    line-height: 1;
}

/* Matriz de Permisos Compacta */
.permisos-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 0.75rem;
}

.modulo-group {
    background: var(--background);
    border: 1px solid var(--border);
    border-radius: 6px;
    overflow: hidden;
}

.modulo-header {
    padding: 0.3rem 0.5rem;
    background: var(--muted);
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modulo-title {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-weight: 600;
    font-size: 0.75rem;
}

.permisos-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* 2 columnas forzadas por módulo */
    gap: 1px;
    background: var(--border); /* Grid effect */
}

.permiso-key {
    background: var(--card);
    padding: 0.35rem 0.5rem;
    cursor: pointer;
    transition: all 0.1s;
    user-select: none;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    position: relative;
    border: none;
    width: 100%;
    text-align: left;
}

.permiso-key:hover {
    background: var(--muted);
}

.permiso-key.active {
    background: rgba(var(--primary-rgb), 0.05); /* Muy suave */
    color: var(--primary);
}

/* Indicador lateral de activo */
.permiso-key.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 3px;
    background: var(--primary);
}

/* Checkbox personalizado minimalista */
.custom-checkbox {
    width: 12px;
    height: 12px;
    border: 1px solid var(--input);
    border-radius: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.1s;
    background: var(--background);
    flex-shrink: 0;
}

.permiso-key.active .custom-checkbox {
    background: var(--primary);
    border-color: var(--primary);
}

.check-icon {
    color: white;
    width: 8px;
    height: 8px;
    display: none;
}

.permiso-key.active .check-icon {
    display: block;
}

.permiso-label {
    font-size: 0.7rem;
    font-weight: 500;
    flex: 1;
}

/* Switch Compacto */
.switch {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.slider {
    position: relative;
    width: 28px;
    height: 16px;
    background: var(--input);
    border-radius: 99px;
    transition: .2s;
    flex-shrink: 0;
}

.switch input { display: none; }

.slider:before {
    content: "";
    position: absolute;
    height: 12px;
    width: 12px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    border-radius: 50%;
    transition: .2s;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.switch input:checked + .slider { background-color: #10b981; }
.switch input:checked + .slider:before { transform: translateX(12px); }

.label-text {
    font-size: 0.75rem;
    font-weight: 500;
}

/* Botones */
.btn {
    padding: 0.4rem 0.85rem;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.8rem;
    border: none;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    height: 30px;
}

.btn-primary { background: var(--primary); color: var(--primary-foreground); }
.btn-primary:hover { opacity: 0.9; }

.btn-secondary { background: var(--secondary); color: var(--secondary-foreground); border: 1px solid var(--border); }
.btn-secondary:hover { background: var(--border); }

/* Responsive */
@media (max-width: 1024px) {
    .grid-layout { grid-template-columns: 1fr; gap: 1rem; }
    .permisos-container { grid-template-columns: 1fr; }
}
</style>
