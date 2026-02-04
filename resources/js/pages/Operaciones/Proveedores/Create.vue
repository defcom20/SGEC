<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const form = useForm({
    tipo_documento: 'RUC',
    numero_documento: '',
    razon_social: '',
    rubro: '',
    contacto: '',
    telefono: '',
    email: '',
    estado: 'activo',
})

const submit = () => {
    form.post('/proveedors', {
        preserveScroll: true,
    })
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Proveedores', href: '/proveedors' },
    { title: 'Nuevo Proveedor', href: '#' },
]
</script>

<template>
    <Head title="Nuevo Proveedor" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="form-container">
            <div class="header">
                <div>
                    <h1>Registrar Proveedor</h1>
                    <p class="subtitle">Ingresa la información del nuevo proveedor</p>
                </div>
                <div class="actions">
                    <Link href="/proveedors" class="btn btn-secondary">Cancelar</Link>
                    <button @click="submit" class="btn btn-primary" :disabled="form.processing">
                        <span v-if="form.processing">Guardando...</span>
                        <span v-else>Guardar Proveedor</span>
                    </button>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid-layout">
                <!-- Columna Izquierda: Identidad -->
                <div class="sidebar">
                    <div class="card">
                        <div class="card-header">
                            <h3>Identificación</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Tipo Documento</label>
                                <select v-model="form.tipo_documento" class="input-control">
                                    <option value="RUC">RUC</option>
                                    <option value="DNI">DNI</option>
                                    <option value="CE">Carnet Extranjería</option>
                                    <option value="PASAPORTE">Pasaporte</option>
                                </select>
                                <span v-if="form.errors.tipo_documento" class="error-msg">{{ form.errors.tipo_documento }}</span>
                            </div>

                            <div class="form-group">
                                <label>Número Documento <span class="required">*</span></label>
                                <input
                                    v-model="form.numero_documento"
                                    type="text"
                                    class="input-control"
                                    placeholder="Ej: 20600000001"
                                >
                                <span v-if="form.errors.numero_documento" class="error-msg">{{ form.errors.numero_documento }}</span>
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
                            <h3>Información General y Contacto</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Razón Social <span class="required">*</span></label>
                                <input
                                    v-model="form.razon_social"
                                    type="text"
                                    class="input-control"
                                    placeholder="Nombre completo de la empresa"
                                >
                                <span v-if="form.errors.razon_social" class="error-msg">{{ form.errors.razon_social }}</span>
                            </div>

                            <div class="form-group">
                                <label>Rubro / Sector</label>
                                <input
                                    v-model="form.rubro"
                                    type="text"
                                    class="input-control"
                                    placeholder="Ej: Materiales de construcción, Servicios IT, etc."
                                >
                                <span v-if="form.errors.rubro" class="error-msg">{{ form.errors.rubro }}</span>
                            </div>

                            <div class="row-2">
                                <div class="form-group">
                                    <label>Persona de Contacto</label>
                                    <input
                                        v-model="form.contacto"
                                        type="text"
                                        class="input-control"
                                        placeholder="Nombre del encargado"
                                    >
                                    <span v-if="form.errors.contacto" class="error-msg">{{ form.errors.contacto }}</span>
                                </div>
                                <div class="form-group">
                                    <label>Teléfono / Celular</label>
                                    <input
                                        v-model="form.telefono"
                                        type="text"
                                        class="input-control"
                                        placeholder="+51 999 999 999"
                                    >
                                    <span v-if="form.errors.telefono" class="error-msg">{{ form.errors.telefono }}</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Email de Contacto</label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    class="input-control"
                                    placeholder="contacto@proveedor.com"
                                >
                                <span v-if="form.errors.email" class="error-msg">{{ form.errors.email }}</span>
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
    margin-bottom: 0.75rem; /* Margen muy reducido */
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border);
}

.header h1 {
    font-size: 1.25rem; /* Título más pequeño */
    font-weight: 700;
    color: var(--foreground);
    margin: 0;
    line-height: 1.1;
}

.subtitle {
    color: var(--muted-foreground);
    font-size: 0.75rem; /* Subtítulo pequeño */
    margin: 0;
}

.actions {
    display: flex;
    gap: 0.5rem;
}

/* Grid Layout Compacto */
.grid-layout {
    display: grid;
    grid-template-columns: 260px 1fr; /* Sidebar un poco más estrecha */
    gap: 0.75rem; /* Gap mínimo */
    align-items: start;
}

/* Cards Ultra Compactas */
.card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 8px; /* Radio reducido */
    overflow: hidden;
}

.card-header {
    padding: 0.4rem 0.75rem; /* Header muy delgado */
    background: var(--muted);
    border-bottom: 1px solid var(--border);
}

.card-header h3 {
    margin: 0;
    font-size: 0.8rem; /* Fuente pequeña y negrita */
    font-weight: 600;
    color: var(--foreground);
    text-transform: uppercase;
    letter-spacing: 0.02em;
}

.card-body {
    padding: 0.75rem; /* Padding interno mínimo */
    display: flex;
    flex-direction: column;
    gap: 0.65rem; /* Espacio entre inputs reducido */
}

/* Controles de Formulario */
.form-group label {
    display: block;
    font-size: 0.7rem; /* Etiqueta pequeña */
    font-weight: 600;
    color: var(--muted-foreground);
    margin-bottom: 0.15rem; /* Casi pegado al input */
    text-transform: uppercase;
}

.required { color: var(--destructive); }

.input-control {
    width: 100%;
    padding: 0.35rem 0.6rem; /* Input bajito */
    border: 1px solid var(--input);
    background: var(--background);
    color: var(--foreground);
    border-radius: 4px; /* Bordes más rectos */
    font-size: 0.8rem; /* Texto interno pequeño */
    transition: all 0.1s;
    line-height: 1.3;
}

.input-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 1px var(--primary); /* Sombra dura, no difusa */
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
    padding: 0.15rem; /* Padding mínimo */
    border-radius: 4px;
    gap: 2px;
}

.radio-option {
    flex: 1;
    text-align: center;
    padding: 0.25rem; /* Opción delgada */
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
    padding: 0.4rem 0.85rem; /* Botón pequeño */
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
    height: 30px; /* Altura fija pequeña */
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
