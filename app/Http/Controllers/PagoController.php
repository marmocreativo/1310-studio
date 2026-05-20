<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class PagoController extends Controller
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
    }

    public function pagar(string $numero)
    {
        $pedido = Pedido::where('numero', $numero)
            ->with('items')
            ->firstOrFail();

        if ($pedido->estado !== 'pendiente') {
            return redirect()->route('checkout.confirmacion', $numero);
        }

        $metodo = session('metodo_pago', 'tarjeta');

        return view('pages.public.pagos.pagar', compact('pedido', 'metodo'));
    }

    public function procesarTarjeta(Request $request)
    {
        $request->validate([
            'pedido_numero' => 'required|string',
            'token'         => 'required|string',
            'issuer_id'     => 'nullable|string',
            'payment_method_id' => 'required|string',
            'transaction_amount' => 'required|numeric',
            'installments'  => 'required|integer',
            'payer'         => 'required|array',
        ]);

        $pedido = Pedido::where('numero', $request->pedido_numero)
            ->with('items')
            ->firstOrFail();

        try {
            $client = new \MercadoPago\Client\Payment\PaymentClient();

            $payment = $client->create([
                'transaction_amount' => (float) $request->transaction_amount,
                'token'              => $request->token,
                'description'        => 'Pedido ' . $pedido->numero,
                'installments'       => (int) $request->installments,
                'payment_method_id'  => $request->payment_method_id,
                'issuer_id'          => $request->issuer_id,
                'payer'              => [
                    'email'          => $request->payer['email'],
                    'identification' => $request->payer['identification'] ?? null,
                ],
                'external_reference' => $pedido->numero,
                'notification_url'   => route('pagos.webhook'),
            ]);

            $estadoPago = match($payment->status) {
                'approved' => 'aprobado',
                'rejected' => 'rechazado',
                default    => 'pendiente',
            };

            // Actualizar o crear pago
            $pago = $pedido->pagos()->latest()->first();
            if ($pago) {
                $pago->update([
                    'mp_payment_id' => $payment->id,
                    'mp_status'     => $payment->status,
                    'estado'        => $estadoPago,
                    'datos_mp'      => (array) $payment,
                ]);
            } else {
                \App\Models\Pago::create([
                    'id_pedido'     => $pedido->id,
                    'metodo'        => 'tarjeta',
                    'mp_payment_id' => $payment->id,
                    'mp_status'     => $payment->status,
                    'monto'         => $pedido->total,
                    'estado'        => $estadoPago,
                    'datos_mp'      => (array) $payment,
                ]);
            }

            if ($estadoPago === 'aprobado') {
                $pedido->update(['estado' => 'pagado']);
                if ($pedido->carrito) {
                    $pedido->carrito->items()->delete();
                }
            }

            return response()->json([
                'ok'       => true,
                'estado'   => $estadoPago,
                'redirect' => route('checkout.confirmacion', $pedido->numero),
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al procesar tarjeta', ['error' => $e->getMessage()]);
            return response()->json([
                'ok'      => false,
                'message' => 'Error al procesar el pago. Intenta de nuevo.',
            ], 500);
        }
    }

    // ─── Crear preferencia ────────────────────────

    public function crearPreferencia(Request $request)
    {
        $numero = session('pedido_numero');
        $metodo = $request->input('metodo') ?? session('metodo_pago', 'tarjeta');

        if (!$numero) {
            return redirect()->route('carrito.index')
                ->with('error', 'No se encontró un pedido activo.');
        }

        $pedido = Pedido::where('numero', $numero)
            ->with('items')
            ->firstOrFail();

        // Mapear método a exclusiones de MP
        $excluirMetodos = match($metodo) {
            'tarjeta'  => [['id' => 'ticket']],    // excluir efectivo
            'efectivo' => [['id' => 'credit_card'], ['id' => 'debit_card']], // excluir tarjetas
            default    => [],
        };

        $items = $pedido->items->map(fn($item) => [
            'id'          => (string) $item->id,
            'title'       => $item->nombre_snapshot,
            'quantity'    => $item->cantidad,
            'unit_price'  => (float) $item->precio_snapshot,
            'currency_id' => 'MXN',
        ])->toArray();

        // Agregar envío como item si aplica
        if ($pedido->costo_envio > 0) {
            $items[] = [
                'id'         => 'envio',
                'title'      => 'Costo de envío',
                'quantity'   => 1,
                'unit_price' => (float) $pedido->costo_envio,
                'currency_id'=> 'MXN',
            ];
        }

        $client = new PreferenceClient();

        $preference = $client->create([
            'items'               => $items,
            'external_reference'  => $pedido->numero,
            'back_urls'           => [
                'success' => route('pagos.exito'),
                'failure' => route('pagos.fallo'),
                'pending' => route('pagos.pendiente'),
            ],
            'auto_return'         => 'approved',
            'notification_url'    => route('pagos.webhook'),
            'payment_methods'     => [
                'excluded_payment_types' => $excluirMetodos,
            ],
            'statement_descriptor'=> '1310 Studio',
            'expires'             => true,
            'expiration_date_to'  => now()->addHours(24)->toIso8601String(),
        ]);

        // Guardar preference_id
        Pago::create([
            'id_pedido'        => $pedido->id,
            'metodo'           => $metodo,
            'mp_preference_id' => $preference->id,
            'monto'            => $pedido->total,
            'estado'           => 'pendiente',
        ]);

        // Devolver preference_id al frontend para Bricks
        return response()->json([
            'ok'            => true,
            'preference_id' => $preference->id,
            'pedido_numero' => $pedido->numero,
            'total'         => $pedido->total,
        ]);
    }

    // ─── Resultado exitoso ────────────────────────

    public function exito(Request $request)
    {
        $numero    = $request->external_reference ?? session('pedido_numero');
        $paymentId = $request->payment_id;

        if (!$numero) {
            return redirect()->route('home');
        }

        $pedido = Pedido::where('numero', $numero)->with('pago')->first();

        if ($pedido && $paymentId) {
            $this->procesarPago($pedido, $paymentId);
        }

        // Vaciar carrito
        if ($pedido?->carrito) {
            $pedido->carrito->items()->delete();
        }

        session()->forget(['pedido_numero', 'metodo_pago']);

        return redirect()->route('checkout.confirmacion', $numero);
    }

    // ─── Pago fallido ─────────────────────────────

    public function fallo(Request $request)
    {
        $numero = $request->external_reference ?? session('pedido_numero');

        return redirect()->route('checkout.index')
            ->with('error', 'El pago no pudo procesarse. Intenta de nuevo.');
    }

    // ─── Pago pendiente ───────────────────────────

    public function pendiente(Request $request)
    {
        $numero = $request->external_reference ?? session('pedido_numero');

        if (!$numero) {
            return redirect()->route('home');
        }

        $pedido = Pedido::where('numero', $numero)->first();

        if ($pedido) {
            $pago = $pedido->pagos()->latest()->first();
            if ($pago) {
                $pago->update(['estado' => 'pendiente']);
            }
        }

        return redirect()->route('checkout.confirmacion', $numero);
    }

    // ─── Webhook ──────────────────────────────────

    public function webhook(Request $request)
    {
        Log::info('MP Webhook recibido', $request->all());

        $tipo = $request->type ?? $request->input('topic');

        if ($tipo !== 'payment') {
            return response()->json(['ok' => true]);
        }

        $paymentId = $request->data['id'] ?? $request->id;

        if (!$paymentId) {
            return response()->json(['ok' => false], 400);
        }

        try {
            $client  = new PaymentClient();
            $payment = $client->get($paymentId);

            $pedido = Pedido::where('numero', $payment->external_reference)
                ->with('pagos')
                ->first();

            if (!$pedido) {
                Log::warning('Pedido no encontrado en webhook', ['numero' => $payment->external_reference]);
                return response()->json(['ok' => false], 404);
            }

            $this->procesarPago($pedido, $paymentId, $payment);

        } catch (\Exception $e) {
            Log::error('Error en webhook MP', ['error' => $e->getMessage()]);
            return response()->json(['ok' => false], 500);
        }

        return response()->json(['ok' => true]);
    }

    // ─── Helper: procesar pago ────────────────────

    private function procesarPago(Pedido $pedido, string $paymentId, $paymentData = null): void
    {
        try {
            if (!$paymentData) {
                $client      = new PaymentClient();
                $paymentData = $client->get($paymentId);
            }

            $estadoPago = match($paymentData->status) {
                'approved' => 'aprobado',
                'rejected' => 'rechazado',
                'refunded' => 'reembolsado',
                default    => 'pendiente',
            };

            // Actualizar o crear registro de pago
            $pago = $pedido->pagos()->latest()->first();

            if ($pago) {
                $pago->update([
                    'mp_payment_id' => $paymentId,
                    'mp_status'     => $paymentData->status,
                    'estado'        => $estadoPago,
                    'datos_mp'      => (array) $paymentData,
                ]);
            }

            // Actualizar estado del pedido
            if ($estadoPago === 'aprobado' && $pedido->estado === 'pendiente') {
                $pedido->update(['estado' => 'pagado']);
            } elseif ($estadoPago === 'rechazado') {
                $pedido->update(['estado' => 'cancelado']);
            }

        } catch (\Exception $e) {
            Log::error('Error al procesar pago', ['error' => $e->getMessage(), 'payment_id' => $paymentId]);
        }
    }
}