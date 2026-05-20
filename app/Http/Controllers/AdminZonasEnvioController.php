<?php

namespace App\Http\Controllers;

use App\Models\ZonaEnvio;
use App\Models\ZonaEnvioAlcaldia;
use Illuminate\Http\Request;

class AdminZonasEnvioController extends Controller
{
    public function index()
    {
        $zonas = ZonaEnvio::with('alcaldias')->orderBy('orden')->get();
        return view('pages.admin.zonas-envio.index', compact('zonas'));
    }

    public function create()
    {
        return view('pages.admin.zonas-envio.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'precio'      => 'required|numeric|min:0',
            'activa'      => 'boolean',
            'orden'       => 'integer|min:0',
            'alcaldias'   => 'nullable|array',
            'alcaldias.*' => 'string|max:255',
        ]);

        $zona = ZonaEnvio::create($data);

        foreach (array_filter($data['alcaldias'] ?? []) as $nombre) {
            ZonaEnvioAlcaldia::create([
                'id_zona' => $zona->id,
                'nombre'  => trim($nombre),
            ]);
        }

        return redirect()->route('admin.zonas-envio.index')
            ->with('success', 'Zona de envío creada correctamente.');
    }

    public function edit(ZonaEnvio $zona)
    {
        $zona->load('alcaldias');
        return view('pages.admin.zonas-envio.edit', compact('zona'));
    }

    public function update(Request $request, ZonaEnvio $zona)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'precio'      => 'required|numeric|min:0',
            'activa'      => 'boolean',
            'orden'       => 'integer|min:0',
            'alcaldias'   => 'nullable|array',
            'alcaldias.*' => 'string|max:255',
        ]);

        $zona->update($data);

        // Reemplazar alcaldías
        $zona->alcaldias()->delete();
        foreach (array_filter($data['alcaldias'] ?? []) as $nombre) {
            ZonaEnvioAlcaldia::create([
                'id_zona' => $zona->id,
                'nombre'  => trim($nombre),
            ]);
        }

        return redirect()->route('admin.zonas-envio.index')
            ->with('success', 'Zona de envío actualizada correctamente.');
    }

    public function destroy(ZonaEnvio $zona)
    {
        $zona->delete();

        return redirect()->route('admin.zonas-envio.index')
            ->with('success', 'Zona eliminada correctamente.');
    }

    public function show(ZonaEnvio $zona)
    {
        return redirect()->route('admin.zonas-envio.edit', $zona);
    }
}