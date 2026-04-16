<?php

namespace App\Http\Controllers;

use App\Models\Pagina;

class PaginasController extends Controller
{
    public function index()
    {
        $paginas = Pagina::where('estado', 'publicado')
            ->latest()
            ->paginate(12);

        return view('pages.public.paginas.index', compact('paginas'));
    }

    public function show(string $slug)
    {
        $pagina = Pagina::where('slug', $slug)
            ->where('estado', 'publicado')
            ->firstOrFail();

        return view('pages.public.paginas.show', compact('pagina'));
    }
}