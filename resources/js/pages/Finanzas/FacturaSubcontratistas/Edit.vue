<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { watch } from 'vue'

const props = defineProps({
    factura: Object,
    subcontratistas: Array,
    ordenes: Array,
})

const form = useForm({
    tipo_documento: props.factura.tipo_documento,
    serie: props.factura.serie,
    numero_documento: props.factura.numero_documento,
    fecha_emision: props.factura.fecha_emision,
    fecha_vencimiento: props.factura.fecha_vencimiento,
    subcontratista_id: props.factura.subcontratista_id,
    orden_servicio_id: props.factura.orden_servicio_id,
    base_imponible: props.factura.base_imponible,
    igv: props.factura.igv,
    total: props.factura.total,
    porcentaje_detraccion: props.factura.porcentaje_detraccion || 0,
    monto_detraccion: props.factura.monto_detraccion || 0,
    estado: props.factura.estado,
    observaciones: props.factura.observaciones || '',
})

// Calculations - Same as Create
watch(() => form.base_imponible, (newVal) => {
    form.igv = parseFloat((newVal * 0.18).toFixed(2))
    calculateTotal()
})

watch(() => form.igv, () => calculateTotal())

watch(() => form.porcentaje_detraccion, (newVal) => {
    if (newVal > 0 && form.total > 0) {
        form.monto_detraccion = parseFloat((form.total * (newVal / 100)).toFixed(2))
    } else {
        form.monto_detraccion = 0
    }
})

function calculateTotal() {
    const base = parseFloat(form.base_imponible || 0)
    const igv = parseFloat(form.igv || 0)
    form.total = parseFloat((base + igv).toFixed(2))

    if (form.porcentaje_detraccion > 0) {
        form.monto_detraccion = parseFloat((form.total * (form.porcentaje_detraccion / 100)).toFixed(2))
    }
}

// Optional: Link Order data if selected (and changed)
watch(() => form.orden_servicio_id, (newVal, oldVal) => {
    if (newVal && newVal !== oldVal && oldVal !== undefined) {
        // Only if manually changed, try to suggest stuff if we wanted to
        // For editing, usually we stick to what was saved unless user explicitly changes
    }
})

const submit = () => {
    form.put(`/factura-subcontratistas/${props.factura.uuid}`, {
        preserveScroll: true,
    })
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Facturas Subcontron.', href: '/factura-subcontratistas' },
    { title: 'Editar Factura', href: '#' },
]
</script>

<template>
    <Head title="Editar Factura Subcontratista" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="form-container">
            <div class="header">
                <div>
                    <h1>Editar Factura {{ props.factura.serie }}-{{ props.factura.numero_documento }}</h1>
                    <p class="subtitle">Modificar comprobante de pago a proveedor</p>
                </div>
                <div class="actions">
                    <Link href="/factura-subcontratistas" class="btn btn-secondary">Cancelar</Link>
                    <button @click="submit" class="btn btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Guardando...' : 'Actualizar Factura' }}
                    </button>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid-layout">
                <!-- Sidebar -->
                <div class="sidebar">
                    <div class="card">
                        <div class="card-header">
                            <h3>Documento</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Tipo Documento</label>
                                <select v-model="form.tipo_documento" class="input-control">
                                    <option value="FACTURA">Factura</option>
                                    <option value="BOLETA">Boleta</option>
                                    <option value="RECIBO_HONORARIOS">Recibo Honorarios</option>
                                </select>
                            </div>
                            <div class="row-2">
                                <div class="form-group">
                                    <label>Serie <span class="required">*</span></label>
                                    <input v-model="form.serie" type="text" class="input-control" placeholder="F001">
                                    <span v-if="form.errors.serie" class="error-msg">{{ form.errors.serie }}</span>
                                </div>
                                <div class="form-group">
                                    <label>Número <span class="required">*</span></label>
                                    <input v-model="form.numero_documento" type="text" class="input-control" placeholder="000001">
                                    <span v-if="form.errors.numero_documento" class="error-msg">{{ form.errors.numero_documento }}</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Fecha Emisión <span class="required">*</span></label>
                                <input v-model="form.fecha_emision" type="date" class="input-control">
                                <span v-if="form.errors.fecha_emision" class="error-msg">{{ form.errors.fecha_emision }}</span>
                            </div>

                            <div class="form-group">
                                <label>Fecha Vencimiento <span class="required">*</span></label>
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
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="main-content">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3>Proveedor y Referencia</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Subcontratista <span class="required">*</span></label>
                                <select v-model="form.subcontratista_id" class="input-control">
                                    <option value="">Seleccione Subcontratista</option>
                                    <option v-for="s in subcontratistas" :key="s.id" :value="s.id">
                                        {{ s.razon_social_nombre }} ({{ s.numero_documento }})
                                    </option>
                                </select>
                                <span v-if="form.errors.subcontratista_id" class="error-msg">{{ form.errors.subcontratista_id }}</span>
                            </div>

                            <div class="form-group">
                                <label>Orden de Servicio (Opcional)</label>
                                <select v-model="form.orden_servicio_id" class="input-control">
                                    <option value="">Sin referencia</option>
                                    <!-- Use filtered list depending on sub or show all if needed. For editing, maybe just show all or just matching to keep it simple -->
                                    <option 
                                        v-for="o in (form.subcontratista_id ? ordenes.filter(or => or.subcontratista_id === form.subcontratista_id) : ordenes)" 
                                        :key="o.id" 
                                        :value="o.id"
                                    >
                                        {{ o.numero_orden }} - {{ o.descripcion }}
                                    </option>
                                </select>
                                <span v-if="form.errors.orden_servicio_id" class="error-msg">{{ form.errors.orden_servicio_id }}</span>
                            </div>

                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea v-model="form.observaciones" class="input-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3>Importes</h3>
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
                                    <label>% Detracción</label>
                                    <input v-model="form.porcentaje_detraccion" type="number" step="0.01" class="input-control" placeholder="0">
                                </div>
                                <div class="form-group">
                                    <label>Monto Detracción</label>
                                    <div class="input-prefix">
                                        <span>S/</span>
                                        <input v-model="form.monto_detraccion" type="number" step="0.01" class="input-control text-right">
                                    </div>
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
