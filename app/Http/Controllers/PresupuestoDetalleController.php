<?php

namespace App\Http\Controllers;

use App\Http\Requests\PresupuestoDetalleStoreRequest;
use App\Http\Requests\PresupuestoDetalleUpdateRequest;
use App\Models\PresupuestoDetalle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PresupuestoDetalleController extends Controller
{
    public function index(Request $request): Response
    {
        $presupuestoDetalles = PresupuestoDetalle::all();

        return view('presupuestoDetalle.index', [
            'presupuestoDetalles' => $presupuestoDetalles,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('presupuestoDetalle.create');
    }

    public function store(PresupuestoDetalleStoreRequest $request): Response
    {
        $presupuestoDetalle = PresupuestoDetalle::create($request->validated());

        $request->session()->flash('presupuestoDetalle.id', $presupuestoDetalle->id);

        return redirect()->route('presupuestoDetalles.index');
    }

    public function show(Request $request, PresupuestoDetalle $presupuestoDetalle): Response
    {
        return view('presupuestoDetalle.show', [
            'presupuestoDetalle' => $presupuestoDetalle,
        ]);
    }

    public function edit(Request $request, PresupuestoDetalle $presupuestoDetalle): Response
    {
        return view('presupuestoDetalle.edit', [
            'presupuestoDetalle' => $presupuestoDetalle,
        ]);
    }

    public function update(PresupuestoDetalleUpdateRequest $request, PresupuestoDetalle $presupuestoDetalle): Response
    {
        $presupuestoDetalle->update($request->validated());

        $request->session()->flash('presupuestoDetalle.id', $presupuestoDetalle->id);

        return redirect()->route('presupuestoDetalles.index');
    }

    public function destroy(Request $request, PresupuestoDetalle $presupuestoDetalle): Response
    {
        $presupuestoDetalle->delete();

        return redirect()->route('presupuestoDetalles.index');
    }
}
