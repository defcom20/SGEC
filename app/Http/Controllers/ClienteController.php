<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $clientes = Cliente::query()
            ->when($search, function ($query, $search) {
                $query->where('razon_social', 'like', "%{$search}%")
                    ->orWhere('nombre', 'like', "%{$search}%")
                    ->orWhere('numero_documento', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Comercial/Clientes/Index', [
            'clientes' => $clientes,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Comercial/Clientes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_persona' => 'required|string',
            'tipo_documento' => 'required|string',
            'numero_documento' => 'required|string|unique:clientes,numero_documento',
            'razon_social' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string',
            'direccion' => 'nullable|string',
            'estado' => 'boolean',
        ]);

        // Asegurar que nombre también se llene (duplicado de razon_social) para compatibilidad
        $validated['nombre'] = $validated['razon_social'];

        $validated['user_id'] = auth()->id();
        $validated['usuario_creacion_id'] = auth()->id();

        Cliente::create($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        return Inertia::render('Comercial/Clientes/Show', [
            'cliente' => $cliente,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        return Inertia::render('Comercial/Clientes/Edit', [
            'cliente' => $cliente,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'tipo_persona' => 'required|string',
            'tipo_documento' => 'required|string',
            'numero_documento' => 'required|string|unique:clientes,numero_documento,' . $cliente->id,
            'razon_social' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string',
            'direccion' => 'nullable|string',
            'estado' => 'boolean',
        ]);

        $validated['nombre'] = $validated['razon_social'];
        $validated['usuario_modificacion_id'] = auth()->id();

        $cliente->update($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }
}
