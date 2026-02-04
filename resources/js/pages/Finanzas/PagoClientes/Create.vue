<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { watch, ref } from 'vue'

const props = defineProps({
    facturas: Array, // Expected to have id, numero_factura, serie, cliente_nombre, total, monto_pendiente
})

const form = useForm({
    factura_cliente_id: '',
    fecha_pago: new Date().toISOString().split('T')[0],
    monto: 0,
    metodo_pago: 'transferencia',
    banco: '',
    numero_operacion: '',
    observaciones: '',
})

const selectedFactura = ref(null)

// Watch invoice selection to prepopulate max amount or guide user
watch(() => form.factura_cliente_id, (newVal) => {
    if (!newVal) {
        selectedFactura.value = null
        form.monto = 0
        return
    }
    
    selectedFactura.value = props.facturas.find(f => f.id === newVal)
    if (selectedFactura.value) {
        // Default to full pending amount, but allow partial
        form.monto = parseFloat(selectedFactura.value.monto_pendiente)
    }
})

const submit = () => {
    form.post('/pago-clientes', {
        preserveScroll: true,
    })
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Pagos Clientes', href: '/pago-clientes' },
    { title: 'Nuevo Pago', href: '#' },
]

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-PE', { style: 'currency', currency: 'PEN' }).format(value)
}
</script>

<template>
    <Head title="Registrar Pago Cliente" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="form-container">
            <div class="header">
                <div>
                    <h1>Registrar Pago</h1>
                    <p class="subtitle">Ingreso de dinero por facturas de venta</p>
                </div>
                <div class="actions">
                    <Link href="/pago-clientes" class="btn btn-secondary">Cancelar</Link>
                    <button @click="submit" class="btn btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Guardando...' : 'Registrar Pago' }}
                    </button>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid-layout">
                <!-- Payment Details -->
                <div class="card">
                    <div class="card-header">
                        <h3>Detalles del Pago</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Factura a Pagar <span class="required">*</span></label>
                            <select v-model="form.factura_cliente_id" class="input-control">
                                <option value="">Seleccione Factura</option>
                                <option v-for="f in facturas" :key="f.id" :value="f.id">
                                    {{ f.serie }}-{{ f.numero_factura }} | {{ f.cliente_nombre }} | Pendiente: {{ formatCurrency(f.monto_pendiente) }}
                                </option>
                            </select>
                            <span v-if="form.errors.factura_cliente_id" class="error-msg">{{ form.errors.factura_cliente_id }}</span>
                        </div>

                        <!-- Info Card for Selected Invoice -->
                        <div v-if="selectedFactura" class="invoice-summary">
                            <div class="summary-row">
                                <span>Total Factura:</span>
                                <span>{{ formatCurrency(selectedFactura.total) }}</span>
                            </div>
                            <div class="summary-row highlight">
                                <span>Saldo Pendiente:</span>
                                <span>{{ formatCurrency(selectedFactura.monto_pendiente) }}</span>
                            </div>
                        </div>

                        <div class="row-2">
                            <div class="form-group">
                                <label>Fecha Pago <span class="required">*</span></label>
                                <input v-model="form.fecha_pago" type="date" class="input-control">
                                <span v-if="form.errors.fecha_pago" class="error-msg">{{ form.errors.fecha_pago }}</span>
                            </div>
                            <div class="form-group">
                                <label>Monto a Pagar <span class="required">*</span></label>
                                <div class="input-prefix">
                                    <span>S/</span>
                                    <input v-model="form.monto" type="number" step="0.01" class="input-control text-right">
                                </div>
                                <span v-if="form.errors.monto" class="error-msg">{{ form.errors.monto }}</span>
                                <span v-if="selectedFactura && form.monto > selectedFactura.monto_pendiente" class="warning-msg">
                                    ⚠️ El monto excede el saldo pendiente
                                </span>
                            </div>
                        </div>

                        <div class="row-2">
                            <div class="form-group">
                                <label>Método de Pago <span class="required">*</span></label>
                                <select v-model="form.metodo_pago" class="input-control">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia Bancaria</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="yape">Yape / Plin</option>
                                </select>
                                <span v-if="form.errors.metodo_pago" class="error-msg">{{ form.errors.metodo_pago }}</span>
                            </div>
                            <div class="form-group">
                                <label>Banco</label>
                                <input v-model="form.banco" type="text" class="input-control" placeholder="BCP, BBVA, Interbank...">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Número de Operación / Constancia</label>
                            <input v-model="form.numero_operacion" type="text" class="input-control">
                            <span v-if="form.errors.numero_operacion" class="error-msg">{{ form.errors.numero_operacion }}</span>
                        </div>

                        <div class="form-group">
                            <label>Observaciones</label>
                            <textarea v-model="form.observaciones" class="input-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
.form-container { max-width: 600px; margin: 0 auto; padding: 0.5rem; }
.header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border); }
.header h1 { font-size: 1.25rem; font-weight: 700; color: var(--foreground); margin: 0; }
.subtitle { color: var(--muted-foreground); font-size: 0.75rem; margin: 0; }
.actions { display: flex; gap: 0.5rem; }

.card { background: var(--card); border: 1px solid var(--border); border-radius: 8px; overflow: hidden; }
.card-header { padding: 0.4rem 0.75rem; background: var(--muted); border-bottom: 1px solid var(--border); }
.card-header h3 { margin: 0; font-size: 0.8rem; font-weight: 600; color: var(--foreground); text-transform: uppercase; }
.card-body { padding: 1rem; display: flex; flex-direction: column; gap: 0.75rem; }

.form-group label { display: block; font-size: 0.7rem; font-weight: 600; color: var(--muted-foreground); margin-bottom: 0.15rem; text-transform: uppercase; }
.required { color: var(--destructive); }
.input-control { width: 100%; padding: 0.35rem 0.6rem; border: 1px solid var(--input); background: var(--background); color: var(--foreground); border-radius: 4px; font-size: 0.8rem; line-height: 1.3; }
.input-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 1px var(--primary); }
.error-msg { color: var(--destructive); font-size: 0.7rem; margin-top: 0.1rem; display: block; }
.warning-msg { color: #d97706; font-size: 0.7rem; margin-top: 0.1rem; display: block; }
.row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }

.input-prefix { position: relative; display: flex; align-items: center; }
.input-prefix span { position: absolute; left: 0.5rem; font-size: 0.8rem; color: var(--muted-foreground); }
.input-prefix input { padding-left: 1.8rem; }
.text-right { text-align: right; }

.invoice-summary { background: var(--muted); padding: 0.5rem; border-radius: 4px; border: 1px dashed var(--border); margin-bottom: 0.5rem; }
.summary-row { display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--muted-foreground); margin-bottom: 0.2rem; }
.summary-row.highlight { color: var(--foreground); font-weight: 600; }

.btn { padding: 0.4rem 0.85rem; border-radius: 4px; font-weight: 600; font-size: 0.8rem; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; height: 30px; }
.btn-primary { background: var(--primary); color: var(--primary-foreground); }
.btn-secondary { background: var(--secondary); color: var(--secondary-foreground); border: 1px solid var(--border); }
</style>
