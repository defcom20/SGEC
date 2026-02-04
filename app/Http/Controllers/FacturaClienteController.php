<?php

namespace App\Http\Controllers;

use App\Http\Requests\FacturaClienteStoreRequest;
use App\Http\Requests\FacturaClienteUpdateRequest;
use App\Models\FacturaCliente;
use App\Models\Cliente;
use App\Models\Presupuesto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FacturaClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $facturas = FacturaCliente::query()
            ->with(['cliente', 'presupuesto'])
            ->when($search, function ($query, $search) {
                $query->where('numero_factura', 'like', "%{$search}%")
                    ->orWhereHas('cliente', function ($q) use ($search) {
                        $q->where('razon_social', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Finanzas/FacturaClientes/Index', [
            'facturas' => $facturas,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::select('id', 'razon_social', 'numero_documento')->orderBy('razon_social')->get();
        // Assuming Presupuesto model exists and has a reference/description
        $presupuestos = Presupuesto::select('id', 'cliente_id', 'numero_presupuesto', 'observaciones', 'base_imponible', 'igv', 'total', 'descuento_monto', 'descuento_porcentaje')->orderBy('numero_presupuesto', 'desc')->get();

        return Inertia::render('Finanzas/FacturaClientes/Create', [
            'clientes' => $clientes,
            'presupuestos' => $presupuestos,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_factura' => 'required|string|max:50',
            'serie' => 'required|string|max:20',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_emision',
            'cliente_id' => 'required|exists:clientes,id',
            'presupuesto_id' => 'required|exists:presupuestos,id',
            'base_imponible' => 'required|numeric|min:0',
            'igv' => 'required|numeric|min:0',
            'descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'descuento_descripcion' => 'nullable|string|max:255',
            'descuento_monto' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'porcentaje_detraccion' => 'nullable|numeric|min:0|max:100',
            'monto_detraccion' => 'nullable|numeric|min:0',
            'estado' => 'required|in:pendiente,pago_parcial,pagado_completo',
            'observaciones' => 'nullable|string',
        ]);

        // Auto-calculated fields or defaults
        $validated['monto_pagado'] = 0;
        $validated['monto_pendiente'] = $validated['total'];

        $validated['user_id'] = auth()->id();
        $validated['usuario_creacion_id'] = auth()->id();

        FacturaCliente::create($validated);

        return redirect()->route('factura-clientes.index')
            ->with('success', 'Factura creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FacturaCliente $facturaCliente)
    {
        $facturaCliente->load(['cliente', 'presupuesto', 'pagoClientes']);

        return Inertia::render('Finanzas/FacturaClientes/Show', [
            'factura' => $facturaCliente,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FacturaCliente $facturaCliente)
    {
        $clientes = Cliente::select('id', 'razon_social', 'numero_documento')->orderBy('razon_social')->get();
        $presupuestos = Presupuesto::select('id', 'cliente_id', 'numero_presupuesto', 'observaciones', 'base_imponible', 'igv', 'total', 'descuento_monto', 'descuento_porcentaje')->orderBy('numero_presupuesto', 'desc')->get();

        return Inertia::render('Finanzas/FacturaClientes/Edit', [
            'factura' => $facturaCliente,
            'clientes' => $clientes,
            'presupuestos' => $presupuestos,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FacturaCliente $facturaCliente)
    {
        $validated = $request->validate([
            'numero_factura' => 'required|string|max:50',
            'serie' => 'required|string|max:20',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_emision',
            'cliente_id' => 'required|exists:clientes,id',
            'presupuesto_id' => 'required|exists:presupuestos,id',
            'base_imponible' => 'required|numeric|min:0',
            'igv' => 'required|numeric|min:0',
            'descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'descuento_descripcion' => 'nullable|string|max:255',
            'descuento_monto' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'porcentaje_detraccion' => 'nullable|numeric|min:0|max:100',
            'monto_detraccion' => 'nullable|numeric|min:0',
            'estado' => 'required|in:pendiente,pago_parcial,pagado_completo',
            'observaciones' => 'nullable|string',
        ]);

        $validated['usuario_modificacion_id'] = auth()->id();

        // Recalculate pending amount if total changed (simplified logic, usually needs more care with existing payments)
        if ($facturaCliente->monto_pagado == 0) {
            $validated['monto_pendiente'] = $validated['total'];
        } else {
            $validated['monto_pendiente'] = $validated['total'] - $facturaCliente->monto_pagado;
        }

        $facturaCliente->update($validated);

        return redirect()->route('factura-clientes.index')
            ->with('success', 'Factura actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FacturaCliente $facturaCliente)
    {
        $facturaCliente->delete();

        return redirect()->route('factura-clientes.index')
            ->with('success', 'Factura eliminada exitosamente.');
    }
}
