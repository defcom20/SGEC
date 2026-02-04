<?php

namespace App\Http\Controllers;

use App\Http\Requests\PagoClienteStoreRequest;
use App\Models\PagoCliente;
use App\Models\FacturaCliente;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PagoClienteController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $pagos = PagoCliente::query()
            ->with(['facturaCliente.cliente'])
            ->when($search, function ($query, $search) {
                $query->where('numero_pago', 'like', "%{$search}%")
                    ->orWhere('numero_operacion', 'like', "%{$search}%")
                    ->orWhereHas('facturaCliente', function ($q) use ($search) {
                        $q->where('numero_factura', 'like', "%{$search}%")
                            ->orWhereHas('cliente', function ($q2) use ($search) {
                                $q2->where('razon_social', 'like', "%{$search}%");
                            });
                    });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Finanzas/PagoClientes/Index', [
            'pagos' => $pagos,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        // Only invoices with pending amount
        $facturas = FacturaCliente::with('cliente')
            ->where('estado', '!=', 'pagado_completo')
            ->where('monto_pendiente', '>', 0)
            ->get()
            ->map(function ($factura) {
                return [
                    'id' => $factura->id,
                    'numero_factura' => $factura->numero_factura,
                    'serie' => $factura->serie,
                    'cliente_nombre' => $factura->cliente->razon_social,
                    'total' => $factura->total,
                    'monto_pendiente' => $factura->monto_pendiente,
                ];
            });

        return Inertia::render('Finanzas/PagoClientes/Create', [
            'facturas' => $facturas,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'factura_cliente_id' => 'required|exists:factura_clientes,id',
            'fecha_pago' => 'required|date',
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|string',
            'banco' => 'nullable|string',
            'numero_operacion' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $validated['user_id'] = auth()->id();
            // Generate simple payment number logic or leave as null if DB handles it (assuming nullable or auto-generated logic)
            // For now, let's create a simple timestamp-based number if not provided or just use a placeholder
            $validated['numero_pago'] = 'P-' . time();
            $validated['usuario_registro_id'] = auth()->id();

            // Create Payment
            $pago = PagoCliente::create($validated);

            // Update Invoice Status
            $factura = FacturaCliente::findOrFail($validated['factura_cliente_id']);

            // Validate amount doesn't exceed pending (optional but good practice)
            if ($validated['monto'] > $factura->monto_pendiente) {
                // We permit it for now but in strict systems might block. 
                // Let's just allow it for flexibility but warn or cap logic could be added.
            }

            $factura->monto_pagado += $validated['monto'];
            $factura->monto_pendiente = $factura->total - $factura->monto_pagado;

            if ($factura->monto_pendiente <= 0) {
                $factura->monto_pendiente = 0;
                $factura->estado = 'pagado_completo';
            } else {
                $factura->estado = 'pago_parcial';
            }

            $factura->save();
        });

        return redirect()->route('pago-clientes.index')
            ->with('success', 'Pago registrado correctamente.');
    }

    public function edit(PagoCliente $pagoCliente)
    {
        // In this simplified version, changing the invoice of a payment is complex due to recalculations.
        // We will restrict editing to non-financial fields or allow editing amount with rollback logic.
        // For MVP, let's allow basic editing and re-adjustment of the SAME invoice.

        $factura = $pagoCliente->facturaCliente;

        return Inertia::render('Finanzas/PagoClientes/Edit', [
            'pago' => $pagoCliente,
            'factura' => [
                'id' => $factura->id,
                'serie' => $factura->serie,
                'numero_factura' => $factura->numero_factura,
                'max_amount' => $factura->monto_pendiente + $pagoCliente->monto // Allow paying up to current pending + what was already paid
            ]
        ]);
    }

    public function update(Request $request, PagoCliente $pagoCliente)
    {
        $validated = $request->validate([
            'fecha_pago' => 'required|date',
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|string',
            'banco' => 'nullable|string',
            'numero_operacion' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $pagoCliente) {
            $factura = $pagoCliente->facturaCliente;

            // Revert old amount
            $factura->monto_pagado -= $pagoCliente->monto;

            // Apply new amount
            $factura->monto_pagado += $validated['monto'];
            $factura->monto_pendiente = $factura->total - $factura->monto_pagado;

            if ($factura->monto_pendiente <= 0) {
                $factura->monto_pendiente = 0;
                $factura->estado = 'pagado_completo';
            } else {
                $factura->estado = ($factura->monto_pagado > 0) ? 'pago_parcial' : 'pendiente';
            }
            $factura->save();

            $pagoCliente->update($validated);
        });

        return redirect()->route('pago-clientes.index')
            ->with('success', 'Pago actualizado correctamente.');
    }

    public function destroy(PagoCliente $pagoCliente)
    {
        DB::transaction(function () use ($pagoCliente) {
            $factura = $pagoCliente->facturaCliente;

            // Revert amount
            $factura->monto_pagado -= $pagoCliente->monto;
            $factura->monto_pendiente = $factura->total - $factura->monto_pagado;

            if ($factura->monto_pendiente == $factura->total) {
                $factura->estado = 'pendiente';
            } else {
                $factura->estado = 'pago_parcial'; // If > 0 paid remains
            }
            if ($factura->monto_pendiente <= 0) {
                $factura->estado = 'pagado_completo';
            }

            $factura->save();
            $pagoCliente->delete();
        });

        return redirect()->route('pago-clientes.index')
            ->with('success', 'Pago eliminado correctamente.');
    }
}
