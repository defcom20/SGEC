<?php

namespace App\Http\Controllers;

use App\Models\Presupuesto;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class PresupuestoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $estado = $request->input('estado');

        $presupuestos = Presupuesto::with(['cliente', 'supervisor'])
            ->when($search, function ($query, $search) {
                $query->where('numero_presupuesto', 'like', "%{$search}%")
                    ->orWhereHas('cliente', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%");
                    });
            })
            ->when($estado, function ($query, $estado) {
                $query->where('estado', $estado);
            })
            ->latest('fecha_emision')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Comercial/Presupuestos/Index', [
            'presupuestos' => $presupuestos,
            'filters' => $request->only(['search', 'estado']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::where('estado', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'numero_documento']);

        $supervisores = User::orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Comercial/Presupuestos/Create', [
            'clientes' => $clientes,
            'supervisores' => $supervisores,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_presupuesto' => ['required', 'string', 'max:50', 'unique:presupuestos,numero_presupuesto'],
            'fecha_emision' => ['required', 'date'],
            'fecha_vencimiento' => ['required', 'date', 'after_or_equal:fecha_emision'],
            'cliente_id' => ['required', 'exists:clientes,id'],
            'persona_contacto' => ['nullable', 'string', 'max:150'],
            'supervisor_id' => ['required', 'exists:users,id'],
            'estado' => ['required', 'in:en_revision,aprobado,rechazado,vencido,en_ejecucion,finalizado'],
            'fecha_aceptacion' => ['nullable', 'date'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_finalizacion_estimada' => ['nullable', 'date'],
            'periodo_ejecucion_dias' => ['nullable', 'integer', 'min:1'],
            'base_imponible' => ['required', 'numeric', 'min:0'],
            'igv' => ['required', 'numeric', 'min:0'],
            'descuento_porcentaje' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'descuento_monto' => ['nullable', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string'],
        ]);

        // Agregar usuario autenticado
        $validated['user_id'] = auth()->id();
        $validated['usuario_creacion_id'] = auth()->id();

        Presupuesto::create($validated);

        return redirect()->route('presupuestos.index')
            ->with('success', 'Presupuesto creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Presupuesto $presupuesto)
    {
        $presupuesto->load(['cliente', 'supervisor']);

        $clientes = Cliente::where('estado', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'numero_documento']);

        $supervisores = User::orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Comercial/Presupuestos/Edit', [
            'presupuesto' => $presupuesto,
            'clientes' => $clientes,
            'supervisores' => $supervisores,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Presupuesto $presupuesto)
    {
        $validated = $request->validate([
            'numero_presupuesto' => ['required', 'string', 'max:50', 'unique:presupuestos,numero_presupuesto,' . $presupuesto->id],
            'fecha_emision' => ['required', 'date'],
            'fecha_vencimiento' => ['required', 'date', 'after_or_equal:fecha_emision'],
            'cliente_id' => ['required', 'exists:clientes,id'],
            'persona_contacto' => ['nullable', 'string', 'max:150'],
            'supervisor_id' => ['required', 'exists:users,id'],
            'estado' => ['required', 'in:en_revision,aprobado,rechazado,vencido,en_ejecucion,finalizado'],
            'fecha_aceptacion' => ['nullable', 'date'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_finalizacion_estimada' => ['nullable', 'date'],
            'periodo_ejecucion_dias' => ['nullable', 'integer', 'min:1'],
            'base_imponible' => ['required', 'numeric', 'min:0'],
            'igv' => ['required', 'numeric', 'min:0'],
            'descuento_porcentaje' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'descuento_monto' => ['nullable', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string'],
        ]);

        // Actualizar usuario modificación
        $validated['usuario_modificacion_id'] = auth()->id();

        $presupuesto->update($validated);

        return redirect()->route('presupuestos.index')
            ->with('success', 'Presupuesto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Presupuesto $presupuesto)
    {
        $presupuesto->delete();

        return redirect()->route('presupuestos.index')
            ->with('success', 'Presupuesto eliminado exitosamente.');
    }
}
