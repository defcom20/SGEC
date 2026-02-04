<?php

namespace App\Http\Controllers;

use App\Models\FacturaSubcontratista;
use App\Models\Subcontratista;
use App\Models\OrdenServicio;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FacturaSubcontratistaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $facturas = FacturaSubcontratista::query()
            ->with(['subcontratista', 'ordenServicio'])
            ->when($search, function ($query, $search) {
                $query->where('numero_documento', 'like', "%{$search}%")
                    ->orWhere('serie', 'like', "%{$search}%")
                    ->orWhereHas('subcontratista', function ($q) use ($search) {
                        $q->where('razon_social_nombre', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Finanzas/FacturaSubcontratistas/Index', [
            'facturas' => $facturas,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        $subcontratistas = Subcontratista::select('id', 'razon_social_nombre as razon_social', 'numero_documento')->orderBy('razon_social_nombre')->get();
        // Assuming OrdenServicio relates to subcontratista
        $ordenes = OrdenServicio::select('id', 'numero_orden', 'tipo_contrato', 'total', 'subcontratista_id')->latest()->get();

        return Inertia::render('Finanzas/FacturaSubcontratistas/Create', [
            'subcontratistas' => $subcontratistas,
            'ordenes' => $ordenes->map(function ($orden) {
                return [
                    'id' => $orden->id,
                    'numero_orden' => $orden->numero_orden,
                    'descripcion' => $orden->tipo_contrato, // Mapping tipo_contrato to description for frontend compatibility
                    'total' => $orden->total,
                    'subcontratista_id' => $orden->subcontratista_id,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_documento' => 'required|string',
            'serie' => 'required|string|max:20',
            'numero_documento' => 'required|string|max:50',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_emision',
            'subcontratista_id' => 'required|exists:subcontratistas,id',
            'orden_servicio_id' => 'nullable|exists:orden_servicios,id',
            'base_imponible' => 'required|numeric|min:0',
            'igv' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'porcentaje_detraccion' => 'nullable|numeric|min:0|max:100',
            'monto_detraccion' => 'nullable|numeric|min:0',
            'estado' => 'required|in:pendiente,pago_parcial,pagado_completo',
            'observaciones' => 'nullable|string',
        ]);

        $validated['monto_pagado'] = 0;
        $validated['monto_pendiente'] = $validated['total'];
        $validated['user_id'] = auth()->id();
        $validated['usuario_creacion_id'] = auth()->id();

        FacturaSubcontratista::create($validated);

        return redirect()->route('factura-subcontratistas.index')
            ->with('success', 'Factura de subcontratista registrada correctamente.');
    }

    public function edit(FacturaSubcontratista $facturaSubcontratista)
    {
        // Using route binding 'factura_subcontratista' might be tricky if params don't match, 
        // but let's assume standard resource binding works or we use ID.
        // Actually, let's verify if route param is 'factura_subcontratista' or something else.
        // Usually Laravel uses snake_case model name.

        $facturaSubcontratista->load(['subcontratista', 'ordenServicio']);

        $subcontratistas = Subcontratista::select('id', 'razon_social_nombre as razon_social', 'numero_documento')->orderBy('razon_social_nombre')->get();
        $ordenes = OrdenServicio::select('id', 'numero_orden', 'tipo_contrato', 'total', 'subcontratista_id')->latest()->get();

        return Inertia::render('Finanzas/FacturaSubcontratistas/Edit', [
            'factura' => $facturaSubcontratista,
            'subcontratistas' => $subcontratistas,
            'ordenes' => $ordenes->map(function ($orden) {
                return [
                    'id' => $orden->id,
                    'numero_orden' => $orden->numero_orden,
                    'descripcion' => $orden->tipo_contrato,
                    'total' => $orden->total,
                    'subcontratista_id' => $orden->subcontratista_id,
                ];
            }),
        ]);
    }

    public function update(Request $request, FacturaSubcontratista $facturaSubcontratista)
    {
        $validated = $request->validate([
            'tipo_documento' => 'required|string',
            'serie' => 'required|string|max:20',
            'numero_documento' => 'required|string|max:50',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_emision',
            'subcontratista_id' => 'required|exists:subcontratistas,id',
            'orden_servicio_id' => 'nullable|exists:orden_servicios,id',
            'base_imponible' => 'required|numeric|min:0',
            'igv' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'porcentaje_detraccion' => 'nullable|numeric|min:0|max:100',
            'monto_detraccion' => 'nullable|numeric|min:0',
            'estado' => 'required|in:pendiente,pago_parcial,pagado_completo',
            'observaciones' => 'nullable|string',
        ]);

        $validated['usuario_modificacion_id'] = auth()->id();

        if ($facturaSubcontratista->monto_pagado == 0) {
            $validated['monto_pendiente'] = $validated['total'];
        } else {
            $validated['monto_pendiente'] = $validated['total'] - $facturaSubcontratista->monto_pagado;
        }

        $facturaSubcontratista->update($validated);

        return redirect()->route('factura-subcontratistas.index')
            ->with('success', 'Factura actualizada correctamente.');
    }

    public function destroy(FacturaSubcontratista $facturaSubcontratista)
    {
        $facturaSubcontratista->delete();

        return redirect()->route('factura-subcontratistas.index')
            ->with('success', 'Factura eliminada correctamente.');
    }
}
