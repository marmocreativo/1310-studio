<?php

namespace App\Http\Controllers;

use App\Models\Taller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Illuminate\Support\Str;

class AdminTalleresController extends Controller
{
    public function index()
    {
        $talleres = Taller::orderBy('fecha', 'desc')->paginate(20);
        return view('pages.admin.talleres.index', compact('talleres'));
    }

    public function create()
    {
        return view('pages.admin.talleres.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'   => 'required|string|max:255',
            'detalles' => 'nullable|string',
            'imagen'   => 'nullable|image|max:2048',
            'fecha'    => 'nullable|date',
            'estado'   => 'boolean',
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $this->procesarImagen($request->file('imagen'), 'talleres');
        }

        $data['slug'] = $this->generarSlug(Str::slug($data['nombre']));

        Taller::create($data);

        return redirect()->route('admin.talleres.index')
                         ->with('success', 'Taller creado correctamente.');
    }

    public function show(Taller $taller)
    {
        return view('pages.admin.talleres.show', compact('taller'));
    }

    public function edit(Taller $taller)
    {
        return view('pages.admin.talleres.edit', compact('taller'));
    }

    public function update(Request $request, Taller $taller)
    {
        $data = $request->validate([
            'nombre'   => 'required|string|max:255',
            'detalles' => 'nullable|string',
            'imagen'   => 'nullable|image|max:2048',
            'fecha'    => 'nullable|date',
            'estado'   => 'boolean',
        ]);

        if ($request->hasFile('imagen')) {
            if ($taller->imagen) {
                Storage::disk('public')->delete($taller->imagen);
            }
            $data['imagen'] = $this->procesarImagen($request->file('imagen'), 'talleres');
        }

        $data['slug'] = $this->generarSlug(Str::slug($data['nombre']), $taller->id);

        $taller->update($data);

        return redirect()->route('admin.talleres.edit', $taller)
                         ->with('success', 'Taller actualizado correctamente.');
    }

    public function destroy(Taller $taller)
    {
        if ($taller->imagen) {
            Storage::disk('public')->delete($taller->imagen);
        }

        $taller->delete();

        return redirect()->route('admin.talleres.index')
                         ->with('success', 'Taller eliminado correctamente.');
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
        $query    = Taller::where('slug', 'like', "{$slug}%");

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $count = $query->count();

        return $count > 0 ? "{$original}-{$count}" : $slug;
    }
}