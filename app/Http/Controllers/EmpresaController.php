<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmpresaController extends Controller
{
    /**
     * Display the company settings form.
     */
    public function index(Request $request): Response
    {
        // Obtener la primera empresa o crear una nueva si no existe
        $empresa = Empresa::first();

        if (!$empresa) {
            $empresa = Empresa::create([
                'ruc' => '',
                'razon_social' => '',
                'nombre_comercial' => '',
                'direccion' => '',
                'telefono' => '',
                'email' => '',
            ]);
        }

        return Inertia::render('Configuracion/Empresa/Index', [
            'empresa' => $empresa,
        ]);
    }

    /**
     * Update the company information.
     */
    public function update(Request $request, Empresa $empresa): RedirectResponse
    {
        $validated = $request->validate([
            'ruc' => 'required|string|max:20',
            'razon_social' => 'required|string|max:255',
            'nombre_comercial' => 'nullable|string|max:255',
            'direccion' => 'required|string|max:500',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255',
        ]);

        $empresa->update($validated);

        return redirect()
            ->route('empresas.index')
            ->with('success', 'Información de la empresa actualizada exitosamente.');
    }
}
