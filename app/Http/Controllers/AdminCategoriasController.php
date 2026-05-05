<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Format;
use Intervention\Image\Drivers\Gd\Driver;

class AdminCategoriasController extends Controller
{
    public function index(Request $request)
    {
        $query = Categoria::with('padre');

        if ($request->filled('padre')) {
            $query->where('id_padre', $request->padre);
        }

        if ($request->filled('busqueda')) {
            $query->where('titulo', 'like', '%' . $request->busqueda . '%');
        }

        $categorias = $query->latest()->paginate(15)->withQueryString();
        $padres = Categoria::raiz()->orderBy('titulo')->get();

        return view('pages.admin.categorias.index', compact('categorias', 'padres'));
    }

    public function create()
    {
        $padres = Categoria::raiz()->orderBy('titulo')->get();
        return view('pages.admin.categorias.create', compact('padres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo'   => 'required|string|max:255',
            'resumen'  => 'nullable|string',
            'id_padre' => 'nullable|exists:categorias,id',
            'imagen'   => 'nullable|image|max:5120',
            'estado'   => 'required|in:publicado,borrador',
        ]);

        $validated['slug'] = $this->generarSlug(Str::slug($validated['titulo']));

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $this->procesarImagen($request->file('imagen'));
        }

        Categoria::create($validated);

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function show(Categoria $categoria)
    {
        // Por el momento vacío — aquí irán los productos
        return view('pages.admin.categorias.show', compact('categoria'));
    }

    public function edit(Categoria $categoria)
    {
        // Excluir la categoría actual y sus hijos para evitar ciclos
        $padres = Categoria::raiz()
            ->where('id', '!=', $categoria->id)
            ->orderBy('titulo')
            ->get();

        return view('pages.admin.categorias.edit', compact('categoria', 'padres'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $validated = $request->validate([
            'titulo'   => 'required|string|max:255',
            'resumen'  => 'nullable|string',
            'id_padre' => 'nullable|exists:categorias,id',
            'imagen'   => 'nullable|image|max:5120',
            'estado'   => 'required|in:publicado,borrador',
        ]);

        // Evitar que una categoría sea su propio padre
        if ($validated['id_padre'] == $categoria->id) {
            $validated['id_padre'] = null;
        }

        $validated['slug'] = $this->generarSlug(Str::slug($validated['titulo']), $categoria->id);

        if ($request->hasFile('imagen')) {
            if ($categoria->imagen) {
                Storage::disk('public')->delete($categoria->imagen);
            }
            $validated['imagen'] = $this->procesarImagen($request->file('imagen'));
        }

        $categoria->update($validated);

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Categoria $categoria)
    {
        if ($categoria->imagen) {
            Storage::disk('public')->delete($categoria->imagen);
        }

        $categoria->delete();

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
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

    private function procesarImagen($file): string
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->decode($file);

        if ($image->width() > $image->height()) {
            $image->scaleDown(width: 1200);
        } else {
            $image->scaleDown(height: 1200);
        }

        $filename = 'categorias/' . Str::uuid() . '.webp';
        $encoded = $image->encode(new \Intervention\Image\Encoders\WebpEncoder(quality: 85));
        Storage::disk('public')->put($filename, $encoded);

        return $filename;
    }
}