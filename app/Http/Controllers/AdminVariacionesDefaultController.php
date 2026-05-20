<?php

namespace App\Http\Controllers;

use App\Models\VariacionTipoDefault;
use App\Models\VariacionOpcionDefault;
use Illuminate\Http\Request;

class AdminVariacionesDefaultController extends Controller
{
    public function index()
    {
        $tipos = VariacionTipoDefault::ordenados()->with('opciones')->get();

        return response()->json([
            'tipos' => $tipos->map(fn($t) => [
                'id'      => $t->id,
                'nombre'  => $t->nombre,
                'orden'   => $t->orden,
                'opciones'=> $t->opciones->map(fn($o) => [
                    'id'     => $o->id,
                    'nombre' => $o->nombre,
                ]),
            ]),
        ]);
    }

    public function tipoStore(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'orden'  => 'integer|min:0',
        ]);

        $tipo = VariacionTipoDefault::create($data);

        return response()->json(['ok' => true, 'tipo' => ['id' => $tipo->id, 'nombre' => $tipo->nombre, 'orden' => $tipo->orden, 'opciones' => []]]);
    }

    public function tipoUpdate(Request $request, VariacionTipoDefault $tipo)
    {
        $data = $request->validate(['nombre' => 'required|string|max:255']);
        $tipo->update($data);

        return response()->json(['ok' => true]);
    }

    public function tipoDestroy(VariacionTipoDefault $tipo)
    {
        $tipo->delete();
        return response()->json(['ok' => true]);
    }

    public function opcionStore(Request $request, VariacionTipoDefault $tipo)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'orden'  => 'integer|min:0',
        ]);

        $data['id_tipo_default'] = $tipo->id;
        $data['orden'] = $data['orden'] ?? ($tipo->opciones()->max('orden') + 1);

        $opcion = VariacionOpcionDefault::create($data);

        return response()->json(['ok' => true, 'opcion' => ['id' => $opcion->id, 'nombre' => $opcion->nombre]]);
    }

    public function opcionUpdate(Request $request, VariacionOpcionDefault $opcion)
    {
        $data = $request->validate(['nombre' => 'required|string|max:255']);
        $opcion->update($data);

        return response()->json(['ok' => true]);
    }

    public function opcionDestroy(VariacionOpcionDefault $opcion)
    {
        $opcion->delete();
        return response()->json(['ok' => true]);
    }
}