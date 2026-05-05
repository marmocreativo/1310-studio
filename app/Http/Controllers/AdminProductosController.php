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
    public function index()
    {
        $productos = Producto::with('categorias')
                             ->orderBy('nombre')
                             ->paginate(20);

        return view('pages.admin.productos.index', compact('productos'));
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

        return back()->with('success', 'Imágenes agregadas correctamente.');
    }

    public function galeriaDestroy(Producto $producto, GaleriaProducto $imagen)
    {
        Storage::disk('public')->delete($imagen->imagen);
        $imagen->delete();

        return back()->with('success', 'Imagen eliminada.');
    }

    public function galeriaOrden(Request $request, Producto $producto, GaleriaProducto $imagen)
    {
        $request->validate(['orden' => 'required|integer|min:0']);
        $imagen->update(['orden' => $request->orden]);

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