<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $destacados = \App\Models\Producto::destacados()
            ->activos()
            ->with('galeria')
            ->get();

        $flores = \App\Models\DirectorioFloral::activos()
            ->with('galeria')
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $categorias = \App\Models\Categoria::raiz()
            ->where('estado', 'publicado')
            ->get();

        return view('pages.public.home', compact('destacados', 'flores', 'categorias'));
    }

    public function empresas()
    {
        return view('pages.public.empresas');
    }

    public function visitanos()
    {
        return view('pages.public.visitanos');
    }
}