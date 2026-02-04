<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const form = useForm({
    codigo: '',
    descripcion: '',
    unidad_medida: 'UND',
    precio_referencial: 0,
    estado: 'activo',
})

const submit = () => {
    form.post('/servicios', {
        preserveScroll: true,
    })
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Servicios', href: '/servicios' },
    { title: 'Nuevo Servicio', href: '#' },
]

const unidadesMedida = [
    { value: 'UND', label: 'Unidad' },
    { value: 'M2', label: 'Metro Cuadrado' },
    { value: 'M3', label: 'Metro Cúbico' },
    { value: 'ML', label: 'Metro Lineal' },
    { value: 'GLB', label: 'Global' },
    { value: 'KG', label: 'Kilogramo' },
    { value: 'HR', label: 'Hora' },
    { value: 'DIA', label: 'Día' },
]
</script>

<template>
    <Head title="Nuevo Servicio" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="form-container">
            <div class="header">
                <div>
                    <h1>Registrar Servicio</h1>
                    <p class="subtitle">Agrega un nuevo servicio o producto al catálogo</p>
                </div>
                <div class="actions">
                    <Link href="/servicios" class="btn btn-secondary">Cancelar</Link>
                    <button @click="submit" class="btn btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Guardando...' : 'Guardar Servicio' }}
                    </button>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid-layout">
                <!-- Sidebar: Info Básica -->
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
                                    placeholder="Ej: SRV-001"
                                    autofocus
                                >
                                <span v-if="form.errors.codigo" class="error-msg">{{ form.errors.codigo }}</span>
                            </div>

                            <div class="form-group">
                                <label>Estado</label>
                                <select v-model="form.estado" class="input-control">
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content: Detalles del Servicio -->
                <div class="main-content">
                    <div class="card">
                        <div class="card-header">
                            <h3>Detalles del Servicio</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Descripción del Servicio <span class="required">*</span></label>
                                <input 
                                    v-model="form.descripcion" 
                                    type="text" 
                                    class="input-control" 
                                    placeholder="Ej: Instalación de Sistema Eléctrico"
                                >
                                <span v-if="form.errors.descripcion" class="error-msg">{{ form.errors.descripcion }}</span>
                            </div>

                            <div class="row-2">
                                <div class="form-group">
                                    <label>Unidad de Medida <span class="required">*</span></label>
                                    <select v-model="form.unidad_medida" class="input-control">
                                        <option v-for="unidad in unidadesMedida" :key="unidad.value" :value="unidad.value">
                                            {{ unidad.label }}
                                        </option>
                                    </select>
                                    <span v-if="form.errors.unidad_medida" class="error-msg">{{ form.errors.unidad_medida }}</span>
                                </div>

                                <div class="form-group">
                                    <label>Precio Referencial (S/) <span class="required">*</span></label>
                                    <input 
                                        v-model="form.precio_referencial" 
                                        type="number" 
                                        step="0.01" 
                                        min="0" 
                                        class="input-control" 
                                        placeholder="0.00"
                                    >
                                    <span v-if="form.errors.precio_referencial" class="error-msg">{{ form.errors.precio_referencial }}</span>
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
/* Ultra Compact Styles */
.form-container {
    max-width: 100%;
    margin: 0;
    padding: 0.5rem;
}

/* Header */
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

/* Base Layout */
.grid-layout {
    display: grid;
    grid-template-columns: 260px 1fr;
    gap: 0.75rem;
    align-items: start;
}

/* Cards */
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

select.input-control {
    cursor: pointer;
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

/* Botones */
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
