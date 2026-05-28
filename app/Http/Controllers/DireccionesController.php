<?php

namespace App\Http\Controllers;

use App\Models\DireccionUsuario;
use App\Models\Estado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DireccionesController extends Controller
{
    private function estados()
    {
        return Estado::activos()->with('municipios')->get();
    }

    public function index()
    {
        $direcciones = DireccionUsuario::where('id_usuario', Auth::id())
            ->with(['estado', 'municipio'])
            ->orderByDesc('predeterminada')
            ->orderByDesc('created_at')
            ->get();

        $estados = $this->estados();

        return view('pages.public.cuenta.direcciones.index', compact('direcciones', 'estados'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'alias'          => 'nullable|string|max:50',
            'nombre_contacto'=> 'required|string|max:255',
            'telefono'       => 'required|string|max:20',
            'calle'          => 'required|string|max:255',
            'numero_ext'     => 'required|string|max:20',
            'numero_int'     => 'nullable|string|max:20',
            'colonia'        => 'required|string|max:255',
            'cp'             => 'required|string|max:10',
            'id_estado'      => 'required|exists:estados,id',
            'id_municipio'   => 'required|exists:municipios,id',
            'referencias'    => 'nullable|string|max:500',
            'predeterminada' => 'boolean',
        ]);

        $data['id_usuario'] = Auth::id();
        $data['alias']      = $data['alias'] ?? 'Casa';

        // Si se marca como predeterminada, quitar la anterior
        if (!empty($data['predeterminada'])) {
            DireccionUsuario::where('id_usuario', Auth::id())
                ->update(['predeterminada' => false]);
        }

        // Si es la primera dirección, marcarla predeterminada automáticamente
        $esPrimera = DireccionUsuario::where('id_usuario', Auth::id())->count() === 0;
        if ($esPrimera) {
            $data['predeterminada'] = true;
        }

        DireccionUsuario::create($data);

        return response()->json(['ok' => true]);
    }

    public function update(Request $request, DireccionUsuario $direccion)
    {
        abort_if($direccion->id_usuario !== Auth::id(), 403);

        $data = $request->validate([
            'alias'          => 'nullable|string|max:50',
            'nombre_contacto'=> 'required|string|max:255',
            'telefono'       => 'required|string|max:20',
            'calle'          => 'required|string|max:255',
            'numero_ext'     => 'required|string|max:20',
            'numero_int'     => 'nullable|string|max:20',
            'colonia'        => 'required|string|max:255',
            'cp'             => 'required|string|max:10',
            'id_estado'      => 'required|exists:estados,id',
            'id_municipio'   => 'required|exists:municipios,id',
            'referencias'    => 'nullable|string|max:500',
            'predeterminada' => 'boolean',
        ]);

        if (!empty($data['predeterminada'])) {
            DireccionUsuario::where('id_usuario', Auth::id())
                ->update(['predeterminada' => false]);
        }

        $direccion->update($data);

        return response()->json(['ok' => true]);
    }

    public function destroy(DireccionUsuario $direccion)
    {
        abort_if($direccion->id_usuario !== Auth::id(), 403);
        $direccion->delete();

        // Si era la predeterminada, asignar la más reciente
        if ($direccion->predeterminada) {
            DireccionUsuario::where('id_usuario', Auth::id())
                ->latest()
                ->first()
                ?->update(['predeterminada' => true]);
        }

        return response()->json(['ok' => true]);
    }

    public function predeterminada(DireccionUsuario $direccion)
    {
        abort_if($direccion->id_usuario !== Auth::id(), 403);

        DireccionUsuario::where('id_usuario', Auth::id())
            ->update(['predeterminada' => false]);

        $direccion->update(['predeterminada' => true]);

        return response()->json(['ok' => true]);
    }

    // Endpoint para el checkout — devuelve municipios de un estado
    public function municipiosPorEstado(Estado $estado)
    {
        return response()->json(
            $estado->municipios()->get(['id', 'nombre', 'tipo'])
        );
    }
}