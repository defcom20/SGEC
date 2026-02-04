<?php

namespace App\Http\Controllers;

use App\Models\Modulo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ModuloController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener módulos padre con sus hijos
        $modulosPadre = Modulo::padres()
            ->with([
                'children' => function ($query) {
                    $query->ordenados();
                }
            ])
            ->ordenados()
            ->get();

        return Inertia::render('Modulos/Index', [
            'modulosPadre' => $modulosPadre,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Modulo $modulo)
    {
        $validated = $request->validate([
            'activo' => 'required|boolean',
            'visible_menu' => 'boolean',
            'orden' => 'integer|min:0',
        ]);

        $modulo->update([
            ...$validated,
            'usuario_modificacion_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Módulo actualizado correctamente');
    }

    /**
     * Toggle module status (activate/deactivate)
     */
    public function toggle(Modulo $modulo)
    {
        $modulo->update([
            'activo' => !$modulo->activo,
            'usuario_modificacion_id' => auth()->id(),
        ]);

        $estado = $modulo->activo ? 'activado' : 'desactivado';

        return redirect()->back()->with('success', "Módulo {$estado} correctamente");
    }

    /**
     * Bulk update module order
     */
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'modulos' => 'required|array',
            'modulos.*.id' => 'required|exists:modulos,id',
            'modulos.*.orden' => 'required|integer|min:0',
        ]);

        foreach ($validated['modulos'] as $moduloData) {
            Modulo::where('id', $moduloData['id'])->update([
                'orden' => $moduloData['orden'],
                'usuario_modificacion_id' => auth()->id(),
            ]);
        }

        return redirect()->back()->with('success', 'Orden de módulos actualizado');
    }
}
