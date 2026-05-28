<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUsuariosController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('pedidos')->latest();

        if ($request->filled('busqueda')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->busqueda . '%')
                  ->orWhere('lastname', 'like', '%' . $request->busqueda . '%')
                  ->orWhere('email', 'like', '%' . $request->busqueda . '%');
            });
        }

        if ($request->filled('rol')) {
            $query->where('role', $request->rol);
        }

        $usuarios = $query->paginate(20)->withQueryString();

        return view('pages.admin.usuarios.index', compact('usuarios'));
    }

    public function show(User $usuario)
    {
        $pedidos = Pedido::where('id_usuario', $usuario->id)
            ->with(['items', 'pago'])
            ->latest()
            ->get();

        return view('pages.admin.usuarios.show', compact('usuario', 'pedidos'));
    }

    public function edit(User $usuario)
    {
        return view('pages.admin.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($usuario->id)],
        ]);

        $usuario->update($validated);

        return redirect()->route('admin.usuarios.show', $usuario)
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function toggleRol(User $usuario)
    {
        // Proteger: no permitir quitarle el rol admin al propio usuario autenticado
        if ($usuario->id === auth()->id()) {
            return response()->json(['ok' => false, 'message' => 'No puedes modificar tu propio rol.'], 403);
        }

        $usuario->role = $usuario->role === 'admin' ? 'user' : 'admin';
        $usuario->save();

        return response()->json([
            'ok'   => true,
            'role' => $usuario->role,
        ]);
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado.');
    }
}