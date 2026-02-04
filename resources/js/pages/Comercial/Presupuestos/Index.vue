<script setup>
import { ref, computed } from 'vue'
import { Link, router, Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'

const props = defineProps({
    presupuestos: Object,
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
    { value: 'en_revision', label: 'En Revisión' },
    { value: 'aprobado', label: 'Aprobado' },
    { value: 'rechazado', label: 'Rechazado' },
    { value: 'vencido', label: 'Vencido' },
    { value: 'en_ejecucion', label: 'En Ejecución' },
    { value: 'finalizado', label: 'Finalizado' },
]

const estadoColors = {
    'en_revision': { bg: 'rgba(59, 130, 246, 0.1)', color: '#3b82f6', label: 'En Revisión' },
    'aprobado': { bg: 'rgba(16, 185, 129, 0.1)', color: '#10b981', label: 'Aprobado' },
    'rechazado': { bg: 'rgba(239, 68, 68, 0.1)', color: '#ef4444', label: 'Rechazado' },
    'vencido': { bg: 'rgba(156, 163, 175, 0.1)', color: '#9ca3af', label: 'Vencido' },
    'en_ejecucion': { bg: 'rgba(245, 158, 11, 0.1)', color: '#f59e0b', label: 'En Ejecución' },
    'finalizado': { bg: 'rgba(107, 114, 128, 0.1)', color: '#6b7280', label: 'Finalizado' },
}

// Debounce para búsqueda
let timeout = null
const onSearch = () => {
    clearTimeout(timeout)
    timeout = setTimeout(() => {
        router.get('/presupuestos', {
            search: searchQuery.value,
            estado: estadoFilter.value
        }, {
            preserveState: true,
            preserveScroll: true
        })
    }, 300)
}

const onEstadoChange = () => {
    router.get('/presupuestos', {
        search: searchQuery.value,
        estado: estadoFilter.value
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

const confirmDelete = (presupuesto) => {
    modalConfig.value = {
        title: 'Eliminar Presupuesto',
        message: `¿Estás seguro de que deseas eliminar el presupuesto "${presupuesto.numero_presupuesto}"? Esta acción se puede revertir si se cuenta con historial.`,
        confirmText: 'Sí, eliminar',
        type: 'danger',
        target: presupuesto
    }
    showModal.value = true
}

const onDeleteConfirm = () => {
    const presupuesto = modalConfig.value.target
    if (presupuesto) {
        router.delete(`/presupuestos/${presupuesto.uuid}`, {
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

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('es-PE', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Comercial', href: '#' },
    { title: 'Presupuestos', href: '/presupuestos' },
]
</script>

<template>
    <Head title="Gestión de Presupuestos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-container">
            <div class="header">
                <div>
                    <h1>Presupuestos</h1>
                    <p class="subtitle">Gestiona las cotizaciones y propuestas comerciales</p>
                </div>
                <Link href="/presupuestos/create" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Nuevo Presupuesto
                </Link>
            </div>

            <div class="controls-bar">
                <div class="search-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input
                        v-model="searchQuery"
                        @input="onSearch"
                        type="text"
                        placeholder="Buscar por número o cliente..."
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

            <!-- Grid de Presupuestos -->
            <div class="grid-container">
                <div
                    v-for="presupuesto in presupuestos.data"
                    :key="presupuesto.id"
                    class="card-presupuesto"
                >
                    <div class="card-header-styled">
                        <span class="numero-badge">{{ presupuesto.numero_presupuesto }}</span>
                        <span
                            class="estado-badge"
                            :style="{
                                background: estadoColors[presupuesto.estado]?.bg,
                                color: estadoColors[presupuesto.estado]?.color
                            }"
                        >
                            {{ estadoColors[presupuesto.estado]?.label }}
                        </span>
                    </div>

                    <div class="presupuesto-info">
                        <h3>{{ presupuesto.cliente?.nombre || 'Sin cliente' }}</h3>
                        <div class="detail-row">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <span>Emisión: {{ formatDate(presupuesto.fecha_emision) }}</span>
                        </div>
                        <div class="detail-row">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span>Vence: {{ formatDate(presupuesto.fecha_vencimiento) }}</span>
                        </div>
                        <div class="detail-row" v-if="presupuesto.supervisor">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <span>Supervisor: {{ presupuesto.supervisor.name }}</span>
                        </div>
                    </div>

                    <div class="presupuesto-monto">
                        <span class="monto-label">Total</span>
                        <span class="monto-value">{{ formatCurrency(presupuesto.total) }}</span>
                    </div>

                    <div class="card-actions">
                        <Link :href="`/presupuestos/${presupuesto.uuid}/edit`" class="action-btn edit" title="Editar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </Link>
                        <button @click="confirmDelete(presupuesto)" class="action-btn delete" title="Eliminar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Paginación Simple -->
            <div v-if="presupuestos.links && presupuestos.data.length > 0" class="pagination">
                <Link
                    v-for="(link, key) in presupuestos.links"
                    :key="key"
                    :href="link.url || '#'"
                    class="page-link"
                    :class="{ 'active': link.active, 'disabled': !link.url }"
                    v-html="link.label"
                />
            </div>

            <div v-if="presupuestos.data.length === 0" class="empty-state">
                <p>No se encontraron presupuestos.</p>
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
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.card-presupuesto {
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
    .card-presupuesto {
        background: linear-gradient(to bottom, #1f2937, #111827);
    }
}

.card-presupuesto:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border-color: var(--primary);
}

/* Borde superior decorativo más fino */
.card-presupuesto::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, var(--primary), var(--chart-1));
    opacity: 0.7;
}

.card-header-styled {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
    gap: 0.5rem;
}

.numero-badge {
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.15rem 0.5rem;
    background: var(--muted);
    border-radius: 3px;
    color: var(--foreground);
    font-family: monospace;
}

.estado-badge {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.15rem 0.5rem;
    border-radius: 3px;
    text-transform: uppercase;
}

.presupuesto-info h3 {
    font-size: 0.9rem;
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

.presupuesto-monto {
    margin: 0.5rem 0;
    padding: 0.5rem;
    background: var(--muted);
    border-radius: 4px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.monto-label {
    font-size: 0.65rem;
    color: var(--muted-foreground);
    font-weight: 600;
    text-transform: uppercase;
}

.monto-value {
    font-size: 1rem;
    font-weight: 700;
    color: var(--primary);
    font-family: monospace;
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
