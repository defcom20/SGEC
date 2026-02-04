import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function usePermissions() {
    const page = usePage()

    /**
     * Verificar si el usuario tiene permiso para un módulo y acción
     */
    const can = (modulo, accion = 'ver') => {
        const user = page.props.auth?.user

        if (!user) return false

        // Admin tiene acceso a todo
        if (user.rol?.nombre === 'admin') return true

        // Buscar el módulo en los módulos del usuario
        const modules = page.props.permissions?.modules || []
        const module = modules.find(m => m.slug === modulo)

        if (!module) return false

        // Verificar si la acción está en las acciones del módulo
        return module.acciones?.includes(accion) || false
    }

    /**
     * Verificar si el usuario NO tiene permiso
     */
    const cannot = (modulo, accion = 'ver') => {
        return !can(modulo, accion)
    }

    /**
     * Verificar si el usuario es admin
     */
    const isAdmin = computed(() => {
        return page.props.auth?.user?.rol?.nombre === 'admin'
    })

    /**
     * Obtener módulos accesibles
     */
    const modules = computed(() => {
        return page.props.permissions?.modules || []
    })

    /**
     * Obtener acciones permitidas para un módulo
     */
    const getActions = (modulo) => {
        const module = modules.value.find(m => m.slug === modulo)
        return module?.acciones || []
    }

    /**
     * Verificar si tiene al menos uno de los permisos
     */
    const canAny = (permisos) => {
        return permisos.some(([modulo, accion]) => can(modulo, accion))
    }

    /**
     * Verificar si tiene todos los permisos
     */
    const canAll = (permisos) => {
        return permisos.every(([modulo, accion]) => can(modulo, accion))
    }

    /**
     * Verificar si un módulo está activo en el sistema
     */
    const isModuleActive = (slug) => {
        return modules.value.some(m => m.slug === slug)
    }

    return {
        can,
        cannot,
        isAdmin,
        modules,
        getActions,
        canAny,
        canAll,
        isModuleActive,
    }
}
