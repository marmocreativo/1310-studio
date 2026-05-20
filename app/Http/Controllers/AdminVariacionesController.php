<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\VariacionTipo;
use App\Models\VariacionOpcion;
use App\Models\VariacionSku;
use App\Models\VariacionTipoDefault;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Illuminate\Support\Str;

class AdminVariacionesController extends Controller
{
    // ─── Index (devuelve JSON con toda la data del producto) ──────────────────

    public function index(Producto $producto)
    {
        return response()->json($this->buildPayload($producto));
    }

    // ─── Tipos ────────────────────────────────────────────────────────────────

    public function tipoStore(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'nombre'          => 'required|string|max:255',
            'orden'           => 'integer|min:0',
            'id_tipo_default' => 'nullable|exists:variacion_tipos_default,id',
        ]);

        $data['id_producto'] = $producto->id;
        $data['orden'] = $data['orden'] ?? ($producto->variacionTipos()->max('orden') + 1);

        $tipo = VariacionTipo::create($data);

        return response()->json([
            'ok'   => true,
            'tipo' => $this->formatTipo($tipo->load('opciones')),
        ]);
    }

    public function tipoUpdate(Request $request, Producto $producto, VariacionTipo $tipo)
    {
        $this->autorizarTipo($tipo, $producto);

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $tipo->update($data);

        return response()->json(['ok' => true, 'nombre' => $tipo->nombre]);
    }

    public function tipoDestroy(Producto $producto, VariacionTipo $tipo)
    {
        $this->autorizarTipo($tipo, $producto);

        // Eliminar imágenes de opciones
        foreach ($tipo->opciones as $opcion) {
            if ($opcion->imagen) {
                Storage::disk('public')->delete($opcion->imagen);
            }
        }

        // Eliminar imágenes de SKUs huérfanos
        foreach ($producto->variacionSkus as $sku) {
            if ($sku->imagen) {
                Storage::disk('public')->delete($sku->imagen);
            }
        }

        $tipo->delete(); // cascade elimina opciones y rompe SKUs

        // Limpiar SKUs que quedaron sin opciones válidas
        $this->limpiarSkusHuerfanos($producto);

        return response()->json(['ok' => true, 'payload' => $this->buildPayload($producto->fresh())]);
    }

    public function tipoOrden(Request $request, Producto $producto, VariacionTipo $tipo)
    {
        $this->autorizarTipo($tipo, $producto);

        $request->validate(['orden' => 'required|integer|min:0']);
        $tipo->update(['orden' => $request->orden]);

        return response()->json(['ok' => true]);
    }

    // ─── Opciones ─────────────────────────────────────────────────────────────

    public function opcionStore(Request $request, Producto $producto, VariacionTipo $tipo)
    {
        $this->autorizarTipo($tipo, $producto);

        $data = $request->validate([
            'nombre'            => 'required|string|max:255',
            'orden'             => 'integer|min:0',
            'id_opcion_default' => 'nullable|exists:variacion_opciones_default,id',
            'imagen'            => 'nullable|image|max:2048',
        ]);

        $data['id_tipo'] = $tipo->id;
        $data['orden']   = $data['orden'] ?? ($tipo->opciones()->max('orden') + 1);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $this->procesarImagen($request->file('imagen'), 'variaciones/opciones');
        }

        $opcion = VariacionOpcion::create($data);

        return response()->json([
            'ok'     => true,
            'opcion' => $this->formatOpcion($opcion),
        ]);
    }

    public function opcionUpdate(Request $request, Producto $producto, VariacionOpcion $opcion)
    {
        $this->autorizarOpcion($opcion, $producto);

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'imagen' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            if ($opcion->imagen) {
                Storage::disk('public')->delete($opcion->imagen);
            }
            $data['imagen'] = $this->procesarImagen($request->file('imagen'), 'variaciones/opciones');
        }

        $opcion->update($data);

        return response()->json([
            'ok'     => true,
            'opcion' => $this->formatOpcion($opcion->fresh()),
        ]);
    }

    public function opcionDestroy(Producto $producto, VariacionOpcion $opcion)
    {
        $this->autorizarOpcion($opcion, $producto);

        if ($opcion->imagen) {
            Storage::disk('public')->delete($opcion->imagen);
        }

        $opcion->delete();

        $this->limpiarSkusHuerfanos($producto);

        return response()->json(['ok' => true, 'payload' => $this->buildPayload($producto->fresh())]);
    }

    // ─── SKUs ─────────────────────────────────────────────────────────────────

    public function skuStore(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'precio_venta' => 'required|numeric|min:0',
            'precio_lista' => 'nullable|numeric|min:0',
            'estado'       => 'boolean',
            'notas'        => 'nullable|string|max:500',
            'opciones'     => 'required|array',
            'opciones.*'   => 'exists:variacion_opciones,id',
        ]);

        // Verificar que las opciones pertenecen al producto
        $this->validarOpcionesDelProducto($data['opciones'], $producto);

        $sku = VariacionSku::create([
            'id_producto'  => $producto->id,
            'precio_venta' => $data['precio_venta'],
            'precio_lista' => $data['precio_lista'] ?? null,
            'estado'       => $data['estado'] ?? true,
            'notas'        => $data['notas'] ?? null,
        ]);

        $sku->opciones()->sync($data['opciones']);

        return response()->json([
            'ok'  => true,
            'sku' => $this->formatSku($sku->load('opciones')),
        ]);
    }

    public function skuUpdate(Request $request, Producto $producto, VariacionSku $sku)
    {
        $this->autorizarSku($sku, $producto);

        $data = $request->validate([
            'precio_venta' => 'required|numeric|min:0',
            'precio_lista' => 'nullable|numeric|min:0',
            'estado'       => 'boolean',
            'notas'        => 'nullable|string|max:500',
        ]);

        $sku->update($data);

        return response()->json([
            'ok'  => true,
            'sku' => $this->formatSku($sku->load('opciones')),
        ]);
    }

    public function skuDestroy(Producto $producto, VariacionSku $sku)
    {
        $this->autorizarSku($sku, $producto);

        if ($sku->imagen) {
            Storage::disk('public')->delete($sku->imagen);
        }

        $sku->delete();

        return response()->json(['ok' => true]);
    }

    public function skuImagen(Request $request, Producto $producto, VariacionSku $sku)
    {
        $this->autorizarSku($sku, $producto);

        $request->validate(['imagen' => 'required|image|max:2048']);

        if ($sku->imagen) {
            Storage::disk('public')->delete($sku->imagen);
        }

        $sku->update([
            'imagen' => $this->procesarImagen($request->file('imagen'), 'variaciones/skus'),
        ]);

        return response()->json([
            'ok'  => true,
            'url' => Storage::disk('public')->url($sku->imagen),
        ]);
    }

    // Genera automáticamente todos los SKUs posibles (producto cartesiano de opciones)
    public function skuGenerar(Request $request, Producto $producto)
    {
        $tipos = $producto->variacionTipos()->with('opciones')->ordenados()->get();

        if ($tipos->isEmpty() || $tipos->some(fn($t) => $t->opciones->isEmpty())) {
            return response()->json([
                'ok'      => false,
                'message' => 'Todos los tipos deben tener al menos una opción antes de generar combinaciones.',
            ], 422);
        }

        // Producto cartesiano
        $combinaciones = $this->cartesiano($tipos->map(fn($t) => $t->opciones->pluck('id')->toArray())->toArray());

        $creados = 0;

        foreach ($combinaciones as $combo) {
            sort($combo);

            // Verificar si ya existe un SKU con exactamente estas opciones
            $existe = VariacionSku::where('id_producto', $producto->id)
                ->whereHas('opciones', function ($q) use ($combo) {
                    $q->whereIn('variacion_opciones.id', $combo);
                }, '=', count($combo))
                ->whereDoesntHave('opciones', function ($q) use ($combo) {
                    $q->whereNotIn('variacion_opciones.id', $combo);
                })
                ->exists();

            if (! $existe) {
                $sku = VariacionSku::create([
                    'id_producto'  => $producto->id,
                    'precio_venta' => $producto->precio_venta,
                    'precio_lista' => $producto->precio_lista,
                    'estado'       => true,
                ]);
                $sku->opciones()->sync($combo);
                $creados++;
            }
        }

        return response()->json([
            'ok'      => true,
            'creados' => $creados,
            'payload' => $this->buildPayload($producto->fresh()),
        ]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function buildPayload(Producto $producto): array
    {
        $tipos = $producto->variacionTipos()
            ->with('opciones')
            ->ordenados()
            ->get();

        $skus = $producto->variacionSkus()
            ->with('opciones')
            ->orderBy('id')
            ->get();

        return [
            'tipos' => $tipos->map(fn($t) => $this->formatTipo($t)),
            'skus'  => $skus->map(fn($s) => $this->formatSku($s)),
        ];
    }

    private function formatTipo(VariacionTipo $tipo): array
    {
        return [
            'id'      => $tipo->id,
            'nombre'  => $tipo->nombre,
            'orden'   => $tipo->orden,
            'opciones'=> $tipo->opciones->map(fn($o) => $this->formatOpcion($o)),
        ];
    }

    private function formatOpcion(VariacionOpcion $opcion): array
    {
        return [
            'id'     => $opcion->id,
            'nombre' => $opcion->nombre,
            'imagen' => $opcion->imagen ? Storage::disk('public')->url($sku->imagen) : null,
            'orden'  => $opcion->orden,
        ];
    }

    private function formatSku(VariacionSku $sku): array
    {
        return [
            'id'           => $sku->id,
            'label'        => $sku->label,
            'precio_venta' => $sku->precio_venta,
            'precio_lista' => $sku->precio_lista,
            'estado'       => $sku->estado,
            'notas'        => $sku->notas,
            'imagen'       => $sku->imagen ? Storage::disk('public')->url($sku->imagen) : null,
            'opciones'     => $sku->opciones->pluck('id'),
        ];
    }

    private function limpiarSkusHuerfanos(Producto $producto): void
    {
        $totalTipos = $producto->variacionTipos()->count();

        foreach ($producto->variacionSkus()->with('opciones')->get() as $sku) {
            $tiposCubiertos = $sku->opciones
                ->map(fn($o) => $o->id_tipo)
                ->unique()
                ->count();

            if ($tiposCubiertos < $totalTipos) {
                if ($sku->imagen) {
                    Storage::disk('public')->delete($sku->imagen);
                }
                $sku->delete();
            }
        }
    }

    private function cartesiano(array $sets): array
    {
        $result = [[]];

        foreach ($sets as $set) {
            $temp = [];
            foreach ($result as $existing) {
                foreach ($set as $item) {
                    $temp[] = array_merge($existing, [$item]);
                }
            }
            $result = $temp;
        }

        return $result;
    }

    private function autorizarTipo(VariacionTipo $tipo, Producto $producto): void
    {
        abort_if($tipo->id_producto !== $producto->id, 403);
    }

    private function autorizarOpcion(VariacionOpcion $opcion, Producto $producto): void
    {
        abort_if($opcion->tipo->id_producto !== $producto->id, 403);
    }

    private function autorizarSku(VariacionSku $sku, Producto $producto): void
    {
        abort_if($sku->id_producto !== $producto->id, 403);
    }

    private function validarOpcionesDelProducto(array $opcionIds, Producto $producto): void
    {
        $tiposDelProducto = $producto->variacionTipos()->pluck('id');
        $count = VariacionOpcion::whereIn('id', $opcionIds)
            ->whereIn('id_tipo', $tiposDelProducto)
            ->count();

        abort_if($count !== count($opcionIds), 422);
    }

    private function procesarImagen($file, string $carpeta): string
    {
        $manager  = new ImageManager(new Driver());
        $image    = $manager->decode($file);

        if ($image->width() > $image->height()) {
            $image->scaleDown(width: 1200);
        } else {
            $image->scaleDown(height: 1200);
        }

        $filename = $carpeta . '/' . Str::uuid() . '.webp';
        $encoded  = $image->encode(new WebpEncoder(quality: 85));
        Storage::disk('public')->put($filename, $encoded);

        return $filename;
    }
}