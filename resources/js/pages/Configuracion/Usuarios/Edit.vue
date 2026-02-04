<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps({
    usuario: {
        type: Object,
        default: null,
    },
    roles: Array,
})

const isEditing = computed(() => !!props.usuario)

const form = useForm({
    name: props.usuario?.name || '',
    email: props.usuario?.email || '',
    password: '',
    password_confirmation: '',
    rol_id: props.usuario?.rol_id || '',
})

const submit = () => {
    if (isEditing.value) {
        form.put(`/usuarios/${props.usuario.id}`, {
            preserveScroll: true,
        })
    } else {
        form.post('/usuarios', {
            preserveScroll: true,
        })
    }
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Usuarios', href: '/usuarios' },
    { title: isEditing.value ? 'Editar' : 'Crear', href: '#' },
]
</script>

<template>
    <Head :title="isEditing ? 'Editar Usuario' : 'Nuevo Usuario'" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="usuario-dashboard">
            <div class="header">
                <div>
                    <h1>{{ isEditing ? 'Editar Usuario' : 'Crear Usuario' }}</h1>
                    <p class="subtitle">
                        {{ isEditing ? 'Actualiza los datos del usuario' : 'Registra un nuevo usuario' }}
                    </p>
                </div>
                
                <div class="header-actions">
                    <Link 
                        href="/usuarios" 
                        class="btn btn-secondary"
                    >
                        Cancelar
                    </Link>
                    <button 
                        @click="submit"
                        class="btn btn-primary"
                        :disabled="form.processing"
                    >
                        <span v-if="!form.processing">{{ isEditing ? 'Guardar Cambios' : 'Crear Usuario' }}</span>
                        <span v-else>Guardando...</span>
                    </button>
                </div>
            </div>

            <form @submit.prevent="submit" class="usuario-grid">
                <!-- Tarjeta: Información de la Cuenta -->
                <div class="card">
                    <div class="card-header">
                        <div class="icon-box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <h3>Información de la Cuenta</h3>
                    </div>
                    
                    <div class="card-content grid-2-col">
                        <div class="form-group">
                            <label for="name">Nombre Completo <span class="required">*</span></label>
                            <input 
                                id="name"
                                v-model="form.name"
                                type="text" 
                                placeholder="Ej. Juan Pérez"
                                :class="{ 'error': form.errors.name }"
                                required
                            >
                            <span v-if="form.errors.name" class="error-msg">{{ form.errors.name }}</span>
                        </div>

                        <div class="form-group">
                            <label for="email">Correo Electrónico <span class="required">*</span></label>
                            <input 
                                id="email"
                                v-model="form.email"
                                type="email" 
                                placeholder="usuario@empresa.com"
                                :class="{ 'error': form.errors.email }"
                                required
                            >
                            <span v-if="form.errors.email" class="error-msg">{{ form.errors.email }}</span>
                        </div>

                        <div class="form-group span-2">
                            <label for="rol_id">Rol Asignado <span class="required">*</span></label>
                            <select 
                                id="rol_id"
                                v-model="form.rol_id"
                                :class="{ 'error': form.errors.rol_id }"
                                required
                            >
                                <option value="">Seleccionar Rol...</option>
                                <option 
                                    v-for="rol in roles" 
                                    :key="rol.id"
                                    :value="rol.id"
                                >
                                    {{ rol.nombre }}
                                </option>
                            </select>
                            <span v-if="form.errors.rol_id" class="error-msg">{{ form.errors.rol_id }}</span>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta: Seguridad -->
                <div class="card">
                    <div class="card-header">
                        <div class="icon-box accent">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <h3>Seguridad</h3>
                    </div>

                    <div class="card-content">
                        <div class="alert-info" v-if="isEditing">
                            <p>Deja estos campos vacíos si no deseas cambiar la contraseña.</p>
                        </div>

                        <div class="grid-2-col">
                            <div class="form-group">
                                <label for="password">
                                    Contraseña
                                    <span v-if="!isEditing" class="required">*</span>
                                </label>
                                <input 
                                    id="password"
                                    v-model="form.password"
                                    type="password" 
                                    placeholder="••••••••"
                                    :required="!isEditing"
                                    :class="{ 'error': form.errors.password }"
                                >
                                <span v-if="form.errors.password" class="error-msg">{{ form.errors.password }}</span>
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">
                                    Confirmar Contraseña
                                    <span v-if="!isEditing" class="required">*</span>
                                </label>
                                <input 
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password" 
                                    placeholder="••••••••"
                                    :required="!isEditing"
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
.usuario-dashboard {
    padding: 1rem;
    max-width: 1000px;
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
    margin: 0;
    line-height: 1.2;
    letter-spacing: -0.025em;
}

.subtitle {
    color: var(--muted-foreground);
    font-size: 0.875rem;
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 0.5rem;
}

/* Grid layout */
.usuario-grid {
    display: grid;
    gap: 1rem;
}

/* Cards */
.card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
}

.card-header {
    background: var(--muted);
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.65rem;
    border-bottom: 1px solid var(--border);
}

.icon-box {
    background: var(--background);
    width: 28px;
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
    color: var(--chart-4); /* Color distintivo para seguridad */
}

.card-header h3 {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--foreground);
    margin: 0;
}
.card-content {
    padding: 1rem;
}

.grid-2-col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.span-2 {
    grid-column: span 2;
}

/* Forms */
.form-group label {
    display: block;
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--foreground);
    margin-bottom: 0.25rem;
}

.required {
    color: var(--destructive);
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 0.4rem 0.65rem;
    background: var(--background);
    border: 1px solid var(--input);
    border-radius: 4px;
    font-size: 0.85rem;
    color: var(--foreground);
    transition: border-color 0.2s;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 2px var(--ring);
}

.form-group input::placeholder {
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

.row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.alert-info {
    font-size: 0.75rem;
    background: var(--accent);
    color: var(--muted-foreground);
    padding: 0.5rem 0.75rem;
    border-radius: 4px;
    border: 1px solid var(--border);
}

/* Buttons */
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
    text-decoration: none;
}

.btn-primary {
    background: var(--primary);
    color: var(--primary-foreground);
}

.btn-primary:hover {
    opacity: 0.9;
}

.btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.btn-secondary {
    background: var(--secondary);
    color: var(--secondary-foreground);
}

.btn-secondary:hover {
    opacity: 0.9;
}

@media (max-width: 640px) {
    .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .header-actions {
        width: 100%;
        justify-content: stretch;
    }
    
    .btn {
        flex: 1;
        justify-content: center;
    }
    
    .row-2 {
        grid-template-columns: 1fr;
    }
}
</style>
