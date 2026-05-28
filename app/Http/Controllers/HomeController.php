<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slide;

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
        
        $slides = Slide::activos()->ordenados()->get();


        return view('pages.public.home', compact('destacados', 'flores', 'categorias', 'slides'));
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