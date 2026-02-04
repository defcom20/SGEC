<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class UsuarioController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): Response
    {
        $usuarios = User::with('rol')
            ->orderBy('name')
            ->get();

        return Inertia::render('Configuracion/Usuarios/Index', [
            'usuarios' => $usuarios,
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(Request $request): Response
    {
        $roles = Rol::orderBy('nombre')->get();

        return Inertia::render('Configuracion/Usuarios/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'rol_id' => 'required|exists:rols,id',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'rol_id' => $validated['rol_id'],
        ]);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Display the specified user.
     */
    public function show(Request $request, User $usuario): Response
    {
        $usuario->load('rol');

        return Inertia::render('Configuracion/Usuarios/Show', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(Request $request, User $usuario): Response
    {
        $usuario->load('rol');
        $roles = Rol::orderBy('nombre')->get();

        return Inertia::render('Configuracion/Usuarios/Edit', [
            'usuario' => $usuario,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $usuario): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $usuario->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'rol_id' => 'required|exists:rols,id',
        ]);

        $usuario->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'rol_id' => $validated['rol_id'],
        ]);

        // Solo actualizar la contraseña si se proporcionó una nueva
        if (!empty($validated['password'])) {
            $usuario->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $usuario): RedirectResponse
    {
        // No permitir eliminar al usuario autenticado
        if ($usuario->id === $request->user()->id) {
            return redirect()
                ->route('usuarios.index')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $usuario->delete();

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }
}
