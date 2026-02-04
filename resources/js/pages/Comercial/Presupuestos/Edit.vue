<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3'
import { computed, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps({
    presupuesto: Object,
    clientes: Array,
    supervisores: Array,
})

const form = useForm({
    numero_presupuesto: props.presupuesto.numero_presupuesto || '',
    fecha_emision: props.presupuesto.fecha_emision || '',
    fecha_vencimiento: props.presupuesto.fecha_vencimiento || '',
    cliente_id: props.presupuesto.cliente_id || '',
    persona_contacto: props.presupuesto.persona_contacto || '',
    supervisor_id: props.presupuesto.supervisor_id || '',
    estado: props.presupuesto.estado || 'en_revision',
    fecha_aceptacion: props.presupuesto.fecha_aceptacion || '',
    fecha_inicio: props.presupuesto.fecha_inicio || '',
    fecha_finalizacion_estimada: props.presupuesto.fecha_finalizacion_estimada || '',
    periodo_ejecucion_dias: props.presupuesto.periodo_ejecucion_dias || '',
    base_imponible: props.presupuesto.base_imponible || 0,
    igv: props.presupuesto.igv || 0,
    descuento_porcentaje: props.presupuesto.descuento_porcentaje || 0,
    descuento_monto: props.presupuesto.descuento_monto || 0,
    total: props.presupuesto.total || 0,
    observaciones: props.presupuesto.observaciones || '',
})

// Calcular automáticamente IGV y Total
watch(() => form.base_imponible, (newVal) => {
    const base = parseFloat(newVal) || 0
    form.igv = (base * 0.18).toFixed(2)
    calcularTotal()
})

watch(() => form.descuento_porcentaje, (newVal) => {
    const base = parseFloat(form.base_imponible) || 0
    const porcentaje = parseFloat(newVal) || 0
    form.descuento_monto = (base * porcentaje / 100).toFixed(2)
    calcularTotal()
})

watch(() => form.descuento_monto, () => {
    calcularTotal()
})

const calcularTotal = () => {
    const base = parseFloat(form.base_imponible) || 0
    const igv = parseFloat(form.igv) || 0
    const descuento = parseFloat(form.descuento_monto) || 0
    form.total = (base + igv - descuento).toFixed(2)
}

const submit = () => {
    form.put(`/presupuestos/${props.presupuesto.uuid}`, {
        preserveScroll: true,
    })
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Presupuestos', href: '/presupuestos' },
    { title: 'Editar Presupuesto', href: '#' },
]
</script>

<template>
    <Head title="Editar Presupuesto" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="form-container">
            <div class="header">
                <div>
                    <h1>Editar Presupuesto</h1>
                    <p class="subtitle">Modifica la información del presupuesto</p>
                </div>
                <div class="actions">
                    <Link href="/presupuestos" class="btn btn-secondary">Cancelar</Link>
                    <button @click="submit" class="btn btn-primary" :disabled="form.processing">
                        <span v-if="form.processing">Guardando...</span>
                        <span v-else>Actualizar Presupuesto</span>
                    </button>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid-layout">
                <!-- Columna Izquierda: Información General -->
                <div class="sidebar">
                    <div class="card">
                        <div class="card-header">
                            <h3>Información General</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Número Presupuesto <span class="required">*</span></label>
                                <input
                                    v-model="form.numero_presupuesto"
                                    type="text"
                                    class="input-control"
                                    placeholder="Ej: PRES-2026-001"
                                >
                                <span v-if="form.errors.numero_presupuesto" class="error-msg">{{ form.errors.numero_presupuesto }}</span>
                            </div>

                            <div class="form-group">
                                <label>Cliente <span class="required">*</span></label>
                                <select v-model="form.cliente_id" class="input-control">
                                    <option value="">Seleccionar cliente</option>
                                    <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                        {{ cliente.nombre }}
                                    </option>
                                </select>
                                <span v-if="form.errors.cliente_id" class="error-msg">{{ form.errors.cliente_id }}</span>
                            </div>

                            <div class="form-group">
                                <label>Persona de Contacto</label>
                                <input
                                    v-model="form.persona_contacto"
                                    type="text"
                                    class="input-control"
                                    placeholder="Nombre del contacto"
                                >
                                <span v-if="form.errors.persona_contacto" class="error-msg">{{ form.errors.persona_contacto }}</span>
                            </div>

                            <div class="form-group">
                                <label>Supervisor <span class="required">*</span></label>
                                <select v-model="form.supervisor_id" class="input-control">
                                    <option value="">Seleccionar supervisor</option>
                                    <option v-for="supervisor in supervisores" :key="supervisor.id" :value="supervisor.id">
                                        {{ supervisor.name }}
                                    </option>
                                </select>
                                <span v-if="form.errors.supervisor_id" class="error-msg">{{ form.errors.supervisor_id }}</span>
                            </div>

                            <div class="form-group">
                                <label>Estado <span class="required">*</span></label>
                                <select v-model="form.estado" class="input-control">
                                    <option value="en_revision">En Revisión</option>
                                    <option value="aprobado">Aprobado</option>
                                    <option value="rechazado">Rechazado</option>
                                    <option value="vencido">Vencido</option>
                                    <option value="en_ejecucion">En Ejecución</option>
                                    <option value="finalizado">Finalizado</option>
                                </select>
                                <span v-if="form.errors.estado" class="error-msg">{{ form.errors.estado }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Detalles -->
                <div class="main-content">
                    <!-- Fechas -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Fechas Importantes</h3>
                        </div>
                        <div class="card-body">
                            <div class="row-3">
                                <div class="form-group">
                                    <label>Fecha Emisión <span class="required">*</span></label>
                                    <input
                                        v-model="form.fecha_emision"
                                        type="date"
                                        class="input-control"
                                    >
                                    <span v-if="form.errors.fecha_emision" class="error-msg">{{ form.errors.fecha_emision }}</span>
                                </div>
                                <div class="form-group">
                                    <label>Fecha Vencimiento <span class="required">*</span></label>
                                    <input
                                        v-model="form.fecha_vencimiento"
                                        type="date"
                                        class="input-control"
                                    >
                                    <span v-if="form.errors.fecha_vencimiento" class="error-msg">{{ form.errors.fecha_vencimiento }}</span>
                                </div>
                                <div class="form-group">
                                    <label>Fecha Aceptación</label>
                                    <input
                                        v-model="form.fecha_aceptacion"
                                        type="date"
                                        class="input-control"
                                    >
                                    <span v-if="form.errors.fecha_aceptacion" class="error-msg">{{ form.errors.fecha_aceptacion }}</span>
                                </div>
                            </div>

                            <div class="row-3">
                                <div class="form-group">
                                    <label>Fecha Inicio</label>
                                    <input
                                        v-model="form.fecha_inicio"
                                        type="date"
                                        class="input-control"
                                    >
                                    <span v-if="form.errors.fecha_inicio" class="error-msg">{{ form.errors.fecha_inicio }}</span>
                                </div>
                                <div class="form-group">
                                    <label>Fecha Fin Estimada</label>
                                    <input
                                        v-model="form.fecha_finalizacion_estimada"
                                        type="date"
                                        class="input-control"
                                    >
                                    <span v-if="form.errors.fecha_finalizacion_estimada" class="error-msg">{{ form.errors.fecha_finalizacion_estimada }}</span>
                                </div>
                                <div class="form-group">
                                    <label>Periodo (días)</label>
                                    <input
                                        v-model="form.periodo_ejecucion_dias"
                                        type="number"
                                        class="input-control"
                                        placeholder="30"
                                    >
                                    <span v-if="form.errors.periodo_ejecucion_dias" class="error-msg">{{ form.errors.periodo_ejecucion_dias }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Montos -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Montos y Cálculos</h3>
                        </div>
                        <div class="card-body">
                            <div class="row-2">
                                <div class="form-group">
                                    <label>Base Imponible <span class="required">*</span></label>
                                    <input
                                        v-model="form.base_imponible"
                                        type="number"
                                        step="0.01"
                                        class="input-control"
                                        placeholder="0.00"
                                    >
                                    <span v-if="form.errors.base_imponible" class="error-msg">{{ form.errors.base_imponible }}</span>
                                </div>
                                <div class="form-group">
                                    <label>IGV (18%) <span class="required">*</span></label>
                                    <input
                                        v-model="form.igv"
                                        type="number"
                                        step="0.01"
                                        class="input-control"
                                        readonly
                                        style="background: var(--muted); cursor: not-allowed;"
                                    >
                                    <span v-if="form.errors.igv" class="error-msg">{{ form.errors.igv }}</span>
                                </div>
                            </div>

                            <div class="row-2">
                                <div class="form-group">
                                    <label>Descuento (%)</label>
                                    <input
                                        v-model="form.descuento_porcentaje"
                                        type="number"
                                        step="0.01"
                                        class="input-control"
                                        placeholder="0.00"
                                    >
                                    <span v-if="form.errors.descuento_porcentaje" class="error-msg">{{ form.errors.descuento_porcentaje }}</span>
                                </div>
                                <div class="form-group">
                                    <label>Descuento (S/)</label>
                                    <input
                                        v-model="form.descuento_monto"
                                        type="number"
                                        step="0.01"
                                        class="input-control"
                                        placeholder="0.00"
                                    >
                                    <span v-if="form.errors.descuento_monto" class="error-msg">{{ form.errors.descuento_monto }}</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Total <span class="required">*</span></label>
                                <input
                                    v-model="form.total"
                                    type="number"
                                    step="0.01"
                                    class="input-control total-input"
                                    readonly
                                >
                                <span v-if="form.errors.total" class="error-msg">{{ form.errors.total }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Observaciones</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <textarea
                                    v-model="form.observaciones"
                                    rows="4"
                                    class="input-control"
                                    placeholder="Notas adicionales o condiciones especiales..."
                                ></textarea>
                                <span v-if="form.errors.observaciones" class="error-msg">{{ form.errors.observaciones }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Contenedor principal sin márgenes desperdiciados */
.form-container {
    max-width: 100%;
    margin: 0;
    padding: 0.5rem;
}

/* Header Compacto */
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

/* Grid Layout Compacto */
.grid-layout {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 0.75rem;
    align-items: start;
}

/* Cards Ultra Compactas */
.card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 0.75rem;
}

.card:last-child {
    margin-bottom: 0;
}

.card-header {
    padding: 0.4rem 0.75rem;
    background: var(--muted);
    border-bottom: 1px solid var(--border);
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
    gap: 0.65rem;
}

/* Controles de Formulario */
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
    transition: all 0.1s;
    line-height: 1.3;
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

.total-input {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--primary);
    background: var(--muted);
    cursor: not-allowed;
}

.error-msg {
    color: var(--destructive);
    font-size: 0.7rem;
    margin-top: 0.1rem;
    display: block;
    line-height: 1;
}

.row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.row-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
}

/* Botones Compactos */
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

@media (max-width: 1024px) {
    .grid-layout { grid-template-columns: 1fr; }
    .row-3 { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .header { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
    .actions { width: 100%; justify-content: flex-end; }
    .btn { flex: 1; }
    .row-2 { grid-template-columns: 1fr; }
}
</style>
