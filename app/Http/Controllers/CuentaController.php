<?php

namespace App\Http\Controllers;

use App\Models\DireccionUsuario;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Pedido;
use App\Models\FechaImportante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CuentaController extends Controller
{
    // ─── Dashboard ────────────────────────────────

    public function dashboard()
    {
        $usuario = Auth::user();

        $totalPedidos  = Pedido::where('id_usuario', $usuario->id)->count();
        $pedidoReciente = Pedido::where('id_usuario', $usuario->id)
            ->with('items')
            ->latest()
            ->first();

        $pedidosActivos = Pedido::where('id_usuario', $usuario->id)
            ->whereIn('estado', ['pendiente', 'pagado', 'preparando', 'enviado'])
            ->count();

        return view('pages.public.cuenta.dashboard', compact(
            'usuario', 'totalPedidos', 'pedidoReciente', 'pedidosActivos'
        ));
    }

    // ─── Pedidos ──────────────────────────────────

    public function pedidos(Request $request)
    {
        $query = Pedido::where('id_usuario', Auth::id())
            ->with(['items', 'zona'])
            ->latest();

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $pedidos = $query->paginate(10)->withQueryString();

        return view('pages.public.cuenta.pedidos.index', compact('pedidos'));
    }

    public function pedidoShow(Pedido $pedido)
    {
        abort_if($pedido->id_usuario !== Auth::id(), 403);
        $pedido->load(['items', 'zona', 'pago']);

        return view('pages.public.cuenta.pedidos.show', compact('pedido'));
    }

    public function pedidoCancelar(Request $request, Pedido $pedido)
    {
        abort_if($pedido->id_usuario !== Auth::id(), 403);
        abort_if(!in_array($pedido->estado, ['pendiente', 'pagado']), 422);

        $request->validate([
            'motivo' => 'required|string|max:500',
        ]);

        $pedido->update([
            'estado'          => 'cancelado',
            'notas_cancelacion' => $request->motivo,
        ]);

        $pedido->load('items', 'zona', 'pago');

        \Illuminate\Support\Facades\Mail::to(config('mail.admin'))
            ->send(new \App\Mail\AdminCancelacion($pedido));

        return back()->with('success', 'Pedido cancelado correctamente.');
    }

    public function pedidoCambioFecha(Request $request, Pedido $pedido)
    {
        abort_if($pedido->id_usuario !== Auth::id(), 403);
        abort_if(!in_array($pedido->estado, ['pendiente', 'pagado', 'preparando']), 422);

        $request->validate([
            'nueva_fecha'   => 'required|date|after:today',
            'nuevo_bloque'  => 'required|in:manana,tarde,noche',
            'motivo'        => 'nullable|string|max:500',
        ]);

        // Por ahora guardamos como nota — en el futuro puede disparar notificación al admin
        $nota = "Solicitud de cambio de fecha: {$request->nueva_fecha} ({$request->nuevo_bloque})";
        if ($request->motivo) {
            $nota .= " — Motivo: {$request->motivo}";
        }

        $pedido->update([
            'notas' => $pedido->notas ? $pedido->notas . "\n" . $nota : $nota,
        ]);

        return back()->with('success', 'Solicitud de cambio de fecha enviada. Te contactaremos pronto.');
    }

    public function pedidoReporte(Request $request, Pedido $pedido)
    {
        abort_if($pedido->id_usuario !== Auth::id(), 403);

        $request->validate([
            'tipo'        => 'required|in:dano,faltante,retraso,otro',
            'descripcion' => 'required|string|max:1000',
        ]);

        $nota = "Reporte [{$request->tipo}]: {$request->descripcion}";

        $pedido->update([
            'notas' => $pedido->notas ? $pedido->notas . "\n" . $nota : $nota,
        ]);

        \Illuminate\Support\Facades\Mail::to(config('mail.admin'))
            ->send(new \App\Mail\AdminReporte($pedido, $request->tipo, $request->descripcion));

        return back()->with('success', 'Reporte enviado. Te contactaremos a la brevedad.');
    }

    // ─── Direcciones ──────────────────────────────

    public function direcciones()
    {
        $direcciones = DireccionUsuario::where('id_usuario', Auth::id())
            ->with(['estado', 'municipio'])
            ->orderByDesc('predeterminada')
            ->get();

        $estados = Estado::activos()->with('municipios')->get();

        return view('pages.public.cuenta.direcciones', compact('direcciones', 'estados'));
    }

    public function direccionStore(Request $request)
    {
        $data = $request->validate([
            'alias'           => 'nullable|string|max:50',
            'nombre_contacto' => 'required|string|max:255',
            'telefono'        => 'required|string|max:20',
            'calle'           => 'required|string|max:255',
            'numero_ext'      => 'required|string|max:20',
            'numero_int'      => 'nullable|string|max:20',
            'colonia'         => 'required|string|max:255',
            'cp'              => 'required|string|max:10',
            'id_estado'       => 'required|exists:estados,id',
            'id_municipio'    => 'required|exists:municipios,id',
            'referencias'     => 'nullable|string|max:500',
            'predeterminada'  => 'boolean',
        ]);

        $data['id_usuario'] = Auth::id();
        $data['alias']      = $data['alias'] ?? 'Casa';

        if (!empty($data['predeterminada'])) {
            DireccionUsuario::where('id_usuario', Auth::id())
                ->update(['predeterminada' => false]);
        }

        if (DireccionUsuario::where('id_usuario', Auth::id())->count() === 0) {
            $data['predeterminada'] = true;
        }

        DireccionUsuario::create($data);

        return back()->with('success', 'Dirección agregada correctamente.');
    }

    public function direccionUpdate(Request $request, DireccionUsuario $direccion)
    {
        abort_if($direccion->id_usuario !== Auth::id(), 403);

        $data = $request->validate([
            'alias'           => 'nullable|string|max:50',
            'nombre_contacto' => 'required|string|max:255',
            'telefono'        => 'required|string|max:20',
            'calle'           => 'required|string|max:255',
            'numero_ext'      => 'required|string|max:20',
            'numero_int'      => 'nullable|string|max:20',
            'colonia'         => 'required|string|max:255',
            'cp'              => 'required|string|max:10',
            'id_estado'       => 'required|exists:estados,id',
            'id_municipio'    => 'required|exists:municipios,id',
            'referencias'     => 'nullable|string|max:500',
            'predeterminada'  => 'boolean',
        ]);

        if (!empty($data['predeterminada'])) {
            DireccionUsuario::where('id_usuario', Auth::id())
                ->update(['predeterminada' => false]);
        }

        $direccion->update($data);

        return back()->with('success', 'Dirección actualizada correctamente.');
    }

    public function direccionDestroy(DireccionUsuario $direccion)
    {
        abort_if($direccion->id_usuario !== Auth::id(), 403);
        $direccion->delete();

        if ($direccion->predeterminada) {
            DireccionUsuario::where('id_usuario', Auth::id())
                ->latest()->first()?->update(['predeterminada' => true]);
        }

        return back()->with('success', 'Dirección eliminada.');
    }

    public function direccionPredeterminada(DireccionUsuario $direccion)
    {
        abort_if($direccion->id_usuario !== Auth::id(), 403);

        DireccionUsuario::where('id_usuario', Auth::id())
            ->update(['predeterminada' => false]);

        $direccion->update(['predeterminada' => true]);

        return back()->with('success', 'Dirección predeterminada actualizada.');
    }

    // ─── Perfil ───────────────────────────────────

    public function perfil()
    {
        return view('pages.public.cuenta.perfil', ['usuario' => Auth::user()]);
    }

    public function perfilUpdate(Request $request)
    {
        $usuario = Auth::user();

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $usuario->id,
        ]);

        $usuario->update($validated);

        return back()->with('success', 'Datos actualizados correctamente.');
    }

    // ─── Contraseña ───────────────────────────────

    public function password()
    {
        return view('pages.public.cuenta.password');
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'actual'        => 'required|string',
            'nueva'         => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->actual, Auth::user()->password)) {
            return back()->withErrors(['actual' => 'La contraseña actual no es correcta.']);
        }

        Auth::user()->update(['password' => Hash::make($request->nueva)]);

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }

    // ─── Fechas importantes ───────────────────────

   public function fechas()
    {
        $fechas = FechaImportante::where('id_usuario', Auth::id())
            ->orderBy('mes')
            ->orderBy('dia')
            ->get();

        return view('pages.public.cuenta.fechas', compact('fechas'));
    }

    public function fechaStore(Request $request)
    {
        $request->validate([
            'dia'      => 'required|integer|min:1|max:31',
            'mes'      => 'required|integer|min:1|max:12',
            'etiqueta' => 'required|string|max:100',
        ]);

        // Validar que el día sea válido para el mes
        $diasEnMes = \Carbon\Carbon::create(null, $request->mes, 1)->daysInMonth;
        if ($request->dia > $diasEnMes) {
            return back()->withErrors(['dia' => 'El día no es válido para el mes seleccionado.']);
        }

        FechaImportante::create([
            'id_usuario' => Auth::id(),
            'dia'        => $request->dia,
            'mes'        => $request->mes,
            'etiqueta'   => $request->etiqueta,
        ]);

        return response()->json(['ok' => true]);
    }

    public function fechaDestroy(FechaImportante $fecha)
    {
        abort_if($fecha->id_usuario !== Auth::id(), 403);
        $fecha->delete();

        return response()->json(['ok' => true]);
    }
}