<script setup>
import { router, Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import ConfirmationModal from '@/components/ConfirmationModal.vue'

const props = defineProps({
    modulosPadre: Array,
})

// Estado del modal de confirmación
const showModal = ref(false)
const modalConfig = ref({
    title: '',
    message: '',
    confirmText: 'Confirmar',
    type: 'danger',
    target: null
})

const openConfirm = (modulo) => {
    // Restaurar el checkbox visualmente hasta que se confirme
    // Esto es un truco visual porque el v-model cambiaría antes de confirmar
    // En este caso usamos :checked y manejamos el evento manualmente, así que solo no hacemos nada aún
    
    const esPadre = modulo.children && modulo.children.length > 0;
    const accion = modulo.activo ? 'desactivar' : 'activar';
    
    modalConfig.value = {
        title: `${accion.charAt(0).toUpperCase() + accion.slice(1)} Módulo`,
        message: esPadre 
            ? `¿Estás seguro de ${accion} el módulo "${modulo.nombre}" y todos sus submódulos?`
            : `¿Estás seguro de ${accion} el módulo "${modulo.nombre}"?`,
        confirmText: modulo.activo ? 'Desactivar' : 'Activar',
        type: modulo.activo ? 'danger' : 'primary',
        target: modulo
    }
    
    showModal.value = true
}

const onConfirm = () => {
    const modulo = modalConfig.value.target
    if (modulo) {
        router.post(`/modulos/${modulo.uuid}/toggle`, {}, {
            preserveScroll: true,
            onFinish: () => {
                showModal.value = false
            }
        })
    }
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Gestión de Módulos', href: '/modulos' },
]
</script>

<template>
    <Head title="Gestión de Módulos" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="modulos-dashboard">
            <div class="header">
                <div>
                    <h1>Gestión de Módulos</h1>
                    <p class="subtitle">Administra la disponibilidad de funciones</p>
                </div>
            </div>

            <!-- Grid Principal Ultra Compacto -->
            <div class="parent-grid">
                <div 
                    v-for="moduloPadre in modulosPadre" 
                    :key="moduloPadre.id"
                    class="category-card"
                    :class="{ 'inactive': !moduloPadre.activo }"
                >
                    <!-- Encabezado de la Categoría -->
                    <div class="category-header">
                        <div class="category-info">
                            <span class="emoji">{{ moduloPadre.emoji || '📦' }}</span>
                            <div class="title-group">
                                <h2>{{ moduloPadre.nombre }}</h2>
                                <span class="badge" :class="moduloPadre.activo ? 'badge-success' : 'badge-neutral'">
                                    {{ moduloPadre.activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de Submódulos -->
                    <div class="submodules-list">
                        <div 
                            v-for="modulo in moduloPadre.children" 
                            :key="modulo.id"
                            class="submodule-row"
                            :class="{ 'row-inactive': !modulo.activo }"
                        >
                            <div class="row-content">
                                <div class="row-main">
                                    <h3>{{ modulo.nombre }}</h3>
                                    <p class="row-desc">{{ modulo.descripcion }}</p>
                                </div>
                                
                                <div class="row-meta">
                                    <span v-for="accion in modulo.acciones.slice(0, 3)" :key="accion" class="mini-tag">
                                        {{ accion }}
                                    </span>
                                    <span v-if="modulo.acciones.length > 3" class="mini-tag more">
                                        +{{ modulo.acciones.length - 3 }}
                                    </span>
                                </div>
                            </div>

                            <div class="row-action">
                                <button 
                                    @click="openConfirm(modulo)"
                                    class="switch-btn"
                                    :class="modulo.activo ? 'is-active' : ''"
                                    :disabled="!moduloPadre.activo"
                                    title="Activar/Desactivar"
                                >
                                    <span class="switch-handle"></span>
                                </button>
                            </div>
                        </div>
                        
                        <div v-if="!moduloPadre.children.length" class="empty-category">
                            Sin submódulos
                        </div>
                    </div>
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
            @confirm="onConfirm"
        />
    </AppLayout>
</template>

<style scoped>
/* ESTILO ULTRA COMPACTO */
.modulos-dashboard {
    padding: 1rem;
    width: 100%;
    margin: 0;
}

.header {
    margin-bottom: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
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

/* Grid Layout */
.parent-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1rem;
    align-items: start;
    width: 100%;
}

/* Card Categories */
.category-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    transition: box-shadow 0.2s;
}

.category-card:hover {
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    border-color: var(--border);
}

.category-card.inactive {
    opacity: 0.7;
    filter: grayscale(0.5);
}

.category-header {
    background: var(--muted);
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--border);
}

.category-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.emoji {
    font-size: 1.25rem;
    background: var(--background);
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    border: 1px solid var(--border);
}

.title-group h2 {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--foreground);
    margin: 0;
    line-height: 1.2;
}

.badge {
    display: inline-block;
    font-size: 0.65rem;
    padding: 1px 6px;
    border-radius: 4px;
    font-weight: 600;
    text-transform: uppercase;
    margin-top: 2px;
}

.badge-success {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
}

.badge-neutral {
    background: var(--input);
    color: var(--muted-foreground);
}

/* Submodules List */
.submodules-list {
    background: var(--card);
}

.submodule-row {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.75rem;
    transition: background 0.1s;
}

.submodule-row:last-child {
    border-bottom: none;
}

.submodule-row:hover {
    background: var(--accent);
}

.row-content {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.row-main h3 {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--foreground);
    margin: 0;
}

.row-desc {
    font-size: 0.75rem;
    color: var(--muted-foreground);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin: 0;
}

.row-meta {
    display: flex;
    gap: 4px;
    margin-top: 2px;
}

.mini-tag {
    font-size: 0.65rem;
    background: var(--background);
    color: var(--muted-foreground);
    padding: 0 4px;
    border-radius: 3px;
    border: 1px solid var(--border);
    line-height: 1.4;
}

/* Custom Switch Button */
.switch-btn {
    width: 36px;
    height: 20px;
    background: var(--input);
    border-radius: 20px;
    position: relative;
    border: none;
    cursor: pointer;
    transition: background 0.2s;
    padding: 0;
}

.switch-handle {
    width: 16px;
    height: 16px;
    background: white;
    border-radius: 50%;
    position: absolute;
    top: 2px;
    left: 2px;
    transition: transform 0.2s;
    box-shadow: 0 1px 2px rgba(0,0,0,0.2);
}

.switch-btn.is-active {
    background: var(--primary);
}

.switch-btn.is-active .switch-handle {
    transform: translateX(16px);
}

.switch-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.empty-category {
    padding: 1rem;
    text-align: center;
    font-size: 0.75rem;
    color: var(--muted-foreground);
    font-style: italic;
}

.row-inactive h3, .row-inactive .row-desc {
    opacity: 0.5;
    text-decoration: line-through;
}
</style>
