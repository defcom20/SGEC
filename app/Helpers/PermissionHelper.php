<?php

use App\Models\Modulo;

if (!function_exists('can_access')) {
    /**
     * Verificar si el usuario tiene permiso para un módulo y acción
     *
     * @param string $modulo
     * @param string $accion
     * @return bool
     */
    function can_access(string $modulo, string $accion = 'ver'): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        // Verificar si el módulo está activo en el sistema
        $moduloActivo = Modulo::where('slug', $modulo)
            ->where('activo', true)
            ->exists();

        if (!$moduloActivo) {
            return false; // Módulo desactivado por el administrador
        }

        // Admin tiene acceso a todo
        if ($user->rol?->nombre === 'admin') {
            return true;
        }

        // Verificar permiso específico
        return $user->rol?->permisos()
            ->where('modulo', $modulo)
            ->where('accion', $accion)
            ->exists() ?? false;
    }
}

if (!function_exists('get_user_modules')) {
    /**
     * Obtener módulos accesibles para el usuario actual (estructura jerárquica)
     *
     * @return array
     */
    function get_user_modules(): array
    {
        $user = auth()->user();

        if (!$user) {
            return [];
        }

        // Obtener módulos padre activos con sus hijos
        $modulosPadre = Modulo::activos()
            ->visiblesEnMenu()
            ->padres()
            ->with([
                'children' => function ($query) {
                    $query->activos()->visiblesEnMenu()->ordenados();
                }
            ])
            ->ordenados()
            ->get();

        // Admin ve todos los módulos
        if ($user->rol?->nombre === 'admin') {
            return $modulosPadre->map(function ($moduloPadre) {
                return [
                    'id' => $moduloPadre->id,
                    'uuid' => $moduloPadre->uuid,
                    'nombre' => $moduloPadre->nombre,
                    'slug' => $moduloPadre->slug,
                    'icono' => $moduloPadre->icono,
                    'ruta' => $moduloPadre->ruta,
                    'descripcion' => $moduloPadre->descripcion,
                    'nivel' => $moduloPadre->nivel,
                    'orden' => $moduloPadre->orden,
                    'children' => $moduloPadre->children->map(function ($child) {
                        return [
                            'id' => $child->id,
                            'uuid' => $child->uuid,
                            'nombre' => $child->nombre,
                            'slug' => $child->slug,
                            'icono' => $child->icono,
                            'ruta' => $child->ruta,
                            'descripcion' => $child->descripcion,
                            'acciones' => $child->acciones,
                            'orden' => $child->orden,
                        ];
                    })->toArray(),
                ];
            })->toArray();
        }

        // Filtrar módulos según permisos del usuario
        $userPermissions = $user->rol?->permisos()
            ->pluck('slug')
            ->map(function ($slug) {
                // Extraer el módulo del slug (ej: 'clientes.ver' -> 'clientes')
                return explode('.', $slug)[0];
            })
            ->unique()
            ->toArray() ?? [];

        return $modulosPadre->map(function ($moduloPadre) use ($userPermissions) {
            // Filtrar hijos según permisos
            $childrenFiltrados = $moduloPadre->children->filter(function ($child) use ($userPermissions) {
                return in_array($child->slug, $userPermissions);
            });

            // Solo incluir módulo padre si tiene hijos accesibles
            if ($childrenFiltrados->isEmpty()) {
                return null;
            }

            return [
                'id' => $moduloPadre->id,
                'uuid' => $moduloPadre->uuid,
                'nombre' => $moduloPadre->nombre,
                'slug' => $moduloPadre->slug,
                'icono' => $moduloPadre->icono,
                'ruta' => $moduloPadre->ruta,
                'descripcion' => $moduloPadre->descripcion,
                'nivel' => $moduloPadre->nivel,
                'orden' => $moduloPadre->orden,
                'children' => $childrenFiltrados->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'uuid' => $child->uuid,
                        'nombre' => $child->nombre,
                        'slug' => $child->slug,
                        'icono' => $child->icono,
                        'ruta' => $child->ruta,
                        'descripcion' => $child->descripcion,
                        'acciones' => $child->acciones,
                        'orden' => $child->orden,
                    ];
                })->values()->toArray(),
            ];
        })->filter()->values()->toArray();
    }
}

if (!function_exists('get_module_actions')) {
    /**
     * Obtener acciones permitidas para un módulo específico
     *
     * @param string $modulo
     * @return array
     */
    function get_module_actions(string $modulo): array
    {
        $user = auth()->user();

        if (!$user) {
            return [];
        }

        // Verificar si el módulo está activo
        $moduloModel = Modulo::where('slug', $modulo)
            ->where('activo', true)
            ->first();

        if (!$moduloModel) {
            return []; // Módulo desactivado
        }

        // Admin tiene todas las acciones del módulo
        if ($user->rol?->nombre === 'admin') {
            return $moduloModel->acciones ?? [];
        }

        // Obtener acciones permitidas del usuario
        return $user->rol?->permisos()
            ->where('modulo', $modulo)
            ->pluck('accion')
            ->toArray() ?? [];
    }
}

if (!function_exists('get_all_modules')) {
    /**
     * Obtener TODOS los módulos del sistema (para administración)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function get_all_modules()
    {
        return Modulo::ordenados()->get();
    }
}

if (!function_exists('toggle_module')) {
    /**
     * Activar/desactivar un módulo
     *
     * @param string $slug
     * @param bool $activo
     * @return bool
     */
    function toggle_module(string $slug, bool $activo): bool
    {
        $modulo = Modulo::where('slug', $slug)->first();

        if (!$modulo) {
            return false;
        }

        $modulo->activo = $activo;
        $modulo->usuario_modificacion_id = auth()->id();

        return $modulo->save();
    }
}
