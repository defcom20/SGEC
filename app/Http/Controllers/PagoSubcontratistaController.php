<?php

namespace App\Http\Controllers;

use App\Http\Requests\PagoSubcontratistumStoreRequest;
use App\Http\Requests\PagoSubcontratistumUpdateRequest;
use App\Models\PagoSubcontratista;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PagoSubcontratistaController extends Controller
{
    public function index(Request $request): Response
    {
        $pagoSubcontratista = PagoSubcontratistum::all();

        return view('pagoSubcontratistum.index', [
            'pagoSubcontratista' => $pagoSubcontratista,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('pagoSubcontratistum.create');
    }

    public function store(PagoSubcontratistumStoreRequest $request): Response
    {
        $pagoSubcontratistum = PagoSubcontratistum::create($request->validated());

        $request->session()->flash('pagoSubcontratistum.id', $pagoSubcontratistum->id);

        return redirect()->route('pagoSubcontratista.index');
    }

    public function show(Request $request, PagoSubcontratistum $pagoSubcontratistum): Response
    {
        return view('pagoSubcontratistum.show', [
            'pagoSubcontratistum' => $pagoSubcontratistum,
        ]);
    }

    public function edit(Request $request, PagoSubcontratistum $pagoSubcontratistum): Response
    {
        return view('pagoSubcontratistum.edit', [
            'pagoSubcontratistum' => $pagoSubcontratistum,
        ]);
    }

    public function update(PagoSubcontratistumUpdateRequest $request, PagoSubcontratistum $pagoSubcontratistum): Response
    {
        $pagoSubcontratistum->update($request->validated());

        $request->session()->flash('pagoSubcontratistum.id', $pagoSubcontratistum->id);

        return redirect()->route('pagoSubcontratista.index');
    }

    public function destroy(Request $request, PagoSubcontratistum $pagoSubcontratistum): Response
    {
        $pagoSubcontratistum->delete();

        return redirect()->route('pagoSubcontratista.index');
    }
}
