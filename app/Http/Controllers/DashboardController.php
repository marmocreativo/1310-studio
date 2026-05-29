<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\User;
use App\Models\Producto;
use App\Models\Taller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $totalPedidos    = Pedido::count();
        $pedidosPendientes = Pedido::where('estado', 'pendiente')->count();
        $pedidosPagados  = Pedido::where('estado', 'pagado')->count();
        $totalUsuarios   = User::count();
        $totalProductos  = Producto::where('estado', 1)->count();
        $totalTalleres   = Taller::where('estado', 1)->count();

        // Ventas del mes actual
        $ventasMes = Pedido::whereIn('estado', ['pagado', 'preparando', 'enviado', 'entregado'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        // Últimos 8 pedidos
        $ultimosPedidos = Pedido::with(['items'])
            ->latest()
            ->take(8)
            ->get();

        return view('pages.admin.dashboard', compact(
            'totalPedidos',
            'pedidosPendientes',
            'pedidosPagados',
            'totalUsuarios',
            'totalProductos',
            'totalTalleres',
            'ventasMes',
            'ultimosPedidos',
        ));
    }
}