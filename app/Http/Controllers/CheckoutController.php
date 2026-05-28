<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\DireccionUsuario;
use App\Models\Estado;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\ZonaEnvio;
use App\Models\ZonaEnvioAlcaldia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonInterface;

class CheckoutController extends Controller
{
    // ─── Helpers ──────────────────────────────────

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

    private function verificarAcceso(): bool
    {
        return Auth::check() || session('checkout_como_invitado');
    }

    private function horaActualCDMX(): int
    {
        return now()->setTimezone('America/Mexico_City')->hour;
    }

    private function fechaMinima(): \Carbon\CarbonInterface
    {
        $ahora = now()->setTimezone('America/Mexico_City');
        $hoy   = $ahora->copy()->startOfDay();
        return $ahora->hour >= 12 ? $hoy->addDay() : $hoy;
    }

    private function pasoCompletado(int $paso): bool
    {
        return match($paso) {
            1 => session()->has('checkout.paso1'),
            2 => session()->has('checkout.paso2'),
            default => false,
        };
    }

    // ─── Acceso ───────────────────────────────────

    public function acceso()
    {
        if (Auth::check()) {
            return redirect()->route('checkout.paso1');
        }

        if (session('checkout_como_invitado')) {
            return redirect()->route('checkout.paso1');
        }

        $carrito = $this->obtenerCarrito();

        if (!$carrito || $carrito->esta_vacio) {
            return redirect()->route('carrito.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        session()->put('url.intended', route('checkout.paso1'));

        return view('pages.public.checkout.acceso');
    }

    public function continuarComoInvitado()
    {
        session(['checkout_como_invitado' => true]);
        return redirect()->route('checkout.paso1');
    }

    // ─── Paso 1 — Contacto y Destinatario ─────────

    public function paso1()
    {
        if (!$this->verificarAcceso()) {
            return redirect()->route('checkout.acceso');
        }

        $carrito = $this->obtenerCarrito();
        if (!$carrito || $carrito->esta_vacio) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío.');
        }

        $datos   = session('checkout.paso1', []);
        $usuario = Auth::user();

        return view('pages.public.checkout.paso1', compact('carrito', 'datos', 'usuario'));
    }

    public function paso1Store(Request $request)
    {
        if (!$this->verificarAcceso()) {
            return redirect()->route('checkout.acceso');
        }

        $validated = $request->validate([
            'nombre'               => 'required|string|max:255',
            'email'                => 'required|email|max:255',
            'telefono'             => 'required|string|max:20',
            'es_regalo'            => 'boolean',
            'destinatario_nombre'  => 'nullable|string|max:255',
            'destinatario_telefono'=> 'nullable|string|max:20',
            'mensaje_tarjeta'      => 'nullable|string|max:500',
        ]);

        session(['checkout.paso1' => $validated]);

        return redirect()->route('checkout.paso2');
    }

    // ─── Paso 2 — Entrega ─────────────────────────

    public function paso2()
    {
        if (!$this->verificarAcceso()) {
            return redirect()->route('checkout.acceso');
        }

        if (!$this->pasoCompletado(1)) {
            return redirect()->route('checkout.paso1');
        }

        $carrito = $this->obtenerCarrito();
        if (!$carrito || $carrito->esta_vacio) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío.');
        }

        $datos     = session('checkout.paso2', []);
        $estados   = Estado::activos()->with('municipios')->get();
        $mapaZonas = ZonaEnvioAlcaldia::whereNotNull('id_municipio')
            ->with('zona')
            ->get()
            ->mapWithKeys(fn($a) => [
                $a->id_municipio => [
                    'id'     => $a->zona->id,
                    'nombre' => $a->zona->nombre,
                    'precio' => (float) $a->zona->precio,
                ]
            ]);

        $direcciones = Auth::check()
            ? DireccionUsuario::where('id_usuario', Auth::id())
                ->with(['estado', 'municipio'])
                ->orderByDesc('predeterminada')
                ->get()
            : collect();

        $hora        = $this->horaActualCDMX();
        $fechaMinima = $this->fechaMinima()->format('Y-m-d');

        return view('pages.public.checkout.paso2',
            compact('carrito', 'datos', 'estados', 'mapaZonas', 'direcciones', 'hora', 'fechaMinima'));
    }

