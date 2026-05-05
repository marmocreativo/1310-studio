<?php

namespace App\Http\Controllers;

use App\Models\DirectorioFloral;
use App\Models\GaleriaFlor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Illuminate\Support\Str;

class AdminDirectorioFloralController extends Controller
{
    public function index()
    {
        $flores = DirectorioFloral::ordenados()->paginate(20);
        return view('pages.admin.directorio-floral.index', compact('flores'));
    }

    public function create()
    {
        return view('pages.admin.directorio-floral.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'categoria'   => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'contenido'   => 'nullable|string',
            'imagen'      => 'nullable|image|max:2048',
            'estado'      => 'boolean',
            'orden'       => 'integer|min:0',
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $this->procesarImagen($request->file('imagen'), 'directorio-floral');
        }

        $data['slug'] = $this->generarSlug(Str::slug($data['nombre']));

        DirectorioFloral::create($data);

        return redirect()->route('admin.directorio-floral.index')
                         ->with('success', 'Flor creada correctamente.');
    }

    public function show(DirectorioFloral $directorioFloral)
    {
        $directorioFloral->load('galeria');
        return view('pages.admin.directorio-floral.show', compact('directorioFloral'));
    }

    public function edit(DirectorioFloral $directorioFloral)
    {
        $directorioFloral->load('galeria');
        return view('pages.admin.directorio-floral.edit', compact('directorioFloral'));
    }

    public function update(Request $request, DirectorioFloral $directorioFloral)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'categoria'   => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'contenido'   => 'nullable|string',
            'imagen'      => 'nullable|image|max:2048',
            'estado'      => 'boolean',
            'orden'       => 'integer|min:0',
        ]);

        if ($request->hasFile('imagen')) {
            if ($directorioFloral->imagen) {
                Storage::disk('public')->delete($directorioFloral->imagen);
            }
            $data['imagen'] = $this->procesarImagen($request->file('imagen'), 'directorio-floral');
        }

        $data['slug'] = $this->generarSlug(Str::slug($data['nombre']), $directorioFloral->id);

        $directorioFloral->update($data);

        return redirect()->route('admin.directorio-floral.edit', $directorioFloral)
                         ->with('success', 'Flor actualizada correctamente.');
    }

    public function destroy(DirectorioFloral $directorioFloral)
    {
        if ($directorioFloral->imagen) {
            Storage::disk('public')->delete($directorioFloral->imagen);
        }

        foreach ($directorioFloral->galeria as $img) {
            Storage::disk('public')->delete($img->imagen);
        }

        $directorioFloral->delete();

        return redirect()->route('admin.directorio-floral.index')
                         ->with('success', 'Flor eliminada correctamente.');
    }

    // ─── Galería ──────────────────────────────────

    public function galeriaStore(Request $request, DirectorioFloral $flor)
    {
        $request->validate([
            'imagenes'   => 'required|array',
            'imagenes.*' => 'image|max:2048',
        ]);

        $orden = $flor->galeria()->max('orden') ?? 0;

        foreach ($request->file('imagenes') as $file) {
            $orden++;
            GaleriaFlor::create([
                'id_flor' => $flor->id,
                'imagen' => $this->procesarImagen($file, 'directorio-floral/galeria'),
                'estado'  => true,
                'orden'   => $orden,
            ]);
        }

        return back()->with('success', 'Imágenes agregadas correctamente.');
    }

    public function galeriaDestroy(DirectorioFloral $flor, GaleriaFlor $imagen)
    {
        Storage::disk('public')->delete($imagen->imagen);
        $imagen->delete();

        return back()->with('success', 'Imagen eliminada.');
    }

    public function galeriaOrden(Request $request, DirectorioFloral $flor, GaleriaFlor $imagen)
    {
        $request->validate(['orden' => 'required|integer|min:0']);
        $imagen->update(['orden' => $request->orden]);

        return response()->json(['ok' => true]);
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
        $query    = DirectorioFloral::where('slug', 'like', "{$slug}%");

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $count = $query->count();

        return $count > 0 ? "{$original}-{$count}" : $slug;
    }
}