<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrdenServicioDetalleStoreRequest;
use App\Http\Requests\OrdenServicioDetalleUpdateRequest;
use App\Models\OrdenServicioDetalle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrdenServicioDetalleController extends Controller
{
    public function index(Request $request): Response
    {
        $ordenServicioDetalles = OrdenServicioDetalle::all();

        return view('ordenServicioDetalle.index', [
            'ordenServicioDetalles' => $ordenServicioDetalles,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('ordenServicioDetalle.create');
    }

    public function store(OrdenServicioDetalleStoreRequest $request): Response
    {
        $ordenServicioDetalle = OrdenServicioDetalle::create($request->validated());

        $request->session()->flash('ordenServicioDetalle.id', $ordenServicioDetalle->id);

        return redirect()->route('ordenServicioDetalles.index');
    }

    public function show(Request $request, OrdenServicioDetalle $ordenServicioDetalle): Response
    {
        return view('ordenServicioDetalle.show', [
            'ordenServicioDetalle' => $ordenServicioDetalle,
        ]);
    }

    public function edit(Request $request, OrdenServicioDetalle $ordenServicioDetalle): Response
    {
        return view('ordenServicioDetalle.edit', [
            'ordenServicioDetalle' => $ordenServicioDetalle,
        ]);
    }

    public function update(OrdenServicioDetalleUpdateRequest $request, OrdenServicioDetalle $ordenServicioDetalle): Response
    {
        $ordenServicioDetalle->update($request->validated());

        $request->session()->flash('ordenServicioDetalle.id', $ordenServicioDetalle->id);

        return redirect()->route('ordenServicioDetalles.index');
    }

    public function destroy(Request $request, OrdenServicioDetalle $ordenServicioDetalle): Response
    {
        $ordenServicioDetalle->delete();

        return redirect()->route('ordenServicioDetalles.index');
    }
}
