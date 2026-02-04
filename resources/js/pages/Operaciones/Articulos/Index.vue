<script setup>
import { ref } from 'vue'
import { Link, router, Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ConfirmationModal from '@/components/ConfirmationModal.vue'

const props = defineProps({
    articulos: Object,
    filters: Object,
})

const searchQuery = ref(props.filters.search || '')
const estadoFilter = ref(props.filters.estado || '')
const showModal = ref(false)
const modalConfig = ref({
    title: '',
    message: '',
    confirmText: 'Eliminar',
    type: 'danger',
    target: null
})

const estadosOptions = [
    { value: '', label: 'Todos los estados' },
    { value: 'activo', label: 'Activo' },
    { value: 'inactivo', label: 'Inactivo' },
]

// Debounce para búsqueda
let timeout = null
const onSearch = () => {
    clearTimeout(timeout)
    timeout = setTimeout(() => {
        router.get('/articulos', {
            search: searchQuery.value,
            estado: estadoFilter.value
        }, {
            preserveState: true,
            preserveScroll: true
        })
    }, 300)
}

const onEstadoChange = () => {
    router.get('/articulos', {
        search: searchQuery.value,
        estado: estadoFilter.value
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

const confirmDelete = (articulo) => {
    modalConfig.value = {
        title: 'Eliminar Artículo',
        message: `¿Estás seguro de que deseas eliminar el artículo "${articulo.descripcion}"? Esta acción se puede revertir si se cuenta con historial.`,
        confirmText: 'Sí, eliminar',
        type: 'danger',
        target: articulo
    }
    showModal.value = true
}

const onDeleteConfirm = () => {
    const articulo = modalConfig.value.target
    if (articulo) {
        router.delete(`/articulos/${articulo.uuid}`, {
            preserveScroll: true,
            onFinish: () => showModal.value = false
        })
    }
}

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('es-PE', {
        style: 'currency',
        currency: 'PEN',
        minimumFractionDigits: 2
    }).format(amount)
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Operaciones', href: '#' },
    { title: 'Artículos', href: '/articulos' },
]
</script>

<template>
    <Head title="Gestión de Artículos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-container">
            <div class="header">
                <div>
                    <h1>Artículos</h1>
                    <p class="subtitle">Gestiona tu inventario de productos y materiales</p>
                </div>
                <Link href="/articulos/create" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Nuevo Artículo
                </Link>
            </div>

            <div class="controls-bar">
                <div class="search-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input
                        v-model="searchQuery"
                        @input="onSearch"
                        type="text"
                        placeholder="Buscar por código, descripción o proveedor..."
                    >
                </div>
                <div class="filter-box">
                    <select v-model="estadoFilter" @change="onEstadoChange" class="filter-select">
                        <option v-for="option in estadosOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Grid de Artículos -->
            <div class="grid-container">
                <div
                    v-for="articulo in articulos.data"
                    :key="articulo.id"
                    class="card-articulo"
                >
                    <div class="card-header-styled">
                        <span class="codigo-badge">{{ articulo.codigo }}</span>
                        <div class="status-indicator" :class="articulo.estado === 'activo' ? 'active' : 'inactive'"></div>
                    </div>

                    <div class="articulo-info">
                        <h3>{{ articulo.descripcion }}</h3>

                        <div class="detail-row" v-if="articulo.proveedor">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7h-9"></path><path d="M14 17H5"></path><circle cx="17" cy="17" r="3"></circle><circle cx="7" cy="7" r="3"></circle></svg>
                            <span>{{ articulo.proveedor.razon_social }}</span>
                        </div>

                        <div class="detail-row">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                            <span>{{ articulo.unidad_medida }}</span>
                        </div>

                        <div class="detail-row">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            <span>Stock: {{ articulo.stock }}</span>
                        </div>
                    </div>

                    <div class="precios-section">
                        <div class="precio-item">
                            <span class="precio-label">Compra</span>
                            <span class="precio-value compra">{{ formatCurrency(articulo.precio_compra) }}</span>
                        </div>
                        <div class="precio-item">
                            <span class="precio-label">Venta</span>
                            <span class="precio-value venta">{{ formatCurrency(articulo.precio_venta) }}</span>
                        </div>
                    </div>

                    <div class="card-actions">
                        <Link :href="`/articulos/${articulo.uuid}/edit`" class="action-btn edit" title="Editar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </Link>
                        <button @click="confirmDelete(articulo)" class="action-btn delete" title="Eliminar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Paginación Simple -->
            <div v-if="articulos.links && articulos.data.length > 0" class="pagination">
                <Link
                    v-for="(link, key) in articulos.links"
                    :key="key"
                    :href="link.url || '#'"
                    class="page-link"
                    :class="{ 'active': link.active, 'disabled': !link.url }"
                    v-html="link.label"
                />
            </div>

            <div v-if="articulos.data.length === 0" class="empty-state">
                <p>No se encontraron artículos.</p>
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
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.search-box {
    position: relative;
    flex: 1;
    min-width: 200px;
    max-width: 400px;
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

.filter-box {
    min-width: 180px;
}

.filter-select {
    width: 100%;
    padding: 0.4rem 0.5rem;
    border: 1px solid var(--border);
    background: var(--background);
    color: var(--foreground);
    border-radius: 4px;
    font-size: 0.8rem;
    cursor: pointer;
}

.filter-select:focus {
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

.card-articulo {
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
    .card-articulo {
        background: linear-gradient(to bottom, #1f2937, #111827);
    }
}

.card-articulo:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border-color: var(--primary);
}

/* Borde superior decorativo más fino */
.card-articulo::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, var(--primary), var(--chart-4));
    opacity: 0.7;
}

.card-header-styled {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.codigo-badge {
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.15rem 0.5rem;
    background: var(--muted);
    border-radius: 3px;
    color: var(--foreground);
    font-family: monospace;
}

.status-indicator {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}
.status-indicator.active { background: #10b981; }
.status-indicator.inactive { background: #ef4444; }

.articulo-info h3 {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--foreground);
    margin: 0 0 0.4rem 0;
    line-height: 1.2;
}

.detail-row {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.7rem;
    color: var(--muted-foreground);
    margin-bottom: 0.2rem;
}

.detail-row svg {
    width: 12px;
    height: 12px;
    color: var(--primary);
    opacity: 0.7;
    flex-shrink: 0;
}

.precios-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
    margin: 0.5rem 0;
    padding: 0.5rem;
    background: var(--muted);
    border-radius: 4px;
}

.precio-item {
    display: flex;
    flex-direction: column;
    gap: 0.1rem;
}

.precio-label {
    font-size: 0.65rem;
    color: var(--muted-foreground);
    font-weight: 600;
    text-transform: uppercase;
}

.precio-value {
    font-size: 0.85rem;
    font-weight: 700;
    font-family: monospace;
}

.precio-value.compra {
    color: #f59e0b;
}

.precio-value.venta {
    color: var(--primary);
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
    .controls-bar { flex-direction: column; }
    .search-box { max-width: 100%; }
    .grid-container { grid-template-columns: 1fr; }
}
</style>
