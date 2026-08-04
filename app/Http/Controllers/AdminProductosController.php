<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\GaleriaProducto;
use App\Models\Categoria;
use App\Models\DirectorioFloral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Illuminate\Support\Str;

class AdminProductosController extends Controller
{

    public function index(Request $request)
    {
        $query = Producto::with(['categorias', 'galeria']);

        if ($request->filled('busqueda')) {
            $query->where('nombre', 'like', '%' . $request->busqueda . '%');
        }

        if ($request->filled('categoria')) {
            $query->whereHas('categorias', fn($q) => $q->where('categorias.id', $request->categoria));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado === 'activo');
        }

        if ($request->filled('destacado')) {
            $query->where('destacado', true);
        }

        $productos  = $query->orderBy('nombre')->paginate(20)->withQueryString();
        $categorias = Categoria::orderBy('titulo')->get();

        return view('pages.admin.productos.index', compact('productos', 'categorias'));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('titulo')->get();
        $flores     = DirectorioFloral::activos()->ordenados()->get();

        return view('pages.admin.productos.create', compact('categorias', 'flores'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'nullable|string',
            'detalles'      => 'nullable|string',
            'destacado'     => 'boolean',
            'precio_lista'  => 'nullable|numeric|min:0',
            'precio_venta'  => 'required|numeric|min:0',
            'estado'        => 'boolean',
            'categorias'    => 'nullable|array',
            'categorias.*'  => 'exists:categorias,id',
            'flores'        => 'nullable|array',
            'flores.*'      => 'exists:directorio_floral,id',
        ]);

        $data['slug'] = $this->generarSlug(Str::slug($data['nombre']));

        $producto = Producto::create($data);

        if (!empty($data['categorias'])) {
            $producto->categorias()->sync($data['categorias']);
        }

        if (!empty($data['flores'])) {
            $producto->flores()->sync($data['flores']);
        }

        return redirect()->route('admin.productos.edit', $producto)
                         ->with('success', 'Producto creado. Ahora puedes agregar imágenes.');
    }

    public function show(Producto $producto)
    {
        $producto->load(['galeria', 'categorias', 'flores']);
        return view('pages.admin.productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $producto->load(['galeria', 'categorias', 'flores']);
        $categorias = Categoria::orderBy('titulo')->get();
        $flores     = DirectorioFloral::activos()->ordenados()->get();

        return view('pages.admin.productos.edit', compact('producto', 'categorias', 'flores'));
    }

    public function update(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'nullable|string',
            'detalles'      => 'nullable|string',
            'destacado'     => 'boolean',
            'precio_lista'  => 'nullable|numeric|min:0',
            'precio_venta'  => 'required|numeric|min:0',
            'estado'        => 'boolean',
            'categorias'    => 'nullable|array',
            'categorias.*'  => 'exists:categorias,id',
            'flores'        => 'nullable|array',
            'flores.*'      => 'exists:directorio_floral,id',
        ]);

        $data['slug'] = $this->generarSlug(Str::slug($data['nombre']), $producto->id);
        
        $producto->update($data);

        $producto->categorias()->sync($data['categorias'] ?? []);
        $producto->flores()->sync($data['flores'] ?? []);

        return redirect()->route('admin.productos.edit', $producto)
                         ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        foreach ($producto->galeria as $img) {
            Storage::disk('public')->delete($img->imagen);
        }

        $producto->delete();

        return redirect()->route('admin.productos.index')
                         ->with('success', 'Producto eliminado correctamente.');
    }

    // ─── Galería ──────────────────────────────────

    public function galeriaStore(Request $request, Producto $producto)
    {
        $request->validate([
            'imagenes'   => 'required|array',
            'imagenes.*' => 'image|max:2048',
        ]);

        $orden = $producto->galeria()->max('orden') ?? 0;

        foreach ($request->file('imagenes') as $file) {
            $orden++;
            GaleriaProducto::create([
                'id_producto' => $producto->id,
                'imagen' => $this->procesarImagen($file, 'productos/galeria'),
                'estado'      => true,
                'orden'       => $orden,
            ]);
        }

        return response()->json([
            'ok'      => true,
            'message' => 'Imágenes agregadas correctamente.',
            'galeria' => $producto->galeria()->get()->map(fn($img) => [
                'id'     => $img->id,
                'url' => Storage::disk('public')->url($img->imagen),
                'orden'  => $img->orden,
                'delete' => route('admin.productos.galeria.destroy', [$producto, $img]),
            ]),
        ]);
    }

    public function galeriaDestroy(Producto $producto, GaleriaProducto $imagen)
    {
        Storage::disk('public')->delete($imagen->imagen);
        $imagen->delete();

        return response()->json(['ok' => true]);
    }

    public function galeriaOrden(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'orden'   => 'required|array',
            'orden.*' => 'integer|exists:galeria_productos,id',
        ]);

        foreach ($data['orden'] as $index => $id) {
            GaleriaProducto::where('id', $id)->where('id_producto', $producto->id)->update(['orden' => $index]);
        }

        return response()->json(['ok' => true]);
    }

    // ─── Relaciones ───────────────────────────────

    public function syncCategorias(Request $request, Producto $producto)
    {
        $request->validate([
            'categorias'   => 'nullable|array',
            'categorias.*' => 'exists:categorias,id',
        ]);

        $producto->categorias()->sync($request->categorias ?? []);

        return back()->with('success', 'Categorías actualizadas.');
    }

    public function syncFlores(Request $request, Producto $producto)
    {
        $request->validate([
            'flores'   => 'nullable|array',
            'flores.*' => 'exists:directorio_floral,id',
        ]);

        $producto->flores()->sync($request->flores ?? []);

        return back()->with('success', 'Flores actualizadas.');
    }

    public function toggleDestacado(Producto $producto)
{
    $producto->update(['destacado' => ! $producto->destacado]);

    return response()->json([
        'destacado' => $producto->destacado,
    ]);
}

    public function lote(Request $request)
    {
        $request->validate([
            'ids'        => 'required|array',
            'ids.*'      => 'exists:productos,id',
            'accion'     => 'required|in:activar,desactivar,destacar,no-destacar,categoria',
            'id_categoria' => 'nullable|exists:categorias,id',
        ]);

        $productos = Producto::whereIn('id', $request->ids);

        match($request->accion) {
            'activar'      => $productos->update(['estado' => true]),
            'desactivar'   => $productos->update(['estado' => false]),
            'destacar'     => $productos->update(['destacado' => true]),
            'no-destacar'  => $productos->update(['destacado' => false]),
            'categoria'    => $productos->get()->each(
                fn($p) => $p->categorias()->syncWithoutDetaching([$request->id_categoria])
            ),
        };

        return redirect()->back()->with('success', 'Acción aplicada correctamente.');
    }

    private function procesarImagen($file, string $carpeta): string
    {
        $manager = new ImageManager(new Driver());
        $image   = $manager->decode($file);

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

    private function generarSlug(string $slug, ?int $excludeId = null): string
    {
        $original = $slug;
        $query    = Producto::where('slug', 'like', "{$slug}%");

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $count = $query->count();

        return $count > 0 ? "{$original}-{$count}" : $slug;
    }
}