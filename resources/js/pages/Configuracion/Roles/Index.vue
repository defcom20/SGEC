<script setup>
import { ref, computed } from 'vue'
import { Link, router, Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'

const props = defineProps({
    roles: Object, // Paginado
    filters: Object,
})

const searchQuery = ref(props.filters.search || '')
const showModal = ref(false)
const modalConfig = ref({
    title: '',
    message: '',
    confirmText: 'Eliminar',
    type: 'danger',
    target: null
})

// Debounce para búsqueda
let timeout = null
const onSearch = () => {
    clearTimeout(timeout)
    timeout = setTimeout(() => {
        router.get('/rols', { search: searchQuery.value }, { preserveState: true, preserveScroll: true })
    }, 300)
}

const confirmDelete = (rol) => {
    if (rol.users_count > 0) {
        return
    }

    modalConfig.value = {
        title: 'Eliminar Rol',
        message: `¿Estás seguro de que deseas eliminar el rol "${rol.nombre}"? Esta acción no se puede deshacer.`,
        confirmText: 'Sí, eliminar',
        type: 'danger',
        target: rol
    }
    showModal.value = true
}

const onDeleteConfirm = () => {
    const rol = modalConfig.value.target
    if (rol) {
        router.delete(`/rols/${rol.uuid}`, {
            preserveScroll: true,
            onFinish: () => showModal.value = false
        })
    }
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Configuración', href: '#' },
    { title: 'Roles', href: '/rols' },
]
</script>

<template>
    <Head title="Gestión de Roles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-container">
            <div class="header">
                <div>
                    <h1>Roles y Permisos</h1>
                    <p class="subtitle">Gestiona los roles del sistema y sus permisos asociados</p>
                </div>
                <Link href="/rols/create" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Nuevo Rol
                </Link>
            </div>

            <div class="controls-bar">
                <div class="search-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input
                        v-model="searchQuery"
                        @input="onSearch"
                        type="text"
                        placeholder="Buscar roles..."
                    >
                </div>
            </div>

            <!-- Grid de Roles -->
            <div class="grid-container">
                <div
                    v-for="rol in roles.data"
                    :key="rol.id"
                    class="card-rol"
                >
                    <div class="card-header-styled">
                        <div class="users-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            {{ rol.users_count }} {{ rol.users_count === 1 ? 'usuario' : 'usuarios' }}
                        </div>
                    </div>

                    <div class="rol-info">
                        <h3>{{ rol.nombre }}</h3>
                        <p class="descripcion" v-if="rol.descripcion">{{ rol.descripcion }}</p>
                        <p class="descripcion" v-else>Sin descripción</p>
                    </div>

                    <div class="rol-stats">
                        <div class="stat-item">
                            <span class="stat-label">Permisos Habilitados</span>
                            <span class="stat-value">{{ rol.permisos?.length || 0 }}</span>
                        </div>
                    </div>

                    <div class="rol-permisos">
                        <div class="permisos-list">
                            <span
                                v-for="permiso in rol.permisos?.slice(0, 4)"
                                :key="permiso.id"
                                class="permiso-tag"
                            >
                                {{ permiso.modulo }}.{{ permiso.accion }}
                            </span>
                            <span
                                v-if="rol.permisos?.length > 4"
                                class="permiso-tag more"
                            >
                                +{{ rol.permisos.length - 4 }}
                            </span>
                        </div>
                    </div>

                    <div class="card-actions">
                        <Link :href="`/rols/${rol.uuid}/edit`" class="action-btn edit" title="Editar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </Link>
                        <button
                            @click="confirmDelete(rol)"
                            class="action-btn delete"
                            :disabled="rol.users_count > 0"
                            :title="rol.users_count > 0 ? 'No se puede eliminar (tiene usuarios asignados)' : 'Eliminar'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Paginación Simple -->
            <div v-if="roles.links && roles.data.length > 0" class="pagination">
                <Link
                    v-for="(link, key) in roles.links"
                    :key="key"
                    :href="link.url || '#'"
                    class="page-link"
                    :class="{ 'active': link.active, 'disabled': !link.url }"
                    v-html="link.label"
                />
            </div>

            <div v-if="roles.data.length === 0" class="empty-state">
                <p>No se encontraron roles.</p>
            </div>
        </div>

        <ConfirmationModal
            :show="showModal"
            v-bind="modalConfig"
            @close="showModal = false"
            @confirm="onDeleteConfirm"
        />
    </AppLayout>
</template>

<style scoped>
/* Estilos Ultra Compactos Globales */
.page-container {
    padding: 0.5rem;
    max-width: 100%;
}

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

.controls-bar {
    margin-bottom: 0.75rem;
}

.search-box {
    position: relative;
    max-width: 300px;
}

.search-box svg {
    position: absolute;
    left: 0.5rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--muted-foreground);
    width: 14px;
    height: 14px;
}

