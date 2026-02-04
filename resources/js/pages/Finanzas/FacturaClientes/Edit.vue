<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { watch } from 'vue'

const props = defineProps({
    factura: Object,
    clientes: Array,
    presupuestos: Array,
})

const form = useForm({
    numero_factura: props.factura.numero_factura,
    serie: props.factura.serie,
    fecha_emision: props.factura.fecha_emision,
    fecha_vencimiento: props.factura.fecha_vencimiento,
    cliente_id: props.factura.cliente_id,
    presupuesto_id: props.factura.presupuesto_id,
    base_imponible: props.factura.base_imponible,
    igv: props.factura.igv,
    descuento_porcentaje: props.factura.descuento_porcentaje || 0,
    descuento_descripcion: props.factura.descuento_descripcion || '',
    descuento_monto: props.factura.descuento_monto || 0,
    total: props.factura.total,
    porcentaje_detraccion: props.factura.porcentaje_detraccion || 0,
    monto_detraccion: props.factura.monto_detraccion || 0,
    estado: props.factura.estado,
    observaciones: props.factura.observaciones || '',
})

// Filtered budgets based on selected client
import { computed } from 'vue'

const filteredPresupuestos = computed(() => {
    if (!form.cliente_id) return []
    return props.presupuestos.filter(p => p.cliente_id === form.cliente_id)
})

watch(() => form.cliente_id, (newVal, oldVal) => {
    // Only reset if it's a user change (oldVal exists)
    if (oldVal !== undefined) {
        form.presupuesto_id = ''
    }
})

// Calculations - Same as Create
watch(() => form.base_imponible, (newVal) => {
    form.igv = parseFloat((newVal * 0.18).toFixed(2))
    calculateTotal()
})

watch(() => form.igv, () => calculateTotal())
watch(() => form.descuento_monto, () => calculateTotal())

watch(() => form.porcentaje_detraccion, (newVal) => {
    if (newVal > 0 && form.total > 0) {
        form.monto_detraccion = parseFloat((form.total * (newVal / 100)).toFixed(2))
    }
})

function calculateTotal() {
    const base = parseFloat(form.base_imponible || 0)
    const igv = parseFloat(form.igv || 0)
    const discount = parseFloat(form.descuento_monto || 0)
    form.total = parseFloat((base + igv - discount).toFixed(2))
    
    if (form.porcentaje_detraccion > 0) {
        form.monto_detraccion = parseFloat((form.total * (form.porcentaje_detraccion / 100)).toFixed(2))
    }
}

const submit = () => {
    form.put(`/factura-clientes/${props.factura.uuid}`, {
        preserveScroll: true,
    })
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Facturas Clientes', href: '/factura-clientes' },
    { title: 'Editar Factura', href: '#' },
]
</script>

