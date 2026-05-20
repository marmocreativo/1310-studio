<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\CarritoItem;
use App\Models\Producto;
use App\Models\VariacionSku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarritoController extends Controller
{
    // ─── Obtener o crear carrito ──────────────────

    private function obtenerCarrito(): Carrito
    {
        $sessionId = session()->getId();

        if (Auth::check()) {
            // Usuario autenticado: buscar por usuario primero
            $carrito = Carrito::where('id_usuario', Auth::id())
                ->noExpirados()
                ->with('items')
                ->first();

            if (!$carrito) {
                // Migrar carrito de sesión si existe
                $carritoSesion = Carrito::where('sesion_id', $sessionId)
                    ->noExpirados()
                    ->first();

                if ($carritoSesion) {
                    $carritoSesion->update(['id_usuario' => Auth::id()]);
                    return $carritoSesion->load('items');
                }

                $carrito = Carrito::create([
                    'id_usuario' => Auth::id(),
                    'sesion_id'  => $sessionId,
                    'expires_at' => now()->addDays(30),
                ]);
            }

            return $carrito;
        }

        // Invitado: buscar por sesión
        return Carrito::firstOrCreate(
            ['sesion_id' => $sessionId, 'id_usuario' => null],
            ['expires_at' => now()->addDays(7)]
        )->load('items');
    }

    // ─── Index ────────────────────────────────────

    public function index()
    {
        $carrito = $this->obtenerCarrito();
        $carrito->load('items.producto.galeria', 'items.sku.opciones.tipo');

        if (request()->expectsJson()) {
            return response()->json([
                'items' => $carrito->items->map(fn($item) => [
                    'id'                => $item->id,
                    'nombre_snapshot'   => $item->nombre_snapshot,
                    'precio_snapshot'   => (float) $item->precio_snapshot,
                    'cantidad'          => $item->cantidad,
                    'opciones_snapshot' => $item->opciones_snapshot,
                    'imagen_url'        => $item->imagen_url,
                ]),
                'total_items' => $carrito->total_items,
                'subtotal'    => $carrito->subtotal,
            ]);
        }

        return view('pages.public.carrito.index', compact('carrito'));
    }

    // ─── Agregar ──────────────────────────────────

    public function agregar(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|exists:productos,id',
            'id_sku'      => 'nullable|exists:variacion_skus,id',
            'cantidad'    => 'integer|min:1|max:99',
        ]);

        $producto = Producto::findOrFail($request->id_producto);

        if (!$producto->estado) {
            return response()->json(['ok' => false, 'message' => 'Producto no disponible.'], 422);
        }

        // Determinar precio y nombre
        $sku            = null;
        $opcionesSnap   = null;
        $precio         = (float) $producto->precio_venta;

        if ($request->id_sku) {
            $sku = VariacionSku::with('opciones.tipo')->findOrFail($request->id_sku);

            if ($sku->id_producto !== $producto->id || !$sku->estado) {
                return response()->json(['ok' => false, 'message' => 'Combinación no disponible.'], 422);
            }

            $precio = (float) $sku->precio_venta;

            $opcionesSnap = $sku->opciones->map(fn($o) => [
                'tipo'   => $o->tipo->nombre,
                'opcion' => $o->nombre,
            ])->toArray();
        }

        $carrito  = $this->obtenerCarrito();
        $cantidad = $request->cantidad ?? 1;

        // Verificar si ya existe el mismo item
        $itemExistente = $carrito->items()
            ->where('id_producto', $producto->id)
            ->where('id_sku', $sku?->id)
            ->first();

        if ($itemExistente) {
            $itemExistente->increment('cantidad', $cantidad);
        } else {
            CarritoItem::create([
                'id_carrito'       => $carrito->id,
                'id_producto'      => $producto->id,
                'id_sku'           => $sku?->id,
                'nombre_snapshot'  => $producto->nombre,
                'precio_snapshot'  => $precio,
                'cantidad'         => $cantidad,
                'opciones_snapshot'=> $opcionesSnap,
            ]);
        }

        $carrito->load('items');

        return response()->json([
            'ok'          => true,
            'message'     => 'Producto agregado al carrito.',
            'total_items' => $carrito->total_items,
        ]);
    }

    // ─── Actualizar cantidad ──────────────────────

    public function actualizar(Request $request, CarritoItem $item)
    {
        $this->autorizarItem($item);

        $request->validate([
            'cantidad' => 'required|integer|min:1|max:99',
        ]);

        $item->update(['cantidad' => $request->cantidad]);

        $carrito = $this->obtenerCarrito();

        return response()->json([
            'ok'       => true,
            'subtotal' => number_format($item->subtotal, 2),
            'total'    => number_format($carrito->subtotal, 2),
        ]);
    }

    // ─── Eliminar item ────────────────────────────

    public function eliminar(CarritoItem $item)
    {
        $this->autorizarItem($item);
        $item->delete();

        $carrito = $this->obtenerCarrito();

        return response()->json([
            'ok'          => true,
            'total_items' => $carrito->total_items,
            'total'       => number_format($carrito->subtotal, 2),
            'vacio'       => $carrito->esta_vacio,
        ]);
    }

    // ─── Vaciar ───────────────────────────────────

    public function vaciar()
    {
        $carrito = $this->obtenerCarrito();
        $carrito->items()->delete();

        return response()->json(['ok' => true]);
    }

    // ─── Helper ───────────────────────────────────

    private function autorizarItem(CarritoItem $item): void
    {
        $carrito = $this->obtenerCarrito();
        abort_if($item->id_carrito !== $carrito->id, 403);
    }
}