<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoPublicoController extends Controller
{
    public function buscar()
    {
        return view('pages.public.pedido-publico.buscar');
    }

    public function show(Request $request)
    {
        $request->validate([
            'numero' => 'required|string',
            'email'  => 'required|email',
        ]);

        $pedido = Pedido::where('numero', $request->numero)
            ->where('email', $request->email)
            ->with(['items', 'zona', 'pago'])
            ->first();

        if (!$pedido) {
            return back()->withErrors([
                'numero' => 'No encontramos un pedido con ese número y correo.'
            ])->withInput();
        }

        return view('pages.public.pedido-publico.show', compact('pedido'));
    }
}