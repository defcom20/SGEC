<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps({
    empresa: Object,
})

const form = useForm({
    ruc: props.empresa.ruc || '',
    razon_social: props.empresa.razon_social || '',
    nombre_comercial: props.empresa.nombre_comercial || '',
    direccion: props.empresa.direccion || '',
    telefono: props.empresa.telefono || '',
    email: props.empresa.email || '',
})

const submit = () => {
    form.put(`/empresas/${props.empresa.uuid}`, {
        preserveScroll: true,
    })
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Configuración', href: '#' },
    { title: 'Datos de la Empresa', href: '/empresas' },
]
</script>

<template>
    <Head title="Datos de la Empresa" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="empresa-dashboard">
            <div class="header">
                <div>
                    <h1>Datos de la Empresa</h1>
                    <p class="subtitle">Configura la información fiscal y de contacto de tu organización</p>
                </div>
                
                <button 
                    @click="submit"
                    class="btn btn-primary"
                    :disabled="form.processing"
                >
                    <svg v-if="!form.processing" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    <span v-else class="spinner"></span>
                    {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                </button>
            </div>

            <form @submit.prevent="submit" class="empresa-grid">
                <!-- Tarjeta: Información Legal -->
                <div class="card">
                    <div class="card-header">
                        <div class="icon-box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                <line x1="8" y1="21" x2="16" y2="21"></line>
                                <line x1="12" y1="17" x2="12" y2="21"></line>
                            </svg>
                        </div>
                        <h3>Información Legal</h3>
                    </div>
                    
                    <div class="card-content">
                        <div class="form-group">
                            <label for="ruc">RUC / NIT <span class="required">*</span></label>
                            <input 
                                id="ruc"
                                v-model="form.ruc"
                                type="text"
                                placeholder="Ej. 20123456789"
                                :class="{ 'error': form.errors.ruc }"
                                required
                            >
                            <span v-if="form.errors.ruc" class="error-msg">{{ form.errors.ruc }}</span>
                        </div>

                        <div class="form-group">
                            <label for="razon_social">Razón Social <span class="required">*</span></label>
                            <input 
                                id="razon_social"
                                v-model="form.razon_social"
                                type="text" 
                                placeholder="Ej. Empresa SAC"
                                :class="{ 'error': form.errors.razon_social }"
                                required
                            >
                            <span v-if="form.errors.razon_social" class="error-msg">{{ form.errors.razon_social }}</span>
                        </div>

                        <div class="form-group">
                            <label for="nombre_comercial">Nombre Comercial</label>
                            <input 
                                id="nombre_comercial"
                                v-model="form.nombre_comercial"
                                type="text" 
                                placeholder="Ej. Mi Empresa"
                                :class="{ 'error': form.errors.nombre_comercial }"
                            >
                            <div class="help-text">Nombre visible para el público (opcional).</div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta: Información de Contacto -->
                <div class="card">
                    <div class="card-header">
                        <div class="icon-box accent">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                        </div>
                        <h3>Contacto y Ubicación</h3>
                    </div>

                    <div class="card-content">
                        <div class="form-group full">
                            <label for="direccion">Dirección Fiscal <span class="required">*</span></label>
                            <textarea 
                                id="direccion"
                                v-model="form.direccion"
                                rows="3"
                                placeholder="Av. Principal 123, Oficina 404"
                                :class="{ 'error': form.errors.direccion }"
                                required
                            ></textarea>
                            <span v-if="form.errors.direccion" class="error-msg">{{ form.errors.direccion }}</span>
                        </div>

                        <div class="row-2">
                            <div class="form-group">
                                <label for="telefono">Teléfono <span class="required">*</span></label>
                                <input 
                                    id="telefono"
                                    v-model="form.telefono"
                                    type="tel" 
                                    placeholder="+51 999 999 999"
                                    :class="{ 'error': form.errors.telefono }"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label for="email">Email Corporativo <span class="required">*</span></label>
                                <input 
                                    id="email"
                                    v-model="form.email"
                                    type="email" 
                                    placeholder="contacto@empresa.com"
                                    :class="{ 'error': form.errors.email }"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
.empresa-dashboard {
    padding: 1rem;
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
}

/* Header */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--foreground);
    margin-bottom: 0;
    letter-spacing: -0.025em;
    line-height: 1.2;
}

.subtitle {
    color: var(--muted-foreground);
    font-size: 0.875rem;
    margin: 0;
}

/* Grid layout */
.empresa-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1rem;
    align-items: start;
}

/* Cards */
.card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 8px; /* Radio un poco más cerrado */
    overflow: hidden;
    transition: box-shadow 0.2s;
}

.card:hover {
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
}

.card-header {
    background: var(--muted);
    padding: 0.75rem 1rem; /* Padding reducido */
    display: flex;
    align-items: center;
    gap: 0.65rem;
    border-bottom: 1px solid var(--border);
}

.icon-box {
    background: var(--background);
    width: 28px; /* Icono más pequeño */
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    border: 1px solid var(--border);
}

.icon-box svg {
    width: 16px;
    height: 16px;
}

.icon-box.accent {
    color: var(--chart-2);
}

.card-header h3 {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--foreground);
    margin: 0;
}

.card-content {
    padding: 1rem; /* Contenido más pegado a los bordes */
}

/* Forms */
.form-group {
    margin-bottom: 0.85rem; /* Margen entre inputs reducido */
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    font-size: 0.75rem; /* Etiqueta más pequeña */
    font-weight: 500;
    color: var(--foreground);
    margin-bottom: 0.25rem;
}

.required {
    color: var(--destructive);
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.4rem 0.65rem; /* Input más delgado verticalmente */
    background: var(--background);
    border: 1px solid var(--input);
    border-radius: 4px; /* Bordes más rectos */
    font-size: 0.85rem;
    color: var(--foreground);
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 2px var(--ring);
}

.form-group input::placeholder,
.form-group textarea::placeholder {
    color: var(--muted-foreground);
    font-size: 0.8rem;
}

.error {
    border-color: var(--destructive);
}

.error-msg {
    color: var(--destructive);
    font-size: 0.7rem;
    margin-top: 0.15rem;
    display: block;
}

.help-text {
    font-size: 0.7rem;
    color: var(--muted-foreground);
    margin-top: 0.15rem;
}

.full {
    width: 100%;
}

.row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

/* Button */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.45rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}

.btn-primary {
    background: var(--primary);
    color: var(--primary-foreground);
}

.btn-primary:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.spinner {
    width: 14px;
    height: 14px;
    border: 2px solid currentColor;
    border-bottom-color: transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin { 
    to { transform: rotate(360deg); } 
}

@media (max-width: 640px) {
    .row-2 {
        grid-template-columns: 1fr;
    }
    
    .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
