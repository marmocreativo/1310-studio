<?php

namespace App\Http\Controllers;

use App\Models\DirectorioFloral;
use Illuminate\Http\Request;

class DirectorioFloralController extends Controller
{
    public function index(Request $request)
    {
        $query = DirectorioFloral::activos()->ordenados()->with('galeria');

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        $flores = $query->paginate(24)->withQueryString();

        $categorias = DirectorioFloral::activos()
                                    ->whereNotNull('categoria')
                                    ->distinct()
                                    ->pluck('categoria');

        return view('pages.public.directorio-floral.index', compact('flores', 'categorias'));
    }

    public function show(DirectorioFloral $flor)
    {
        if (! $flor->estado) {
            abort(404);
        }

        $flor->load(['galeria', 'productos.galeria']);

        $relacionadas = DirectorioFloral::activos()
            ->where('categoria', $flor->categoria)
            ->where('id', '!=', $flor->id)
            ->with('galeria')
            ->limit(6)
            ->get();

        return view('pages.public.directorio-floral.show', compact('flor', 'relacionadas'));
    }
}