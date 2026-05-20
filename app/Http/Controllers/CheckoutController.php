<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\ZonaEnvio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    private function obtenerCarrito(): ?Carrito
    {
        $sessionId = session()->getId();

        if (Auth::check()) {
            return Carrito::where('id_usuario', Auth::id())
                ->noExpirados()
                ->with('items.producto', 'items.sku.opciones.tipo')
                ->first();
        }

        return Carrito::where('sesion_id', $sessionId)
            ->whereNull('id_usuario')
            ->noExpirados()
            ->with('items.producto', 'items.sku.opciones.tipo')
            ->first();
    }

    // ─── Index ────────────────────────────────────

    public function index()
    {
        $carrito = $this->obtenerCarrito();

        if (!$carrito || $carrito->esta_vacio) {
            return redirect()->route('carrito.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        $zonas = ZonaEnvio::activas()->with('alcaldias')->get();

        $usuario = Auth::user();

        return view('pages.public.checkout.index', compact('carrito', 'zonas', 'usuario'));
    }

    // ─── Procesar ─────────────────────────────────

    public function procesar(Request $request)
    {
        $carrito = $this->obtenerCarrito();

        if (!$carrito || $carrito->esta_vacio) {
            return redirect()->route('carrito.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        $request->validate([
            'nombre'               => 'required|string|max:255',
            'email'                => 'required|email|max:255',
            'telefono'             => 'required|string|max:20',
            'tipo_entrega'         => 'required|in:envio,tienda',
            'id_zona'              => 'required_if:tipo_entrega,envio|nullable|exists:zonas_envio,id',
            'direccion'            => 'required_if:tipo_entrega,envio|nullable|string|max:500',
            'colonia'              => 'required_if:tipo_entrega,envio|nullable|string|max:255',
            'municipio'            => 'required_if:tipo_entrega,envio|nullable|string|max:255',
            'cp'                   => 'required_if:tipo_entrega,envio|nullable|string|max:10',
            'referencias'          => 'nullable|string|max:500',
            'fecha_entrega'        => 'required|date|after:today',
            'bloque_entrega'       => 'required|in:manana,tarde,noche',
            'destinatario_nombre'  => 'nullable|string|max:255',
            'destinatario_telefono'=> 'nullable|string|max:20',
            'mensaje_tarjeta'      => 'nullable|string|max:500',
            'metodo_pago'          => 'required|in:tarjeta,efectivo,contra_entrega',
            'notas'                => 'nullable|string|max:500',
        ]);

        // Calcular totales
        $subtotal    = $carrito->subtotal;
        $costoEnvio  = 0;

        if ($request->tipo_entrega === 'envio') {
            $zona       = ZonaEnvio::findOrFail($request->id_zona);
            $costoEnvio = (float) $zona->precio;
        }

        $total = $subtotal + $costoEnvio;

        DB::beginTransaction();

        try {
            // Crear pedido
            $pedido = Pedido::create([
                'numero'               => Pedido::generarNumero(),
                'id_usuario'           => Auth::id(),
                'id_carrito'           => $carrito->id,
                'nombre'               => $request->nombre,
                'email'                => $request->email,
                'telefono'             => $request->telefono,
                'tipo_entrega'         => $request->tipo_entrega,
                'id_zona'              => $request->id_zona,
                'direccion'            => $request->direccion,
                'colonia'              => $request->colonia,
                'municipio'            => $request->municipio,
                'cp'                   => $request->cp,
                'referencias'          => $request->referencias,
                'fecha_entrega'        => $request->fecha_entrega,
                'bloque_entrega'       => $request->bloque_entrega,
                'destinatario_nombre'  => $request->destinatario_nombre,
                'destinatario_telefono'=> $request->destinatario_telefono,
                'mensaje_tarjeta'      => $request->mensaje_tarjeta,
                'subtotal'             => $subtotal,
                'costo_envio'          => $costoEnvio,
                'total'                => $total,
                'estado'               => 'pendiente',
                'notas'                => $request->notas,
            ]);

            // Copiar items del carrito al pedido
            foreach ($carrito->items as $item) {
                PedidoItem::create([
                    'id_pedido'        => $pedido->id,
                    'id_producto'      => $item->id_producto,
                    'id_sku'           => $item->id_sku,
                    'nombre_snapshot'  => $item->nombre_snapshot,
                    'precio_snapshot'  => $item->precio_snapshot,
                    'cantidad'         => $item->cantidad,
                    'opciones_snapshot'=> $item->opciones_snapshot,
                ]);
            }

            DB::commit();

            // Guardar metodo de pago y pedido en sesión para el siguiente paso
            session([
                'pedido_numero'  => $pedido->numero,
                'metodo_pago'    => $request->metodo_pago,
            ]);

            // Contra entrega: saltar Mercado Pago
            if ($request->metodo_pago === 'contra_entrega') {
                \App\Models\Pago::create([
                    'id_pedido' => $pedido->id,
                    'metodo'    => 'contra_entrega',
                    'monto'     => $total,
                    'estado'    => 'pendiente',
                ]);

                $carrito->items()->delete();

                return redirect()->route('checkout.confirmacion', $pedido->numero);
            }

            // Tarjeta o efectivo: ir a pago con Mercado Pago
            session(['metodo_pago' => $request->metodo_pago]);
            return redirect()->route('pagos.pagar', $pedido->numero);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al procesar checkout', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->with('error', 'Ocurrió un error al procesar tu pedido. Intenta de nuevo.');
        }
    }

    // ─── Confirmación ─────────────────────────────

    public function confirmacion(Pedido $pedido)
    {
        // Solo el dueño del pedido puede ver la confirmación
        if (Auth::check() && $pedido->id_usuario && $pedido->id_usuario !== Auth::id()) {
            abort(403);
        }

        $pedido->load('items', 'zona', 'pago');

        return view('pages.public.checkout.confirmacion', compact('pedido'));
    }
}