<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class ArticuloController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $estado = $request->input('estado');

        $articulos = Articulo::with('proveedor')
            ->when($search, function ($query, $search) {
                $query->where('codigo', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%")
                    ->orWhereHas('proveedor', function ($q) use ($search) {
                        $q->where('razon_social', 'like', "%{$search}%");
                    });
            })
            ->when($estado, function ($query, $estado) {
                $query->where('estado', $estado);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Operaciones/Articulos/Index', [
            'articulos' => $articulos,
            'filters' => $request->only(['search', 'estado']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $proveedores = Proveedor::where('estado', 'activo')
            ->orderBy('razon_social')
            ->get(['id', 'razon_social', 'numero_documento']);

        return Inertia::render('Operaciones/Articulos/Create', [
            'proveedores' => $proveedores,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:articulos,codigo'],
            'descripcion' => ['required', 'string', 'max:255'],
            'unidad_medida' => ['required', 'string', 'max:20'],
            'proveedor_id' => ['required', 'exists:proveedors,id'],
            'precio_compra' => ['required', 'numeric', 'min:0'],
            'precio_venta' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'numeric', 'min:0'],
            'fecha_vencimiento' => ['nullable', 'date'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        // Agregar usuario autenticado
        $validated['user_id'] = auth()->id();
        $validated['usuario_creacion_id'] = auth()->id();

        Articulo::create($validated);

        return redirect()->route('articulos.index')
            ->with('success', 'Artículo creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Articulo $articulo)
    {
        $articulo->load('proveedor');

        $proveedores = Proveedor::where('estado', 'activo')
            ->orderBy('razon_social')
            ->get(['id', 'razon_social', 'numero_documento']);

        return Inertia::render('Operaciones/Articulos/Edit', [
            'articulo' => $articulo,
            'proveedores' => $proveedores,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Articulo $articulo)
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:50', Rule::unique('articulos')->ignore($articulo->id)],
            'descripcion' => ['required', 'string', 'max:255'],
            'unidad_medida' => ['required', 'string', 'max:20'],
            'proveedor_id' => ['required', 'exists:proveedors,id'],
            'precio_compra' => ['required', 'numeric', 'min:0'],
            'precio_venta' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'numeric', 'min:0'],
            'fecha_vencimiento' => ['nullable', 'date'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        // Actualizar usuario modificación
        $validated['usuario_modificacion_id'] = auth()->id();

        $articulo->update($validated);

        return redirect()->route('articulos.index')
            ->with('success', 'Artículo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Articulo $articulo)
    {
        $articulo->delete();

        return redirect()->route('articulos.index')
            ->with('success', 'Artículo eliminado exitosamente.');
    }
}
