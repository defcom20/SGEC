<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermisoStoreRequest;
use App\Http\Requests\PermisoUpdateRequest;
use App\Models\Permiso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermisoController extends Controller
{
    public function index(Request $request): Response
    {
        $permisos = Permiso::all();

        return view('permiso.index', [
            'permisos' => $permisos,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('permiso.create');
    }

    public function store(PermisoStoreRequest $request): Response
    {
        $permiso = Permiso::create($request->validated());

        $request->session()->flash('permiso.id', $permiso->id);

        return redirect()->route('permisos.index');
    }

    public function show(Request $request, Permiso $permiso): Response
    {
        return view('permiso.show', [
            'permiso' => $permiso,
        ]);
    }

    public function edit(Request $request, Permiso $permiso): Response
    {
        return view('permiso.edit', [
            'permiso' => $permiso,
        ]);
    }

    public function update(PermisoUpdateRequest $request, Permiso $permiso): Response
    {
        $permiso->update($request->validated());

        $request->session()->flash('permiso.id', $permiso->id);

        return redirect()->route('permisos.index');
    }

    public function destroy(Request $request, Permiso $permiso): Response
    {
        $permiso->delete();

        return redirect()->route('permisos.index');
    }
}
