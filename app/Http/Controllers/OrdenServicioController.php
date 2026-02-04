<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrdenServicioStoreRequest;
use App\Http\Requests\OrdenServicioUpdateRequest;
use App\Models\OrdenServicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrdenServicioController extends Controller
{
    public function index(Request $request): Response
    {
        $ordenServicios = OrdenServicio::all();

        return view('ordenServicio.index', [
            'ordenServicios' => $ordenServicios,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('ordenServicio.create');
    }

    public function store(OrdenServicioStoreRequest $request): Response
    {
        $ordenServicio = OrdenServicio::create($request->validated());

        $request->session()->flash('ordenServicio.id', $ordenServicio->id);

        return redirect()->route('ordenServicios.index');
    }

    public function show(Request $request, OrdenServicio $ordenServicio): Response
    {
        return view('ordenServicio.show', [
            'ordenServicio' => $ordenServicio,
        ]);
    }

    public function edit(Request $request, OrdenServicio $ordenServicio): Response
    {
        return view('ordenServicio.edit', [
            'ordenServicio' => $ordenServicio,
        ]);
    }

    public function update(OrdenServicioUpdateRequest $request, OrdenServicio $ordenServicio): Response
    {
        $ordenServicio->update($request->validated());

        $request->session()->flash('ordenServicio.id', $ordenServicio->id);

        return redirect()->route('ordenServicios.index');
    }

    public function destroy(Request $request, OrdenServicio $ordenServicio): Response
    {
        $ordenServicio->delete();

        return redirect()->route('ordenServicios.index');
    }
}
