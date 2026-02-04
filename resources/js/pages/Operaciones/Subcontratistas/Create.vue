<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const form = useForm({
    tipo: 'persona_natural',
    tipo_documento: 'DNI',
    numero_documento: '',
    razon_social_nombre: '',
    direccion: '',
    telefono: '',
    email: '',
    banco: '',
    numero_cuenta: '',
    cci: '',
    numero_cuenta_detraccion: '',
    estado: 'activo',
})

const submit = () => {
    form.post('/subcontratistas', {
        preserveScroll: true,
    })
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Subcontratistas', href: '/subcontratistas' },
    { title: 'Nuevo Subcontratista', href: '#' },
]
</script>

<template>
    <Head title="Nuevo Subcontratista" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="form-container">
            <div class="header">
                <div>
                    <h1>Registrar Subcontratista</h1>
                    <p class="subtitle">Datos del maestro de obra o empresa contratista</p>
                </div>
                <div class="actions">
                    <Link href="/subcontratistas" class="btn btn-secondary">Cancelar</Link>
                    <button @click="submit" class="btn btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Guardando...' : 'Guardar' }}
                    </button>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid-layout">
                <!-- Sidebar: Identidad -->
                <div class="sidebar">
                    <div class="card">
                        <div class="card-header">
                            <h3>Identificación</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Tipo de Subcontratista</label>
                                <select v-model="form.tipo" class="input-control">
                                    <option value="persona_natural">Persona Natural (Maestro/Técnico)</option>
                                    <option value="empresa">Empresa</option>
                                </select>
                                <span v-if="form.errors.tipo" class="error-msg">{{ form.errors.tipo }}</span>
                            </div>

                            <div class="form-group">
                                <label>Tipo Documento</label>
                                <select v-model="form.tipo_documento" class="input-control">
                                    <option value="DNI">DNI</option>
                                    <option value="RUC">RUC</option>
                                    <option value="CE">Carné Extranjería</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Número Documento <span class="required">*</span></label>
                                <input
                                    v-model="form.numero_documento"
                                    type="text"
                                    class="input-control"
                                    placeholder="Ingrese documento"
                                >
                                <span v-if="form.errors.numero_documento" class="error-msg">{{ form.errors.numero_documento }}</span>
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

                <!-- Main Content -->
                <div class="main-content">
                    <div class="card">
                        <div class="card-header">
                            <h3>Datos Generales</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nombre Completo / Razón Social <span class="required">*</span></label>
                                <input
                                    v-model="form.razon_social_nombre"
                                    type="text"
                                    class="input-control"
                                    placeholder="Nombre del maestro o empresa"
                                >
                                <span v-if="form.errors.razon_social_nombre" class="error-msg">{{ form.errors.razon_social_nombre }}</span>
                            </div>

                            <div class="row-2">
                                <div class="form-group">
                                    <label>Teléfono / Celular</label>
                                    <input
                                        v-model="form.telefono"
                                        type="text"
                                        class="input-control"
                                        placeholder="+51"
                                    >
                                    <span v-if="form.errors.telefono" class="error-msg">{{ form.errors.telefono }}</span>
                                </div>
                                <div class="form-group">
                                    <label>Correo Electrónico</label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        class="input-control"
                                        placeholder="correo@ejemplo.com"
                                    >
                                    <span v-if="form.errors.email" class="error-msg">{{ form.errors.email }}</span>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Dirección</label>
                                <input
                                    v-model="form.direccion"
                                    type="text"
                                    class="input-control"
                                    placeholder="Dirección fiscal o domicilio"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="card" style="margin-top: 0.75rem;">
                        <div class="card-header">
                            <h3>Información Financiera</h3>
                        </div>
                        <div class="card-body">
                            <div class="row-2">
                                <div class="form-group">
                                    <label>Banco</label>
                                    <input
                                        v-model="form.banco"
                                        type="text"
                                        class="input-control"
                                        placeholder="Ej: BCP, Interbank"
                                    >
                                </div>
                                <div class="form-group">
                                    <label>Cuenta de Detracción</label>
                                    <input
                                        v-model="form.numero_cuenta_detraccion"
                                        type="text"
                                        class="input-control"
                                        placeholder="BN: 00-..."
                                    >
                                </div>
                            </div>
                            <div class="row-2">
                                <div class="form-group">
                                    <label>Número de Cuenta</label>
                                    <input
                                        v-model="form.numero_cuenta"
                                        type="text"
                                        class="input-control"
                                        placeholder="Cuenta bancaria"
                                    >
                                </div>
                                <div class="form-group">
                                    <label>CCI</label>
                                    <input
                                        v-model="form.cci"
                                        type="text"
                                        class="input-control"
                                        placeholder="Código Interbancario"
                                    >
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

.actions { display: flex; gap: 0.5rem; }

/* Grid Layout */
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

/* Controls */
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

select.input-control { cursor: pointer; }

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

/* Buttons */
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
