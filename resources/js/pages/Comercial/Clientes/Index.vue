<script setup>
import { ref, computed } from 'vue'
import { Link, router, Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ConfirmationModal from '@/components/ConfirmationModal.vue'

const props = defineProps({
    clientes: Object, // Paginado
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
        router.get('/clientes', { search: searchQuery.value }, { preserveState: true, preserveScroll: true })
    }, 300)
}

const confirmDelete = (cliente) => {
    modalConfig.value = {
        title: 'Eliminar Cliente',
        message: `¿Estás seguro de que deseas eliminar a "${cliente.razon_social}"? Esta acción se puede revertir si se cuenta con historial.`,
        confirmText: 'Sí, eliminar',
        type: 'danger',
        target: cliente
    }
    showModal.value = true
}

const onDeleteConfirm = () => {
    const cliente = modalConfig.value.target
    if (cliente) {
        router.delete(`/clientes/${cliente.uuid}`, {
            preserveScroll: true,
            onFinish: () => showModal.value = false
        })
    }
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Comercial', href: '#' },
    { title: 'Clientes', href: '/clientes' },
]
</script>

<template>
    <Head title="Gestión de Clientes" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-container">
            <div class="header">
                <div>
                    <h1>Clientes</h1>
                    <p class="subtitle">Gestiona tu cartera de clientes y contactos comerciales</p>
                </div>
                <Link href="/clientes/create" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Nuevo Cliente
                </Link>
            </div>

            <div class="controls-bar">
                <div class="search-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input 
                        v-model="searchQuery"
                        @input="onSearch"
                        type="text" 
                        placeholder="Buscar por nombre, RUC o email..."
                    >
                </div>
            </div>

            <!-- Grid de Clientes -->
            <div class="grid-container">
                <div 
                    v-for="cliente in clientes.data" 
                    :key="cliente.id"
                    class="card-cliente"
                >
                    <div class="card-header-styled">
                        <div class="status-indicator" :class="cliente.estado ? 'active' : 'inactive'"></div>
                        <span class="doc-badge">{{ cliente.tipo_documento }}: {{ cliente.numero_documento }}</span>
                    </div>

                    <div class="cliente-info">
                        <h3>{{ cliente.razon_social }}</h3>
                        <div class="detail-row" v-if="cliente.email">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            <span>{{ cliente.email }}</span>
                        </div>
                        <div class="detail-row" v-if="cliente.telefono">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            <span>{{ cliente.telefono }}</span>
                        </div>
                    </div>

                    <div class="card-actions">
                        <Link :href="`/clientes/${cliente.uuid}/edit`" class="action-btn edit" title="Editar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </Link>
                        <button @click="confirmDelete(cliente)" class="action-btn delete" title="Eliminar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Paginación Simple -->
            <div v-if="clientes.links && clientes.data.length > 0" class="pagination">
                <Link 
                    v-for="(link, key) in clientes.links" 
                    :key="key" 
                    :href="link.url || '#'" 
                    class="page-link" 
                    :class="{ 'active': link.active, 'disabled': !link.url }"
                    v-html="link.label"
                />
            </div>

            <div v-if="clientes.data.length === 0" class="empty-state">
                <p>No se encontraron clientes.</p>
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
    padding: 0.5rem; /* Máxima anchura */
    max-width: 100%;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem; /* Margen cabecera reducido */
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border);
}

.header h1 {
    font-size: 1.25rem; /* Título igual que formularios */
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
    padding: 0.4rem 0.5rem 0.4rem 2rem; /* Input search delgado */
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
    padding: 0.4rem 0.8rem; /* Botón delgado */
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
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); /* Tarjetas más estrechas si es posible */
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.card-cliente {
    background: linear-gradient(to bottom, #ffffff, #f9fafb);
    border: 1px solid var(--border);
    border-radius: 8px; /* Radio consistente */
    padding: 0.75rem; /* Padding interno mínimo */
    display: flex;
    flex-direction: column;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

@media (prefers-color-scheme: dark) {
    .card-cliente {
        background: linear-gradient(to bottom, #1f2937, #111827);
    }
}

.card-cliente:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border-color: var(--primary);
}

/* Borde superior decorativo más fino */
.card-cliente::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, var(--primary), var(--chart-2));
    opacity: 0.7;
}

.card-header-styled {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.status-indicator {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}
.status-indicator.active { background: #10b981; }
.status-indicator.inactive { background: #ef4444; }

.doc-badge {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.1rem 0.4rem;
    background: var(--muted);
    border-radius: 3px;
    color: var(--foreground);
    opacity: 0.8;
}

.cliente-info h3 {
    font-size: 0.9rem; /* Nombre más compacto */
    font-weight: 700;
    color: var(--foreground);
    margin: 0 0 0.4rem 0;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.detail-row {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.75rem;
    color: var(--muted-foreground);
    margin-bottom: 0.2rem;
}

.detail-row svg {
    width: 12px;
    height: 12px;
    color: var(--primary);
    opacity: 0.7;
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
    width: 24px; /* Botones de acción diminutos */
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

.action-btn:hover {
    background: var(--muted);
    color: var(--foreground);
}

.action-btn.edit:hover { background: rgba(var(--primary-rgb), 0.1); color: var(--primary); }
.action-btn.delete:hover { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

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
