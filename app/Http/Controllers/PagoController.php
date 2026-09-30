<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Order\OrderClient;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\MercadoPagoConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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
            'pedido_numero'       => 'required|string',
            'token'               => 'required|string',
            'issuer_id'           => 'nullable|string',
            'payment_method_id'   => 'required|string',
            'transaction_amount'  => 'required|numeric',
            'installments'        => 'required|integer',
            'payer'               => 'required|array',
        ]);

        $pedido = Pedido::where('numero', $request->pedido_numero)->with('items')->firstOrFail();

        $paymentMethod = [
            'id'           => $request->payment_method_id,
            'type'         => $this->resolverTipoPaymentMethod($request->payment_method_id),
            'token'        => $request->token,
            'installments' => (int) $request->installments,
        ];

        if ($request->filled('issuer_id')) {
            $paymentMethod['issuer_id'] = $request->issuer_id;
        }

        $monto = (float) $request->transaction_amount;

        try {
            $client = new OrderClient();

            $requestOptions = new RequestOptions();
            $requestOptions->setCustomHeaders(['X-Idempotency-Key: ' . (string) Str::uuid()]);

            $order = $client->create([
                'type'               => 'online',
                'processing_mode'    => 'automatic',
                'total_amount'       => number_format($monto, 2, '.', ''),
                'external_reference' => $pedido->numero,
                'notification_url'   => route('pagos.webhook'),
                'payer' => [
                    'email'          => $request->payer['email'],
                    'identification' => $request->payer['identification'] ?? null,
                ],
                'transactions' => [
                    'payments' => [
                        [
                            'amount'         => number_format($monto, 2, '.', ''),
                            'payment_method' => $paymentMethod,
                        ],
                    ],
                ],
            ], $requestOptions);

            $datosPago = $this->guardarPagoDesdeOrder($pedido, $order);

            return response()->json([
                'ok'       => true,
                'estado'   => $datosPago['estado'],
                'redirect' => route('checkout.confirmacion', $pedido->numero),
            ]);

        } catch (\Exception $e) {
            Log::error('Error al procesar tarjeta', ['error' => $e->getMessage()]);
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

        try {
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
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            Log::error('Error creando preferencia MP', [
                'error' => $e->getApiResponse()?->getContent() ?? $e->getMessage(),
            ]);
            return response()->json([
                'ok'      => false,
                'message' => 'No se pudo iniciar el pago.',
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error inesperado creando preferencia MP', ['error' => $e->getMessage()]);
            return response()->json([
                'ok'      => false,
                'message' => 'No se pudo iniciar el pago.',
            ], 500);
        }

        // Guardar preference_id
        Pago::create([
            'id_pedido'        => $pedido->id,
            'metodo'           => $metodo,
            'mp_preference_id' => $preference->id,
            'monto'            => $pedido->total,
            'estado'           => 'pendiente',
        ]);

        return response()->json([
            'ok'         => true,
            'init_point' => $preference->init_point,
        ]);
    }

        // ─── Procesar pago del Payment Brick ──────────

        public function procesarPagoBrick(Request $request)
    {
        $numero = session('pedido_numero');
        $pedido = Pedido::where('numero', $numero)->with('items')->firstOrFail();

        Log::info('formData recibido del Brick', $request->all());

        $payer = ['email' => $request->input('payer.email')];

        if ($request->filled('payer.identification.type') && $request->filled('payer.identification.number')) {
            $payer['identification'] = [
                'type'   => $request->input('payer.identification.type'),
                'number' => $request->input('payer.identification.number'),
            ];
        }

        $esTicket = $request->filled('payment_method_id') && !$request->filled('token');

        if ($esTicket) {
            // OXXO / Paycash / etc.
            $payer['first_name'] = $request->input('payer.first_name');
            $payer['last_name']  = $request->input('payer.last_name');

            $paymentMethod = [
                'id'   => $request->input('payment_method_id'),
                'type' => 'ticket',
            ];
        } else {
            // Tarjeta
            $paymentMethod = [
                'id'           => $request->input('payment_method_id'),
                'type'         => $this->resolverTipoPaymentMethod($request->input('payment_method_id')),
                'token'        => $request->input('token'),
                'installments' => (int) $request->input('installments', 1),
            ];

            if ($request->filled('issuer_id')) {
                $paymentMethod['issuer_id'] = $request->input('issuer_id');
            }
        }

        $monto = (float) $request->input('transaction_amount');

        $orderRequest = [
            'type'               => 'online',
            'processing_mode'    => 'automatic',
            'total_amount'       => number_format($monto, 2, '.', ''),
            'external_reference' => $pedido->numero,
            'notification_url'   => route('pagos.webhook'),
            'payer'              => $payer,
            'transactions'       => [
                'payments' => [
                    [
                        'amount'         => number_format($monto, 2, '.', ''),
                        'payment_method' => $paymentMethod,
                    ],
                ],
            ],
        ];

        if ($esTicket) {
            $orderRequest['payment_expiration_time'] = 'P3D';
        }

        try {
            $client = new OrderClient();

            $requestOptions = new RequestOptions();
            $requestOptions->setCustomHeaders(['X-Idempotency-Key: ' . (string) Str::uuid()]);

            $order = $client->create($orderRequest, $requestOptions);

            $datosPago = $this->guardarPagoDesdeOrder($pedido, $order);

            $pagoTicket = null;
            if ($esTicket) {
                $pagoTicket = $order->transactions->payments[0]->payment_method ?? null;
            }

            return response()->json([
                'ok'            => true,
                'id'            => $order->id,
                'status'        => $order->status,
                'status_detail' => $order->status_detail ?? null,
                'ticket'        => $pagoTicket ? [
                    'ticket_url'        => $pagoTicket->ticket_url ?? null,
                    'barcode_content'   => $pagoTicket->barcode_content ?? null,
                    'reference'         => $pagoTicket->reference ?? null,
                    'verification_code' => $pagoTicket->verification_code ?? null,
                ] : null,
                'redirect'      => $datosPago['estado'] === 'aprobado'
                    ? route('checkout.confirmacion', $pedido->numero)
                    : null,
            ]);

        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            Log::error('Error MP procesarPagoBrick', ['error' => $e->getApiResponse()?->getContent() ?? $e->getMessage()]);
            return response()->json(['ok' => false, 'message' => 'No se pudo procesar el pago. Verifica los datos.'], 422);
        } catch (\Exception $e) {
            Log::error('Error procesarPagoBrick', ['error' => $e->getMessage()]);
            return response()->json(['ok' => false, 'message' => 'Error al procesar el pago.'], 500);
        }
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

        if ($numero) {
            $pedido = Pedido::where('numero', $numero)->first();

            if ($pedido) {
                $pago = $pedido->pagos()->latest()->first();

                if ($pago) {
                    $pago->update([
                        'estado'    => 'rechazado',
                        'mp_status' => $request->collapse_status ?? $request->status ?? 'failed',
                    ]);
                }
            }
        }

        return redirect()->route('checkout.index')
            ->with('error', 'El pago no pudo procesarse. Puedes intentar de nuevo o elegir otro método de pago.');
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

        if ($tipo === 'order') {
            $orderId = $request->input('data.id');

            if (!$orderId) {
                return response()->json(['ok' => false], 400);
            }

            try {
                $client = new OrderClient();
                $order  = $client->get($orderId);

                $pedido = Pedido::where('numero', $order->external_reference)
                    ->with('pagos')
                    ->first();

                if (!$pedido) {
                    Log::warning('Pedido no encontrado en webhook (order)', ['numero' => $order->external_reference]);
                    return response()->json(['ok' => false], 404);
                }

                $this->guardarPagoDesdeOrder($pedido, $order);

            } catch (\Exception $e) {
                Log::error('Error en webhook MP (order)', ['error' => $e->getMessage()]);
                return response()->json(['ok' => false], 500);
            }

            return response()->json(['ok' => true]);
        }

        if ($tipo === 'payment') {
            // Notificaciones de Checkout Pro (crearPreferencia)
            $paymentId = $request->input('data.id') ?? $request->input('id');

            if (!$paymentId) {
                return response()->json(['ok' => false], 400);
            }

            try {
                $client      = new PaymentClient();
                $paymentData = $client->get($paymentId);

                $pedido = Pedido::where('numero', $paymentData->external_reference)
                    ->with('pagos')
                    ->first();

                if (!$pedido) {
                    Log::warning('Pedido no encontrado en webhook (payment)', ['numero' => $paymentData->external_reference]);
                    return response()->json(['ok' => false], 404);
                }

                $this->procesarPago($pedido, $paymentId, $paymentData);

            } catch (\Exception $e) {
                Log::error('Error en webhook MP (payment)', ['error' => $e->getMessage()]);
                return response()->json(['ok' => false], 500);
            }

            return response()->json(['ok' => true]);
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
            if ($estadoPago === 'aprobado' && $pedido->estado !== 'pagado') {
                $yaEstabaPagado = $pedido->estado === 'pagado';
                $pedido->update(['estado' => 'pagado']);
                $pedido->carrito?->items()->delete();

                if (!$yaEstabaPagado) {
                    $pedido->load('items', 'zona');

                    \Illuminate\Support\Facades\Mail::to($pedido->email)
                        ->send(new \App\Mail\PedidoConfirmacion($pedido));

                    \Illuminate\Support\Facades\Mail::to(config('mail.admin'))
                        ->send(new \App\Mail\AdminNuevoPedido($pedido));
                }
            } elseif ($estadoPago === 'rechazado') {
                $pedido->update(['estado' => 'cancelado']);
            }

        } catch (\Exception $e) {
            Log::error('Error al procesar pago', ['error' => $e->getMessage(), 'payment_id' => $paymentId]);
        }
    }

    private function resolverTipoPaymentMethod(string $paymentMethodId): string
    {
        return Cache::remember("mp_payment_method_type_{$paymentMethodId}", now()->addDay(), function () use ($paymentMethodId) {
            $response = Http::withToken(config('services.mercadopago.access_token'))
                ->get('https://api.mercadopago.com/v1/payment_methods');

            $metodo = collect($response->json())->firstWhere('id', $paymentMethodId);

            return $metodo['payment_type_id'] ?? 'credit_card';
        });
    }

    private function guardarPagoDesdeOrder(Pedido $pedido, $order): array
    {
        $estadoPago = match($order->status) {
            'processed'            => 'aprobado',
            'failed', 'canceled'   => 'rechazado',
            'refunded'             => 'reembolsado',
            default                => 'pendiente', // created, action_required, processing, expired
        };

        $pagoTransaccion = $order->transactions->payments[0] ?? null;

        $datosPago = [
            'metodo'        => $pagoTransaccion->payment_method->id ?? null,
            'mp_payment_id' => $order->id,
            'mp_status'     => $order->status,
            'estado'        => $estadoPago,
            'datos_mp'      => (array) $order,
        ];

        $pago = $pedido->pagos()->latest()->first();
        $pago
            ? $pago->update($datosPago)
            : Pago::create(array_merge(['id_pedido' => $pedido->id, 'monto' => $pedido->total], $datosPago));

        if ($estadoPago === 'aprobado') {
        $pedido->update(['estado' => 'pagado']);
        $pedido->carrito?->items()->delete();
        session()->forget(['pedido_numero', 'metodo_pago']);

        // Solo notificamos la primera vez que se confirma el pago,
        // no en cada webhook subsecuente para el mismo pedido.
        if (is_null($pedido->getOriginal('estado')) || $pedido->getOriginal('estado') !== 'pagado') {
            $pedido->load('items', 'zona');

            \Illuminate\Support\Facades\Mail::to($pedido->email)
                ->send(new \App\Mail\PedidoConfirmacion($pedido));

            \Illuminate\Support\Facades\Mail::to(config('mail.admin'))
                ->send(new \App\Mail\AdminNuevoPedido($pedido));
        }
    }

    return $datosPago;
}
}