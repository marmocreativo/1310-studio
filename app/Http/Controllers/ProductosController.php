<?php

namespace App\Http\Controllers;

use App\Models\Producto;

class ProductosController extends Controller
{
    public function show(Producto $producto)
    {
        if (! $producto->estado) {
            abort(404);
        }

        $producto->load(['galeria', 'categorias', 'flores.galeria']);

        $relacionados = Producto::activos()
            ->whereHas('categorias', function ($q) use ($producto) {
                $q->whereIn('categorias.id', $producto->categorias->pluck('id'));
            })
            ->where('id', '!=', $producto->id)
            ->with('galeria')
            ->limit(4)
            ->get();

        return view('pages.public.productos.show', compact('producto', 'relacionados'));
    }
}