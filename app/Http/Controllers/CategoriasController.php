<?php

namespace App\Http\Controllers;

use App\Models\Categoria;

class CategoriasController extends Controller
{
    public function index()
    {
        $categorias = Categoria::raiz()
            ->where('estado', 'publicado')
            ->with('hijos')
            ->orderBy('titulo')
            ->get();

        return view('pages.public.categorias.index', compact('categorias'));
    }

    public function show(string $slug)
    {
        $categoria = Categoria::where('slug', $slug)
            ->where('estado', 'publicado')
            ->with(['padre', 'hijos' => fn($q) => $q->where('estado', 'publicado')])
            ->firstOrFail();

        return view('pages.public.categorias.show', compact('categoria'));
    }
}