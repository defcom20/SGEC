<script setup>
import { onMounted, onUnmounted } from 'vue';

const props = defineProps({
    show: Boolean,
    title: {
        type: String,
        default: 'Confirmar Acción'
    },
    message: {
        type: String,
        default: '¿Estás seguro de realizar esta acción?'
    },
    confirmText: {
        type: String,
        default: 'Confirmar'
    },
    cancelText: {
        type: String,
        default: 'Cancelar'
    },
    type: {
        type: String,
        default: 'danger' // danger, primary, warning
    }
});

const emit = defineEmits(['close', 'confirm']);

const close = () => emit('close');
const confirm = () => {
    emit('confirm');
    close();
};

const handleKeydown = (e) => {
    if (e.key === 'Escape' && props.show) close();
};

onMounted(() => document.addEventListener('keydown', handleKeydown));
onUnmounted(() => document.removeEventListener('keydown', handleKeydown));
</script>

<template>
    <transition name="modal-fade">
        <div v-if="show" class="modal-backdrop" @click.self="close">
            <div class="modal-container">
                <div class="modal-header">
                    <h3 class="modal-title">{{ title }}</h3>
                    <button @click="close" class="close-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                
                <div class="modal-body">
                    <p>{{ message }}</p>
                </div>
                
                <div class="modal-footer">
                    <button @click="close" class="btn btn-secondary">
                        {{ cancelText }}
                    </button>
                    <button 
                        @click="confirm" 
                        class="btn"
                        :class="type === 'danger' ? 'btn-danger' : 'btn-primary'"
                    >
                        {{ confirmText }}
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 50;
    backdrop-filter: blur(4px);
}

.modal-container {
    background: var(--card);
    width: 90%;
    max-width: 400px;
    border-radius: 12px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--border);
    overflow: hidden;
}

.modal-header {
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--border);
    background: var(--muted);
}

.modal-title {
    font-weight: 600;
    font-size: 1rem;
    color: var(--foreground);
    margin: 0;
}

.close-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--muted-foreground);
    padding: 0.25rem;
    border-radius: 4px;
    transition: color 0.2s;
}

.close-btn:hover {
    color: var(--foreground);
    background: rgba(0,0,0,0.05);
}

.modal-body {
    padding: 1.25rem;
    color: var(--muted-foreground);
    font-size: 0.9rem;
    line-height: 1.5;
}

.modal-footer {
    padding: 1rem 1.25rem;
    background: var(--muted);
    background-opacity: 0.5;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

.btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}

.btn-secondary {
    background: var(--background);
    border: 1px solid var(--input);
    color: var(--foreground);
}

.btn-secondary:hover {
    background: var(--accent);
}

.btn-primary {
    background: var(--primary);
    color: var(--primary-foreground);
}

.btn-danger {
    background: var(--destructive);
    color: var(--destructive-foreground);
}

.btn-primary:hover, .btn-danger:hover {
    opacity: 0.9;
}

/* Transitions */
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

.modal-fade-enter-active .modal-container,
.modal-fade-leave-active .modal-container {
    transition: transform 0.3s ease;
}

.modal-fade-enter-from .modal-container,
.modal-fade-leave-to .modal-container {
    transform: scale(0.95);
}
</style>
