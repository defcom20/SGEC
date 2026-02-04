<?php

namespace App\Http\Controllers;

use App\Models\Subcontratista;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubcontratistaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $subcontratistas = Subcontratista::query()
            ->when($search, function ($query, $search) {
                $query->where('razon_social_nombre', 'like', "%{$search}%")
                    ->orWhere('numero_documento', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Operaciones/Subcontratistas/Index', [
            'subcontratistas' => $subcontratistas,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Operaciones/Subcontratistas/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|string|max:50', // MAESTRO_OBRA, EMPRESA
            'tipo_documento' => 'required|string|max:20',
            'numero_documento' => 'required|string|max:20|unique:subcontratistas,numero_documento',
            'razon_social_nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'banco' => 'nullable|string|max:100',
            'numero_cuenta' => 'nullable|string|max:50',
            'cci' => 'nullable|string|max:50',
            'numero_cuenta_detraccion' => 'nullable|string|max:50',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['usuario_creacion_id'] = auth()->id();

        Subcontratista::create($validated);

        return redirect()->route('subcontratistas.index')
            ->with('success', 'Subcontratista creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subcontratista $subcontratista)
    {
        return Inertia::render('Operaciones/Subcontratistas/Show', [
            'subcontratista' => $subcontratista,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subcontratista $subcontratista)
    {
        return Inertia::render('Operaciones/Subcontratistas/Edit', [
            'subcontratista' => $subcontratista,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subcontratista $subcontratista)
    {
        $validated = $request->validate([
            'tipo' => 'required|string|max:50',
            'tipo_documento' => 'required|string|max:20',
            'numero_documento' => 'required|string|max:20|unique:subcontratistas,numero_documento,' . $subcontratista->id,
            'razon_social_nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'banco' => 'nullable|string|max:100',
            'numero_cuenta' => 'nullable|string|max:50',
            'cci' => 'nullable|string|max:50',
            'numero_cuenta_detraccion' => 'nullable|string|max:50',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $validated['usuario_modificacion_id'] = auth()->id();

        $subcontratista->update($validated);

        return redirect()->route('subcontratistas.index')
            ->with('success', 'Subcontratista actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subcontratista $subcontratista)
    {
        $subcontratista->delete();

        return redirect()->route('subcontratistas.index')
            ->with('success', 'Subcontratista eliminado exitosamente.');
    }
}
