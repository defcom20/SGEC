<script setup>
import { ref, computed } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ConfirmationModal from '@/components/ConfirmationModal.vue'

const props = defineProps({
    usuarios: Array,
})

const searchQuery = ref('')
// Estado del modal de confirmación
const showModal = ref(false)
const modalConfig = ref({
    title: '',
    message: '',
    confirmText: 'Eliminar',
    type: 'danger',
    target: null
})

const filteredUsuarios = computed(() => {
    if (!searchQuery.value) return props.usuarios
    
    const query = searchQuery.value.toLowerCase()
    return props.usuarios.filter(usuario => 
        usuario.name.toLowerCase().includes(query) ||
        usuario.email.toLowerCase().includes(query) ||
        (usuario.rol && usuario.rol.nombre.toLowerCase().includes(query))
    )
})

const confirmDelete = (usuario) => {
    modalConfig.value = {
        title: 'Eliminar Usuario',
        message: `¿Estás seguro de que deseas eliminar permanentemente al usuario "${usuario.name}"? Esta acción no se puede deshacer.`,
        confirmText: 'Eliminar',
        type: 'danger',
        target: usuario
    }
    showModal.value = true
}

const onDeleteConfirm = () => {
    const usuario = modalConfig.value.target
    if (usuario) {
        router.delete(`/usuarios/${usuario.id}`, {
            preserveScroll: true,
            onFinish: () => {
                showModal.value = false
            }
        })
    }
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Configuración', href: '#' },
    { title: 'Usuarios', href: '/usuarios' },
]
</script>

<template>
    <Head title="Usuarios" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="usuarios-manager">
            <div class="header">
                <div>
                    <h1>Gestión de Usuarios</h1>
                    <p class="subtitle">
                        Usuarios y roles del sistema
                    </p>
                </div>
                <Link 
                    href="/usuarios/create" 
                    class="btn btn-primary"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Nuevo
                </Link>
            </div>

            <div class="controls-bar">
                <div class="search-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input 
                        v-model="searchQuery"
                        type="text" 
                        placeholder="Buscar usuarios..."
                    >
                </div>
            </div>

            <div class="usuarios-table-container">
                <table class="usuarios-table">
                    <thead>
                        <tr>
                            <th style="width: 40px"></th> <!-- Avatar -->
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Rol Asignado</th>
                            <th>Estado</th>
                            <th class="actions-column"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr 
                            v-for="usuario in filteredUsuarios" 
                            :key="usuario.id"
                            class="usuario-row"
                        >
                            <td class="td-avatar">
                                <div class="avatar">
                                    {{ usuario.name.charAt(0).toUpperCase() }}
                                </div>
                            </td>
                            <td>
                                <span class="usuario-name">{{ usuario.name }}</span>
                            </td>
                            <td>
                                <span class="email">{{ usuario.email }}</span>
                            </td>
                            <td>
                                <span class="rol-badge" v-if="usuario.rol">
                                    {{ usuario.rol.nombre }}
                                </span>
                                <span class="rol-badge sin-rol" v-else>
                                    Sin rol
                                </span>
                            </td>
                            <td>
                                <span class="status-indicator active">
                                    Activo
                                </span>
                            </td>
                            <td class="actions-column">
                                <div class="actions">
                                    <Link 
                                        :href="`/usuarios/${usuario.id}/edit`" 
                                        class="action-btn edit"
                                        title="Editar Usuario"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </Link>
                                    <button 
                                        @click="confirmDelete(usuario)"
                                        class="action-btn delete"
                                        title="Eliminar Usuario"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="filteredUsuarios.length === 0" class="empty-state">
                    <p>No se encontraron usuarios</p>
                </div>
            </div>
        </div>

        <!-- Confirm Modal -->
        <ConfirmationModal 
            :show="showModal"
            :title="modalConfig.title"
            :message="modalConfig.message"
            :confirm-text="modalConfig.confirmText"
            :type="modalConfig.type"
            @close="showModal = false"
            @confirm="onDeleteConfirm"
        />
    </AppLayout>
</template>

<style scoped>
.usuarios-manager {
    padding: 1rem;
    width: 100%;
    margin: 0;
}

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
    font-size: 0.85rem;
    margin: 0;
}

.controls-bar {
    margin-bottom: 0.75rem;
}

.search-box {
    position: relative;
    max-width: 300px;
}

.search-box svg {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--muted-foreground);
}

.search-box input {
    width: 100%;
    padding: 0.45rem 0.75rem 0.45rem 2.25rem;
    border: 1px solid var(--input);
    background: var(--background);
    color: var(--foreground);
    border-radius: 6px;
    font-size: 0.85rem;
    transition: var(--transition-all);
}

.search-box input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 2px var(--ring);
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.45rem 0.85rem;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    transition: opacity 0.2s;
    text-decoration: none;
    border: none;
}

.btn-primary {
    background: var(--primary);
    color: var(--primary-foreground);
}

.btn-primary:hover {
    opacity: 0.9;
}

.usuarios-table-container {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
}

.usuarios-table {
    width: 100%;
    border-collapse: collapse;
}

.usuarios-table th {
    background: var(--muted);
    padding: 0.65rem 1rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--muted-foreground);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid var(--border);
}

.usuarios-table td {
    padding: 0.65rem 1rem;
    font-size: 0.85rem;
    color: var(--foreground);
    border-bottom: 1px solid var(--border);
    background: var(--card);
}

.usuario-row:last-child td {
    border-bottom: none;
}

.usuario-row:hover td {
    background: var(--accent);
}

.td-avatar {
    width: 40px;
    padding-right: 0 !important;
}

.avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--primary);
    color: var(--primary-foreground);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.75rem;
}

.usuario-name {
    font-weight: 600;
    color: var(--foreground);
}

.email {
    color: var(--muted-foreground);
    font-size: 0.8rem;
}

.rol-badge {
    display: inline-block;
    padding: 2px 8px;
    background: rgba(var(--primary-rgb), 0.1); /* Fallback si no hay variable RGB */
    background: var(--secondary);
    color: var(--foreground);
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.rol-badge.sin-rol {
    background: var(--muted);
    color: var(--muted-foreground);
    font-style: italic;
}

.status-indicator {
    display: inline-flex;
    align-items: center;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-indicator::before {
    content: '';
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    margin-right: 6px;
}

.status-indicator.active {
    color: #10b981;
}

.status-indicator.active::before {
    background: #10b981;
}

.actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    padding: 0;
    color: var(--muted-foreground);
    background: transparent;
}

.action-btn:hover {
    background: var(--muted);
    color: var(--foreground);
}

.edit:hover {
    color: var(--primary);
    background: rgba(var(--primary-rgb), 0.1); /* Fallback si no hay variable RGB, usará solo color */
    background: var(--accent);
}

.delete:hover {
    color: var(--destructive);
    background: rgba(239, 68, 68, 0.1);
}

.empty-state {
    text-align: center;
    padding: 2rem;
    color: var(--muted-foreground);
    font-size: 0.85rem;
}

@media (max-width: 640px) {
    .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .search-box {
        max-width: 100%;
    }
    
    .usuarios-table th, .usuarios-table td {
        padding: 0.5rem;
    }
    
    .email {
        display: none; /* Ocultar email en móviles para espacio */
    }
}
</style>