<template>
    <Head title="Editar Factura Cliente" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="form-container">
            <div class="header">
                <div>
                    <h1>Editar Factura {{ props.factura.serie }}-{{ props.factura.numero_factura }}</h1>
                    <p class="subtitle">Modificar comprobante de venta</p>
                </div>
                <div class="actions">
                    <Link href="/factura-clientes" class="btn btn-secondary">Cancelar</Link>
                    <button @click="submit" class="btn btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Guardando...' : 'Actualizar Factura' }}
                    </button>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid-layout">
                <!-- Sidebar: Details -->
                <div class="sidebar">
                    <div class="card">
                        <div class="card-header">
                            <h3>Comprobante</h3>
                        </div>
                        <div class="card-body">
                            <div class="row-2">
                                <div class="form-group">
                                    <label>Serie</label>
                                    <input v-model="form.serie" type="text" class="input-control" placeholder="F001">
                                    <span v-if="form.errors.serie" class="error-msg">{{ form.errors.serie }}</span>
                                </div>
                                <div class="form-group">
                                    <label>Número</label>
                                    <input v-model="form.numero_factura" type="text" class="input-control" placeholder="000001">
                                    <span v-if="form.errors.numero_factura" class="error-msg">{{ form.errors.numero_factura }}</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Fecha Emisión</label>
                                <input v-model="form.fecha_emision" type="date" class="input-control">
                                <span v-if="form.errors.fecha_emision" class="error-msg">{{ form.errors.fecha_emision }}</span>
                            </div>

                            <div class="form-group">
                                <label>Fecha Vencimiento</label>
                                <input v-model="form.fecha_vencimiento" type="date" class="input-control">
                                <span v-if="form.errors.fecha_vencimiento" class="error-msg">{{ form.errors.fecha_vencimiento }}</span>
                            </div>

                            <div class="form-group">
                                <label>Estado</label>
                                <select v-model="form.estado" class="input-control">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="pago_parcial">Pago Parcial</option>
                                    <option value="pagado_completo">Pagado Completo</option>
                                </select>
                                <span v-if="form.errors.estado" class="error-msg">{{ form.errors.estado }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="main-content">
                    <!-- Client & Budget -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3>Cliente y Referencia</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Cliente <span class="required">*</span></label>
                                <select v-model="form.cliente_id" class="input-control">
                                    <option value="">Seleccione Cliente</option>
                                    <option v-for="c in clientes" :key="c.id" :value="c.id">
                                        {{ c.razon_social }} ({{ c.numero_documento }})
                                    </option>
                                </select>
                                <span v-if="form.errors.cliente_id" class="error-msg">{{ form.errors.cliente_id }}</span>
                            </div>

                            <div class="form-group">
                                <label>Presupuesto Relacionado <span class="required">*</span></label>
                                <select v-model="form.presupuesto_id" class="input-control">
                                    <option value="">Seleccione Presupuesto</option>
                                    <option v-for="p in filteredPresupuestos" :key="p.id" :value="p.id">
                                        {{ p.numero_presupuesto }} {{ p.observaciones ? '- ' + p.observaciones.substring(0, 30) : '' }}
                                    </option>
                                </select>
                                <span v-if="form.errors.presupuesto_id" class="error-msg">{{ form.errors.presupuesto_id }}</span>
                            </div>
                            
                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea v-model="form.observaciones" class="input-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Amounts -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Importes Económicos</h3>
                        </div>
                        <div class="card-body">
                            <div class="row-3">
                                <div class="form-group">
                                    <label>Base Imponible</label>
                                    <div class="input-prefix">
                                        <span>S/</span>
                                        <input v-model="form.base_imponible" type="number" step="0.01" class="input-control text-right">
                                    </div>
                                    <span v-if="form.errors.base_imponible" class="error-msg">{{ form.errors.base_imponible }}</span>
                                </div>
                                <div class="form-group">
                                    <label>I.G.V (18%)</label>
                                    <div class="input-prefix">
                                        <span>S/</span>
                                        <input v-model="form.igv" type="number" step="0.01" class="input-control text-right">
                                    </div>
                                    <span v-if="form.errors.igv" class="error-msg">{{ form.errors.igv }}</span>
                                </div>
                                <div class="form-group total-group">
                                    <label>TOTAL</label>
                                    <div class="input-prefix">
                                        <span>S/</span>
                                        <input v-model="form.total" type="number" step="0.01" readonly class="input-control text-right total-input">
                                    </div>
                                </div>
                            </div>

                            <div class="divider"></div>

                            <div class="row-2">
                                <div class="form-group">
                                    <label>Descuento Monto</label>
                                    <input v-model="form.descuento_monto" type="number" step="0.01" class="input-control">
                                </div>
                                <div class="form-group">
                                    <label>Descuento Descripción</label>
                                    <input v-model="form.descuento_descripcion" type="text" class="input-control">
                                </div>
                            </div>

                            <div class="divider"></div>

                            <div class="row-2">
                                <div class="form-group">
                                    <label>% Detracción</label>
                                    <input v-model="form.porcentaje_detraccion" type="number" step="0.1" class="input-control" placeholder="10">
                                </div>
                                <div class="form-group">
                                    <label>Monto Detracción</label>
                                    <input v-model="form.monto_detraccion" type="number" step="0.01" class="input-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Reuse compact styles from Subcontratistas but refined */
.form-container { max-width: 100%; margin: 0; padding: 0.5rem; }
.header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border); }
.header h1 { font-size: 1.25rem; font-weight: 700; color: var(--foreground); margin: 0; }
.subtitle { color: var(--muted-foreground); font-size: 0.75rem; margin: 0; }
.actions { display: flex; gap: 0.5rem; }

.grid-layout { display: grid; grid-template-columns: 260px 1fr; gap: 0.75rem; align-items: start; }
.card { background: var(--card); border: 1px solid var(--border); border-radius: 8px; overflow: hidden; }
.card-header { padding: 0.4rem 0.75rem; background: var(--muted); border-bottom: 1px solid var(--border); }
.card-header h3 { margin: 0; font-size: 0.8rem; font-weight: 600; color: var(--foreground); text-transform: uppercase; }
.card-body { padding: 0.75rem; display: flex; flex-direction: column; gap: 0.65rem; }
.mb-3 { margin-bottom: 0.75rem; }

.form-group label { display: block; font-size: 0.7rem; font-weight: 600; color: var(--muted-foreground); margin-bottom: 0.15rem; text-transform: uppercase; }
.required { color: var(--destructive); }
.input-control { width: 100%; padding: 0.35rem 0.6rem; border: 1px solid var(--input); background: var(--background); color: var(--foreground); border-radius: 4px; font-size: 0.8rem; line-height: 1.3; }
.input-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 1px var(--primary); }
.error-msg { color: var(--destructive); font-size: 0.7rem; margin-top: 0.1rem; display: block; }
.row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
.row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; }

.input-prefix { position: relative; display: flex; align-items: center; }
.input-prefix span { position: absolute; left: 0.5rem; font-size: 0.8rem; color: var(--muted-foreground); }
.input-prefix input { padding-left: 1.8rem; }
.text-right { text-align: right; }

.total-input { font-weight: bold; background: var(--muted); color: var(--foreground); }
.divider { height: 1px; background: var(--border); margin: 0.5rem 0; }

.btn { padding: 0.4rem 0.85rem; border-radius: 4px; font-weight: 600; font-size: 0.8rem; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; height: 30px; }
.btn-primary { background: var(--primary); color: var(--primary-foreground); }
.btn-secondary { background: var(--secondary); color: var(--secondary-foreground); border: 1px solid var(--border); }

@media (max-width: 768px) {
    .grid-layout { grid-template-columns: 1fr; }
    .header { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
    .actions { width: 100%; justify-content: flex-end; }
    .row-3 { grid-template-columns: 1fr; }
}
</style>