    public function paso2Store(Request $request)
    {
        if (!$this->verificarAcceso()) {
            return redirect()->route('checkout.acceso');
        }

        if (!$this->pasoCompletado(1)) {
            return redirect()->route('checkout.paso1');
        }

        $validated = $request->validate([
            'tipo_entrega'  => 'required|in:envio,tienda',
            'id_zona'       => 'required_if:tipo_entrega,envio|nullable|exists:zonas_envio,id',
            'calle'         => 'required_if:tipo_entrega,envio|nullable|string|max:255',
            'numero_ext'    => 'required_if:tipo_entrega,envio|nullable|string|max:20',
            'numero_int'    => 'nullable|string|max:20',
            'colonia'       => 'required_if:tipo_entrega,envio|nullable|string|max:255',
            'cp'            => 'required_if:tipo_entrega,envio|nullable|string|max:10',
            'id_estado'     => 'required_if:tipo_entrega,envio|nullable|exists:estados,id',
            'id_municipio'  => 'required_if:tipo_entrega,envio|nullable|exists:municipios,id',
            'referencias'   => 'nullable|string|max:500',
            'fecha_entrega' => [
                'required', 'date',
                function ($attribute, $value, $fail) {
                    $fecha  = \Carbon\Carbon::parse($value)->startOfDay();
                    $minima = $this->fechaMinima();
                    if ($fecha->lt($minima)) {
                        $fail('La fecha mínima de entrega es ' . $minima->translatedFormat('d \d\e F'));
                    }
                },
            ],
            'bloque_entrega'   => 'required|in:manana,tarde,noche',
            'guardar_direccion' => 'boolean',
        ]);

        // Guardar dirección si lo pidió
        if (Auth::check() && !empty($validated['guardar_direccion'])) {
            DireccionUsuario::create([
                'id_usuario'      => Auth::id(),
                'alias'           => $request->input('alias', 'Casa'),
                'nombre_contacto' => session('checkout.paso1.nombre'),
                'telefono'        => session('checkout.paso1.telefono'),
                'calle'           => $validated['calle'],
                'numero_ext'      => $validated['numero_ext'],
                'numero_int'      => $validated['numero_int'] ?? null,
                'colonia'         => $validated['colonia'],
                'cp'              => $validated['cp'],
                'id_estado'       => $validated['id_estado'],
                'id_municipio'    => $validated['id_municipio'],
                'referencias'     => $validated['referencias'] ?? null,
                'predeterminada'  => DireccionUsuario::where('id_usuario', Auth::id())->count() === 0,
            ]);
        }

        session(['checkout.paso2' => $validated]);

        return redirect()->route('checkout.paso3');
    }

    // ─── Paso 3 — Pago ────────────────────────────

    public function paso3()
    {
        if (!$this->verificarAcceso()) {
            return redirect()->route('checkout.acceso');
        }

        if (!$this->pasoCompletado(1)) {
            return redirect()->route('checkout.paso1');
        }

        if (!$this->pasoCompletado(2)) {
            return redirect()->route('checkout.paso2');
        }

        $carrito = $this->obtenerCarrito();
        if (!$carrito || $carrito->esta_vacio) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío.');
        }

        $paso1     = session('checkout.paso1');
        $paso2     = session('checkout.paso2');
        $costoEnvio = 0;

        if ($paso2['tipo_entrega'] === 'envio' && !empty($paso2['id_zona'])) {
            $zona       = ZonaEnvio::find($paso2['id_zona']);
            $costoEnvio = $zona ? (float) $zona->precio : 0;
        }

        $total = $carrito->subtotal + $costoEnvio;

