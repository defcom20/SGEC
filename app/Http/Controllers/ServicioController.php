<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $servicios = Servicio::query()
            ->when($search, function ($query, $search) {
                $query->where('descripcion', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Comercial/Servicios/Index', [
            'servicios' => $servicios,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Comercial/Servicios/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:servicios,codigo',
            'descripcion' => 'required|string|max:255',
            'unidad_medida' => 'required|string|max:20',
            'precio_referencial' => 'required|numeric|min:0',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $validated['uuid'] = (string) Str::uuid();
        $validated['user_id'] = auth()->id();
        $validated['usuario_creacion_id'] = auth()->id();

        Servicio::create($validated);

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Servicio $servicio)
    {
        return Inertia::render('Comercial/Servicios/Show', [
            'servicio' => $servicio,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Servicio $servicio)
    {
        return Inertia::render('Comercial/Servicios/Edit', [
            'servicio' => $servicio,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Servicio $servicio)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:servicios,codigo,' . $servicio->id,
            'descripcion' => 'required|string|max:255',
            'unidad_medida' => 'required|string|max:20',
            'precio_referencial' => 'required|numeric|min:0',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $validated['usuario_modificacion_id'] = auth()->id();

        $servicio->update($validated);

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Servicio $servicio)
    {
        $servicio->delete();

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio eliminado exitosamente.');
    }
}
