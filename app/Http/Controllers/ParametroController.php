<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParametroStoreRequest;
use App\Http\Requests\ParametroUpdateRequest;
use App\Models\Parametro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParametroController extends Controller
{
    public function index(Request $request): Response
    {
        $parametros = Parametro::all();

        return view('parametro.index', [
            'parametros' => $parametros,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('parametro.create');
    }

    public function store(ParametroStoreRequest $request): Response
    {
        $parametro = Parametro::create($request->validated());

        $request->session()->flash('parametro.id', $parametro->id);

        return redirect()->route('parametros.index');
    }

    public function show(Request $request, Parametro $parametro): Response
    {
        return view('parametro.show', [
            'parametro' => $parametro,
        ]);
    }

    public function edit(Request $request, Parametro $parametro): Response
    {
        return view('parametro.edit', [
            'parametro' => $parametro,
        ]);
    }

    public function update(ParametroUpdateRequest $request, Parametro $parametro): Response
    {
        $parametro->update($request->validated());

        $request->session()->flash('parametro.id', $parametro->id);

        return redirect()->route('parametros.index');
    }

    public function destroy(Request $request, Parametro $parametro): Response
    {
        $parametro->delete();

        return redirect()->route('parametros.index');
    }
}
