<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps({
    articulo: Object,
    proveedores: Array,
})

const form = useForm({
    codigo: props.articulo.codigo || '',
    descripcion: props.articulo.descripcion || '',
    unidad_medida: props.articulo.unidad_medida || 'UND',
    proveedor_id: props.articulo.proveedor_id || '',
    precio_compra: props.articulo.precio_compra || 0,
    precio_venta: props.articulo.precio_venta || 0,
    stock: props.articulo.stock || 0,
    fecha_vencimiento: props.articulo.fecha_vencimiento || '',
    estado: props.articulo.estado || 'activo',
})

const submit = () => {
    form.put(`/articulos/${props.articulo.uuid}`, {
        preserveScroll: true,
    })
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Artículos', href: '/articulos' },
    { title: 'Editar Artículo', href: '#' },
]
</script>

<template>
    <Head title="Editar Artículo" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="form-container">
            <div class="header">
                <div>
                    <h1>Editar Artículo</h1>
                    <p class="subtitle">Modifica la información del producto o material</p>
                </div>
                <div class="actions">
                    <Link href="/articulos" class="btn btn-secondary">Cancelar</Link>
                    <button @click="submit" class="btn btn-primary" :disabled="form.processing">
                        <span v-if="form.processing">Guardando...</span>
                        <span v-else>Actualizar Artículo</span>
                    </button>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid-layout">
                <!-- Columna Izquierda: Identificación -->
                <div class="sidebar">
                    <div class="card">
                        <div class="card-header">
                            <h3>Identificación</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Código <span class="required">*</span></label>
                                <input
                                    v-model="form.codigo"
                                    type="text"
                                    class="input-control"
                                    placeholder="Ej: ART-001"
                                >
                                <span v-if="form.errors.codigo" class="error-msg">{{ form.errors.codigo }}</span>
                            </div>

                            <div class="form-group">
                                <label>Proveedor <span class="required">*</span></label>
                                <select v-model="form.proveedor_id" class="input-control">
                                    <option value="">Seleccionar proveedor</option>
                                    <option v-for="proveedor in proveedores" :key="proveedor.id" :value="proveedor.id">
                                        {{ proveedor.razon_social }}
                                    </option>
                                </select>
                                <span v-if="form.errors.proveedor_id" class="error-msg">{{ form.errors.proveedor_id }}</span>
                            </div>

                            <div class="form-group">
                                <label>Unidad de Medida <span class="required">*</span></label>
                                <select v-model="form.unidad_medida" class="input-control">
                                    <option value="UND">Unidad (UND)</option>
                                    <option value="KG">Kilogramo (KG)</option>
                                    <option value="M">Metro (M)</option>
                                    <option value="M2">Metro Cuadrado (M2)</option>
                                    <option value="M3">Metro Cúbico (M3)</option>
                                    <option value="LT">Litro (LT)</option>
                                    <option value="GL">Galón (GL)</option>
                                    <option value="BL">Bolsa (BL)</option>
                                    <option value="CJ">Caja (CJ)</option>
                                </select>
                                <span v-if="form.errors.unidad_medida" class="error-msg">{{ form.errors.unidad_medida }}</span>
                            </div>

                            <div class="form-group">
                                <label>Estado</label>
                                <div class="radio-group">
                                    <label class="radio-option" :class="{ active: form.estado === 'activo' }">
                                        <input type="radio" value="activo" v-model="form.estado">
                                        <span>Activo</span>
                                    </label>
                                    <label class="radio-option" :class="{ active: form.estado === 'inactivo' }">
                                        <input type="radio" value="inactivo" v-model="form.estado">
                                        <span>Inactivo</span>
                                    </label>
                                </div>
                                <span v-if="form.errors.estado" class="error-msg">{{ form.errors.estado }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Detalles -->
                <div class="main-content">
                    <div class="card">
                        <div class="card-header">
                            <h3>Información del Producto</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Descripción <span class="required">*</span></label>
                                <textarea
                                    v-model="form.descripcion"
                                    rows="3"
                                    class="input-control"
                                    placeholder="Descripción detallada del artículo..."
                                ></textarea>
                                <span v-if="form.errors.descripcion" class="error-msg">{{ form.errors.descripcion }}</span>
                            </div>

                            <div class="row-2">
                                <div class="form-group">
                                    <label>Precio Compra <span class="required">*</span></label>
                                    <input
                                        v-model="form.precio_compra"
                                        type="number"
                                        step="0.01"
                                        class="input-control"
                                        placeholder="0.00"
                                    >
                                    <span v-if="form.errors.precio_compra" class="error-msg">{{ form.errors.precio_compra }}</span>
                                </div>
                                <div class="form-group">
                                    <label>Precio Venta <span class="required">*</span></label>
                                    <input
                                        v-model="form.precio_venta"
                                        type="number"
                                        step="0.01"
                                        class="input-control"
                                        placeholder="0.00"
                                    >
                                    <span v-if="form.errors.precio_venta" class="error-msg">{{ form.errors.precio_venta }}</span>
                                </div>
                            </div>

                            <div class="row-2">
                                <div class="form-group">
                                    <label>Stock <span class="required">*</span></label>
                                    <input
                                        v-model="form.stock"
                                        type="number"
                                        step="0.01"
                                        class="input-control"
                                        placeholder="0.00"
                                    >
                                    <span v-if="form.errors.stock" class="error-msg">{{ form.errors.stock }}</span>
                                </div>
                                <div class="form-group">
                                    <label>Fecha Vencimiento</label>
                                    <input
                                        v-model="form.fecha_vencimiento"
                                        type="date"
                                        class="input-control"
                                    >
                                    <span v-if="form.errors.fecha_vencimiento" class="error-msg">{{ form.errors.fecha_vencimiento }}</span>
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
    grid-template-columns: 260px 1fr;
    gap: 0.75rem;
    align-items: start;
}

/* Cards Ultra Compactas */
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

/* Radio Group Compacto */
.radio-group {
    display: flex;
    background: var(--input);
    padding: 0.15rem;
    border-radius: 4px;
    gap: 2px;
}

.radio-option {
    flex: 1;
    text-align: center;
    padding: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    border-radius: 3px;
    transition: all 0.1s;
    color: var(--muted-foreground);
    position: relative;
    user-select: none;
}

.radio-option input { display: none; }

.radio-option.active {
    background: var(--card);
    color: var(--primary);
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    font-weight: 700;
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

@media (max-width: 768px) {
    .grid-layout { grid-template-columns: 1fr; }
    .header { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
    .actions { width: 100%; justify-content: flex-end; }
    .btn { flex: 1; }
    .row-2 { grid-template-columns: 1fr; }
}
</style>
