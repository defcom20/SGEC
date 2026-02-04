<script setup>
import { ref, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { Link, router, Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ConfirmationModal from '@/components/ConfirmationModal.vue'

const props = defineProps({
    facturas: Object,
    filters: Object,
})

const searchQuery = ref(props.filters?.search || '')
const showModal = ref(false)
const itemToDelete = ref(null)

// Watch search query and debounce the router visit
const onSearch = useDebounceFn((value) => {
    router.get('/factura-clientes', { search: value }, {
        preserveState: true,
        replace: true,
    })
}, 300)

watch(searchQuery, (newValue) => {
    onSearch(newValue)
})

const confirmDelete = (item) => {
    itemToDelete.value = item
    showModal.value = true
}

const onDeleteConfirm = () => {
    if (itemToDelete.value) {
        router.delete(`/factura-clientes/${itemToDelete.value.uuid}`, {
            onSuccess: () => {
                showModal.value = false
                itemToDelete.value = null
            }
        })
    }
}

const modalConfig = {
    title: '¿Eliminar Factura?',
    message: 'Esta acción eliminará la factura permanentemente.',
    confirmText: 'Eliminar',
    cancelText: 'Cancelar',
    type: 'danger'
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Facturas Clientes', href: '#' },
]

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-PE', { style: 'currency', currency: 'PEN' }).format(value)
}

const formatDate = (date) => {
    if (!date) return ''
    return new Date(date).toLocaleDateString('es-PE')
}
</script>

<template>
    <Head title="Facturas de Clientes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-container">
            <div class="header">
                <div>
                    <h1>Facturas Clientes</h1>
                    <p class="subtitle">Gestión de comprobantes emitidos</p>
                </div>
                <Link href="/factura-clientes/create" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Nueva Factura
                </Link>
            </div>

            <div class="controls-bar">
                <div class="search-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Buscar por número o cliente..."
                    >
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Comprobante</th>
                            <th>Emisión</th>
                            <th>Cliente</th>
                            <th>Referencia</th>
                            <th class="text-right">Total</th>
                            <th class="text-right">Pendiente</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="factura in facturas.data" :key="factura.id">
                            <td class="font-medium">
                                {{ factura.serie }}-{{ factura.numero_factura }}
                            </td>
                            <td>{{ formatDate(factura.fecha_emision) }}</td>
                            <td>
                                <div class="client-info">
                                    <span class="client-name">{{ factura.cliente?.razon_social }}</span>
                                    <span class="client-doc">{{ factura.cliente?.numero_documento }}</span>
                                </div>
                            </td>
                            <td>
                                <span v-if="factura.presupuesto" class="budget-badge">
                                    {{ factura.presupuesto.numero_presupuesto }}
                                </span>
                            </td>
                            <td class="text-right font-bold">{{ formatCurrency(factura.total) }}</td>
                            <td class="text-right">
                                <span :class="{'text-danger': factura.monto_pendiente > 0, 'text-success': factura.monto_pendiente == 0}">
                                    {{ formatCurrency(factura.monto_pendiente) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge" :class="factura.estado">
                                    {{ factura.estado.replace('_', ' ') }}
                                </span>
                            </td>
                            <td class="text-right actions-cell">
                                <Link :href="`/factura-clientes/${factura.uuid}/edit`" class="action-btn edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </Link>
                                <button @click="confirmDelete(factura)" class="action-btn delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="facturas.data.length === 0">
                            <td colspan="8" class="empty-state">No hay facturas registradas.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="facturas.links && facturas.data.length > 0" class="pagination">
                <Link
                    v-for="(link, key) in facturas.links"
                    :key="key"
                    :href="link.url || '#'"
                    class="page-link"
                    :class="{ 'active': link.active, 'disabled': !link.url }"
                    v-html="link.label"
                />
            </div>
        </div>

        <ConfirmationModal
            :show="showModal"
            :title="modalConfig.title"
            :message="modalConfig.message"
            :confirmText="modalConfig.confirmText"
            :cancelText="modalConfig.cancelText"
            @close="showModal = false"
            @confirm="onDeleteConfirm"
        />
    </AppLayout>
</template>

<style scoped>
.page-container { padding: 0.5rem; }
.header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; }
.header h1 { font-size: 1.25rem; font-weight: 700; color: var(--foreground); margin: 0; }
.subtitle { color: var(--muted-foreground); font-size: 0.75rem; margin: 0; }
.controls-bar { margin-bottom: 0.75rem; }
.search-box { position: relative; max-width: 300px; }
.search-box svg { position: absolute; left: 0.5rem; top: 50%; transform: translateY(-50%); color: var(--muted-foreground); width: 14px; height: 14px; }
.search-box input { width: 100%; padding: 0.4rem 0.5rem 0.4rem 2rem; border: 1px solid var(--border); background: var(--background); color: var(--foreground); border-radius: 4px; font-size: 0.8rem; }
.search-box input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 1px var(--primary); }

.table-container { overflow-x: auto; border: 1px solid var(--border); border-radius: 6px; }
.data-table { width: 100%; border-collapse: collapse; font-size: 0.8rem; }
.data-table th { text-align: left; padding: 0.5rem 0.75rem; background: var(--muted); color: var(--muted-foreground); font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border-bottom: 1px solid var(--border); }
.data-table td { padding: 0.6rem 0.75rem; border-bottom: 1px solid var(--border); color: var(--foreground); }
.data-table tbody tr:hover { background: rgba(0,0,0,0.02); }

.text-right { text-align: right; }
.font-medium { font-weight: 500; }
.font-bold { font-weight: 700; }
.text-danger { color: #ef4444; }
.text-success { color: #10b981; }

.client-info { display: flex; flex-direction: column; }
.client-name { font-weight: 500; }
.client-doc { font-size: 0.7rem; color: var(--muted-foreground); }

.budget-badge { background: var(--muted); padding: 0.1rem 0.4rem; border-radius: 4px; font-size: 0.7rem; }

.status-badge { display: inline-block; padding: 0.15rem 0.5rem; border-radius: 999px; font-size: 0.65rem; font-weight: 600; text-transform: uppercase; }
.status-badge.pendiente { background: #fee2e2; color: #991b1b; }
.status-badge.pago_parcial { background: #fef3c7; color: #92400e; }
.status-badge.pagado_completo { background: #d1fae5; color: #065f46; }

.actions-cell { white-space: nowrap; }
.action-btn { display: inline-flex; width: 24px; height: 24px; align-items: center; justify-content: center; border-radius: 4px; color: var(--muted-foreground); border: none; background: transparent; cursor: pointer; }
.action-btn:hover { background: var(--muted); color: var(--foreground); }
.action-btn.edit:hover { background: rgba(var(--primary-rgb), 0.1); color: var(--primary); }
.action-btn.delete:hover { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

.pagination { display: flex; justify-content: center; gap: 0.25rem; margin-top: 1rem; }
.page-link { padding: 0.25rem 0.6rem; border-radius: 4px; border: 1px solid var(--border); background: var(--card); color: var(--foreground); font-size: 0.75rem; text-decoration: none; }
.page-link:hover:not(.disabled) { border-color: var(--primary); color: var(--primary); }
.page-link.active { background: var(--primary); color: var(--primary-foreground); border-color: var(--primary); }
.page-link.disabled { opacity: 0.5; cursor: not-allowed; }

.btn { padding: 0.4rem 0.8rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem; border: none; cursor: pointer; height: 30px; }
.btn-primary { background: var(--primary); color: var(--primary-foreground); }
.empty-state { text-align: center; padding: 2rem; color: var(--muted-foreground); }
</style>
