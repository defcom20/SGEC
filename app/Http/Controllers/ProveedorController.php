<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $proveedores = Proveedor::query()
            ->when($search, function ($query, $search) {
                $query->where('razon_social', 'like', "%{$search}%")
                    ->orWhere('numero_documento', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('rubro', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Operaciones/Proveedores/Index', [
            'proveedores' => $proveedores,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Operaciones/Proveedores/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_documento' => ['required', 'string', 'max:20'],
            'numero_documento' => ['required', 'string', 'max:20', 'unique:proveedors,numero_documento'],
            'razon_social' => ['required', 'string', 'max:255'],
            'rubro' => ['nullable', 'string', 'max:150'],
            'contacto' => ['nullable', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        // Agregar usuario autenticado
        $validated['user_id'] = auth()->id();
        $validated['usuario_creacion_id'] = auth()->id();

        Proveedor::create($validated);

        return redirect()->route('proveedors.index')
            ->with('success', 'Proveedor creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proveedor $proveedor)
    {
        $proveedor->load('articulos');
        return Inertia::render('Operaciones/Proveedores/Edit', [
            'proveedor' => $proveedor,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'tipo_documento' => ['required', 'string', 'max:20'],
            'numero_documento' => [
                'required',
                'string',
                'max:20',
                Rule::unique('proveedors')->ignore($proveedor->id)
            ],
            'razon_social' => ['required', 'string', 'max:255'],
            'rubro' => ['nullable', 'string', 'max:150'],
            'contacto' => ['nullable', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        // Actualizar usuario modificación
        $validated['usuario_modificacion_id'] = auth()->id();

        $proveedor->update($validated);

        return redirect()->route('proveedors.index')
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();

        return redirect()->route('proveedors.index')
            ->with('success', 'Proveedor eliminado exitosamente.');
    }
}
