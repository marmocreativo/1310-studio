<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminConfiguracionesController extends Controller
{
    private array $grupos = ['sistema', 'contacto', 'apariencia'];

    public function index(Request $request)
    {
        $grupoActivo = $request->get('grupo', 'sistema');

        $configuraciones = Configuracion::where('grupo', $grupoActivo)
            ->orderBy('nombre_conf')
            ->get();

        return view('pages.admin.configuraciones.index', [
            'configuraciones' => $configuraciones,
            'grupos'          => $this->grupos,
            'grupoActivo'     => $grupoActivo,
        ]);
    }

    public function update(Request $request, Configuracion $configuracion)
    {
        $request->validate([
            'contenido_conf' => 'nullable|string',
        ]);

        $configuracion->update([
            'contenido_conf' => $request->contenido_conf,
        ]);

        Cache::forget('configuraciones_globales');

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Configuración actualizada.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_conf'    => 'required|string|alpha_dash|unique:configuraciones,nombre_conf',
            'contenido_conf' => 'nullable|string',
            'grupo'          => 'required|string',
        ]);

        Configuracion::create($request->only('nombre_conf', 'contenido_conf', 'grupo'));

        Cache::forget('configuraciones_globales');

        return back()->with('success', 'Configuración creada.');
    }

    public function destroy(Configuracion $configuracion)
    {
        $configuracion->delete();
        Cache::forget('configuraciones_globales');

        return back()->with('success', 'Configuración eliminada.');
    }
}