        return view('pages.public.checkout.paso3',
            compact('carrito', 'paso1', 'paso2', 'costoEnvio', 'total'));
    }

    public function paso3Store(Request $request)
    {
        if (!$this->verificarAcceso()) {
            return redirect()->route('checkout.acceso');
        }

        if (!$this->pasoCompletado(1) || !$this->pasoCompletado(2)) {
            return redirect()->route('checkout.paso1');
        }

        $request->validate([
            'metodo_pago' => 'required|in:tarjeta,efectivo,transferencia,contra_entrega',
            'notas'       => 'nullable|string|max:500',
        ]);
        

        $carrito = $this->obtenerCarrito();
        if (!$carrito || $carrito->esta_vacio) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío.');
        }

        $paso1 = session('checkout.paso1');
        $paso2 = session('checkout.paso2');

        $subtotal   = $carrito->subtotal;
        $costoEnvio = 0;

        if ($paso2['tipo_entrega'] === 'envio' && !empty($paso2['id_zona'])) {
            $zona       = ZonaEnvio::findOrFail($paso2['id_zona']);
            $costoEnvio = (float) $zona->precio;
        }

        $total = $subtotal + $costoEnvio;

        DB::beginTransaction();
        try {
            $pedido = Pedido::create([
                'numero'               => Pedido::generarNumero(),
                'id_usuario'           => Auth::id(),
                'id_carrito'           => $carrito->id,
                // Paso 1
                'nombre'               => $paso1['nombre'],
                'email'                => $paso1['email'],
                'telefono'             => $paso1['telefono'],
                'destinatario_nombre'  => $paso1['destinatario_nombre'] ?? null,
                'destinatario_telefono'=> $paso1['destinatario_telefono'] ?? null,
                'mensaje_tarjeta'      => $paso1['mensaje_tarjeta'] ?? null,
                // Paso 2
                'tipo_entrega'         => $paso2['tipo_entrega'],
                'id_zona'              => $paso2['id_zona'] ?? null,
                'calle'                => $paso2['calle'] ?? null,
                'numero_ext'           => $paso2['numero_ext'] ?? null,
                'numero_int'           => $paso2['numero_int'] ?? null,
                'colonia'              => $paso2['colonia'] ?? null,
                'cp'                   => $paso2['cp'] ?? null,
                'id_estado'            => $paso2['id_estado'] ?? null,
                'id_municipio'         => $paso2['id_municipio'] ?? null,
                'referencias'          => $paso2['referencias'] ?? null,
                'fecha_entrega'        => $paso2['fecha_entrega'],
                'bloque_entrega'       => $paso2['bloque_entrega'],
                // Paso 3
                'notas'                => $request->notas,
                // Totales
                'subtotal'             => $subtotal,
                'costo_envio'          => $costoEnvio,
                'total'                => $total,
                'estado'               => 'pendiente',
            ]);

            foreach ($carrito->items as $item) {
                PedidoItem::create([
                    'id_pedido'         => $pedido->id,
                    'id_producto'       => $item->id_producto,
                    'id_sku'            => $item->id_sku,
                    'nombre_snapshot'   => $item->nombre_snapshot,
                    'precio_snapshot'   => $item->precio_snapshot,
                    'cantidad'          => $item->cantidad,
                    'opciones_snapshot' => $item->opciones_snapshot,
                ]);
            }

            DB::commit();

            // Cargar relaciones necesarias para el correo
            $pedido->load('items', 'zona', 'pago');

            // Correo al cliente
            \Illuminate\Support\Facades\Mail::to($pedido->email)
                ->send(new \App\Mail\PedidoConfirmacion($pedido));

            // Correo al admin
            \Illuminate\Support\Facades\Mail::to(config('mail.admin'))
                ->send(new \App\Mail\AdminNuevoPedido($pedido));

            // Limpiar sesión de checkout
            session()->forget(['checkout', 'checkout_como_invitado']);

            if (in_array($request->metodo_pago, ['contra_entrega', 'transferencia'])) {
                \App\Models\Pago::create([
                    'id_pedido' => $pedido->id,
                    'metodo'    => $request->metodo_pago,
                    'monto'     => $total,
                    'estado'    => 'pendiente',
                ]);

                $carrito->items()->delete();

                return redirect()->route('checkout.confirmacion', $pedido->numero);
            }

            // Bypass Mercado Pago en desarrollo
            if (config('app.bypass_pagos')) {
                \App\Models\Pago::create([
                    'id_pedido' => $pedido->id,
                    'metodo'    => $request->metodo_pago,
                    'monto'     => $total,
                    'estado'    => 'aprobado',
                ]);

                $carrito->items()->delete();

                return redirect()->route('checkout.confirmacion', $pedido->numero);
            }

            session(['metodo_pago' => $request->metodo_pago]);
            return redirect()->route('pagos.pagar', $pedido->numero);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al procesar checkout', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Ocurrió un error. Intenta de nuevo.');
        }
    }

    // ─── Confirmación ─────────────────────────────

    public function confirmacion(Pedido $pedido)
    {
        if (Auth::check() && $pedido->id_usuario && $pedido->id_usuario !== Auth::id()) {
            abort(403);
        }

        $pedido->load('items', 'zona', 'pago', 'municipio');

        return view('pages.public.checkout.confirmacion', compact('pedido'));
    }
}