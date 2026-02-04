<script setup>
import { computed, watch, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const show = ref(false)
const type = ref('success') // 'success' | 'error'
const message = ref('')
const timeout = ref(null)

// Escuchar cambios en los props flash de Inertia
watch(() => page.props.flash, (flash) => {
    if (flash.success) {
        showToast(flash.success, 'success')
    } else if (flash.error) {
        showToast(flash.error, 'error')
    }
}, { deep: true })

const showToast = (msg, toastType) => {
    // Si ya hay uno visible, esperamos un poco
    if (show.value) {
        show.value = false
        setTimeout(() => {
            trigger(msg, toastType)
        }, 150)
    } else {
        trigger(msg, toastType)
    }
}

const trigger = (msg, toastType) => {
    message.value = msg
    type.value = toastType
    show.value = true
    
    // Auto ocultar después de 4 segundos
    if (timeout.value) clearTimeout(timeout.value)
    timeout.value = setTimeout(() => {
        show.value = false
    }, 4000)
}

const hide = () => {
    show.value = false
}
</script>

<template>
    <transition name="toast-fade">
        <div v-if="show" class="toast-wrapper">
            <div class="toast" :class="type">
                <div class="toast-icon">
                    <!-- Success Icon -->
                    <svg v-if="type === 'success'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <!-- Error Icon -->
                    <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                </div>
                
                <div class="toast-content">
                    <h4 class="toast-title">{{ type === 'success' ? '¡Éxito!' : 'Error' }}</h4>
                    <p class="toast-message">{{ message }}</p>
                </div>

                <button @click="hide" class="toast-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.toast-wrapper {
    position: fixed;
    top: 1.5rem;
    right: 1.5rem;
    z-index: 9999;
    max-width: 400px;
    width: 100%;
}

.toast {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: 12px;
    background: var(--card); /* Fondo modo oscuro/claro */
    border: 1px solid var(--border);
    box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    backdrop-filter: blur(8px);
    overflow: hidden;
    position: relative;
}

/* Indicador de color lateral */
.toast::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
}

.toast.success::before {
    background: #10b981; /* Green */
}

.toast.error::before {
    background: #ef4444; /* Red */
}

.toast.success .toast-icon {
    color: #10b981;
    background: rgba(16, 185, 129, 0.1);
}

.toast.error .toast-icon {
    color: #ef4444;
    background: rgba(239, 68, 68, 0.1);
}

.toast-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    flex-shrink: 0;
}

.toast-content {
    flex: 1;
}

.toast-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--foreground);
    margin: 0 0 2px 0;
}

.toast-message {
    font-size: 0.8125rem;
    color: var(--muted-foreground);
    line-height: 1.4;
    margin: 0;
}

.toast-close {
    background: transparent;
    border: none;
    color: var(--muted-foreground);
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    transition: all 0.2s;
}

.toast-close:hover {
    background: var(--muted);
    color: var(--foreground);
}

/* Animaciones */
.toast-fade-enter-active,
.toast-fade-leave-active {
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.toast-fade-enter-from,
.toast-fade-leave-to {
    opacity: 0;
    transform: translateY(-20px) scale(0.95);
}
</style>