.search-box input {
    width: 100%;
    padding: 0.4rem 0.5rem 0.4rem 2rem;
    border: 1px solid var(--border);
    background: var(--background);
    color: var(--foreground);
    border-radius: 4px;
    font-size: 0.8rem;
    transition: all 0.2s;
}

.search-box input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 1px var(--primary);
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.4rem 0.8rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
    height: 30px;
}

.btn-primary {
    background: var(--primary);
    color: var(--primary-foreground);
}

.btn-primary:hover {
    opacity: 0.9;
}

/* Grid System Denso */
.grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.card-rol {
    background: linear-gradient(to bottom, #ffffff, #f9fafb);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 0.75rem;
    display: flex;
    flex-direction: column;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

@media (prefers-color-scheme: dark) {
    .card-rol {
        background: linear-gradient(to bottom, #1f2937, #111827);
    }
}

.card-rol:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border-color: var(--primary);
}

/* Borde superior decorativo más fino */
.card-rol::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, var(--primary), #a855f7);
    opacity: 0.7;
}

.card-header-styled {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    margin-bottom: 0.5rem;
}

.users-badge {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.1rem 0.4rem;
    background: var(--muted);
    border-radius: 3px;
    color: var(--foreground);
    opacity: 0.8;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.users-badge svg {
    width: 12px;
    height: 12px;
}

.rol-info h3 {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--foreground);
    margin: 0 0 0.3rem 0;
    line-height: 1.2;
}

.descripcion {
    font-size: 0.7rem;
    color: var(--muted-foreground);
    margin: 0 0 0.5rem 0;
    line-height: 1.3;
}

.rol-stats {
    margin-bottom: 0.5rem;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.3rem 0.5rem;
    background: var(--muted);
    border-radius: 4px;
}

.stat-label {
    font-size: 0.65rem;
    color: var(--muted-foreground);
    font-weight: 600;
    text-transform: uppercase;
}

.stat-value {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--primary);
}

.rol-permisos {
    margin-bottom: 0.5rem;
}

.permisos-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.3rem;
}

.permiso-tag {
    font-size: 0.65rem;
    padding: 0.15rem 0.4rem;
    background: var(--muted);
    color: var(--foreground);
    border-radius: 3px;
    font-weight: 500;
}

.permiso-tag.more {
    background: rgba(var(--primary-rgb), 0.1);
    color: var(--primary);
    font-weight: 600;
}

.card-actions {
    margin-top: auto;
    padding-top: 0.5rem;
    border-top: 1px dashed var(--border);
    display: flex;
    justify-content: flex-end;
    gap: 0.25rem;
}

.action-btn {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    color: var(--muted-foreground);
    transition: all 0.1s;
    background: transparent;
    border: none;
    cursor: pointer;
}

.action-btn svg {
    width: 14px;
    height: 14px;
}

.action-btn:hover:not(:disabled) {
    background: var(--muted);
    color: var(--foreground);
}

.action-btn.edit:hover { background: rgba(var(--primary-rgb), 0.1); color: var(--primary); }
.action-btn.delete:hover:not(:disabled) { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

.action-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

/* Pagination Compacta */
.pagination {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.25rem;
    margin-top: 1rem;
}

.page-link {
    padding: 0.25rem 0.6rem;
    border-radius: 4px;
    border: 1px solid var(--border);
    background: var(--card);
    color: var(--foreground);
    font-size: 0.75rem;
    text-decoration: none;
    transition: all 0.2s;
}

.page-link:hover:not(.disabled) {
    border-color: var(--primary);
    color: var(--primary);
}

.page-link.active {
    background: var(--primary);
    color: var(--primary-foreground);
    border-color: var(--primary);
}

.page-link.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.empty-state {
    text-align: center;
    padding: 2rem;
    color: var(--muted-foreground);
    font-size: 0.85rem;
}

@media (max-width: 640px) {
    .header { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
    .search-box { max-width: 100%; }
    .grid-container { grid-template-columns: 1fr; }
}
</style>
