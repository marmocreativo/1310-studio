<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pagina;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AdminPaginasController extends Controller
{
    public function index()
    {
        $paginas = Pagina::latest()->paginate(15);
        return view('pages.admin.paginas.index', compact('paginas'));
    }

    public function create()
    {
        return view('pages.admin.paginas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo'    => 'required|string|max:255',
            'categoria' => 'required|in:general,legal',
            'resumen'   => 'nullable|string',
            'contenido' => 'nullable|string',
            'imagen'    => 'nullable|image|max:5120',
            'estado'    => 'required|in:publicado,borrador',
        ]);

        $validated['slug'] = Str::slug($validated['titulo']);

        // Evitar slugs duplicados
        $slug = $validated['slug'];
        $count = Pagina::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $validated['slug'] = "{$slug}-{$count}";
        }

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $this->procesarImagen($request->file('imagen'));
        }

        Pagina::create($validated);

        return redirect()->route('admin.paginas.index')
            ->with('success', 'Página creada correctamente.');
    }

    public function edit(Pagina $pagina)
    {
        return view('pages.admin.paginas.edit', compact('pagina'));
    }

    public function update(Request $request, Pagina $pagina)
    {
        $validated = $request->validate([
            'titulo'    => 'required|string|max:255',
            'categoria' => 'required|in:general,legal',
            'resumen'   => 'nullable|string',
            'contenido' => 'nullable|string',
            'imagen'    => 'nullable|image|max:5120',
            'estado'    => 'required|in:publicado,borrador',
        ]);

        $validated['slug'] = Str::slug($validated['titulo']);

        // Evitar slugs duplicados excluyendo la actual
        $slug = $validated['slug'];
        $count = Pagina::where('slug', 'like', "{$slug}%")
            ->where('id', '!=', $pagina->id)
            ->count();
        if ($count > 0) {
            $validated['slug'] = "{$slug}-{$count}";
        }

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior
            if ($pagina->imagen) {
                Storage::disk('public')->delete($pagina->imagen);
            }
            $validated['imagen'] = $this->procesarImagen($request->file('imagen'));
        }

        $pagina->update($validated);

        return redirect()->route('admin.paginas.index')
            ->with('success', 'Página actualizada correctamente.');
    }

    public function destroy(Pagina $pagina)
    {
        if ($pagina->imagen) {
            Storage::disk('public')->delete($pagina->imagen);
        }

        $pagina->delete();

        return redirect()->route('admin.paginas.index')
            ->with('success', 'Página eliminada correctamente.');
    }

    private function procesarImagen($file): string
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->decode($file);

        if ($image->width() > $image->height()) {
            $image->scaleDown(width: 1200);
        } else {
            $image->scaleDown(height: 1200);
        }

        $filename = 'paginas/' . Str::uuid() . '.webp';
        $encoded = $image->encode(new \Intervention\Image\Encoders\WebpEncoder(quality: 85));
        Storage::disk('public')->put($filename, $encoded);

        return $filename;
    }
}