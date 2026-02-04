<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RolController extends Controller
{
    /**
     * Display a listing of roles with their permissions.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $roles = Rol::with(['permisos', 'users'])
            ->withCount('users')
            ->when($search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Configuracion/Roles/Index', [
            'roles' => $roles,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(Request $request): Response
    {
        $permisos = Permiso::orderBy('modulo')
            ->orderBy('accion')
            ->get();

        // Agrupar permisos por módulo
        $modulos = $permisos->groupBy('modulo')->map(function ($permisos, $modulo) {
            return [
                'nombre' => $modulo,
                'permisos' => $permisos->toArray()
            ];
        })->values();

        return Inertia::render('Configuracion/Roles/Create', [
            'modulos' => $modulos,
        ]);
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:rols,nombre',
            'descripcion' => 'nullable|string|max:500',
            'permisos' => 'nullable|array',
            'permisos.*' => 'exists:permisos,id',
        ]);

        $rol = Rol::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
        ]);

        // Sincronizar permisos
        if (isset($validated['permisos'])) {
            $rol->permisos()->sync($validated['permisos']);
        }

        return redirect()
            ->route('rols.index')
            ->with('success', 'Rol creado exitosamente.');
    }

    /**
     * Display the specified role.
     */
    public function show(Request $request, Rol $rol): Response
    {
        $rol->load(['permisos', 'users']);

        return Inertia::render('Configuracion/Roles/Show', [
            'rol' => $rol,
        ]);
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Request $request, Rol $rol): Response
    {
        $rol->load('permisos');

        $permisos = Permiso::orderBy('modulo')
            ->orderBy('accion')
            ->get();

        // Agrupar permisos por módulo
        $modulos = $permisos->groupBy('modulo')->map(function ($permisos, $modulo) {
            return [
                'nombre' => $modulo,
                'permisos' => $permisos->toArray()
            ];
        })->values();

        // IDs de permisos del rol
        $rolePermissions = $rol->permisos->pluck('id')->toArray();

        return Inertia::render('Configuracion/Roles/Edit', [
            'rol' => $rol,
            'modulos' => $modulos,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Rol $rol): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:rols,nombre,' . $rol->id,
            'descripcion' => 'nullable|string|max:500',
            'permisos' => 'nullable|array',
            'permisos.*' => 'exists:permisos,id',
        ]);

        $rol->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
        ]);

        // Sincronizar permisos
        if (isset($validated['permisos'])) {
            $rol->permisos()->sync($validated['permisos']);
        } else {
            $rol->permisos()->detach();
        }

        return redirect()
            ->route('rols.index')
            ->with('success', 'Rol actualizado exitosamente.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Request $request, Rol $rol): RedirectResponse
    {
        // Verificar si el rol tiene usuarios asignados
        if ($rol->users()->count() > 0) {
            return redirect()
                ->route('rols.index')
                ->with('error', 'No se puede eliminar el rol porque tiene usuarios asignados.');
        }

        $rol->delete();

        return redirect()
            ->route('rols.index')
            ->with('success', 'Rol eliminado exitosamente.');
    }
}
