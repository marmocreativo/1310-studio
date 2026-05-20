<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class AdminPedidosController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::with(['items', 'zona', 'pago'])->latest();

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('busqueda')) {
            $query->where(function ($q) use ($request) {
                $q->where('numero', 'like', '%' . $request->busqueda . '%')
                  ->orWhere('nombre', 'like', '%' . $request->busqueda . '%')
                  ->orWhere('email', 'like', '%' . $request->busqueda . '%');
            });
        }

        if ($request->filled('tipo_entrega')) {
            $query->where('tipo_entrega', $request->tipo_entrega);
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_entrega', $request->fecha);
        }

        $pedidos = $query->paginate(20)->withQueryString();

        $estados = [
            'pendiente', 'pagado', 'preparando', 'enviado', 'entregado', 'cancelado'
        ];

        return view('pages.admin.pedidos.index', compact('pedidos', 'estados'));
    }

    public function show(Pedido $pedido)
    {
        $pedido->load(['items.producto.galeria', 'items.sku', 'zona', 'pagos', 'usuario']);

        return view('pages.admin.pedidos.show', compact('pedido'));
    }

    public function cambiarEstado(Request $request, Pedido $pedido)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,pagado,preparando,enviado,entregado,cancelado',
        ]);

        $pedido->update(['estado' => $request->estado]);

        // Si se marca como pagado manualmente (contra entrega)
        if ($request->estado === 'pagado') {
            $pago = $pedido->pagos()->latest()->first();
            if ($pago && $pago->estado === 'pendiente') {
                $pago->update(['estado' => 'aprobado']);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok'     => true,
                'estado' => $pedido->estado_label,
                'color'  => $pedido->estado_color,
            ]);
        }

        return back()->with('success', 'Estado actualizado correctamente.');
    }

    public function destroy(Pedido $pedido)
    {
        $pedido->delete();

        return redirect()->route('admin.pedidos.index')
            ->with('success', 'Pedido eliminado.');
    }
}