<?php
    namespace App\Http\Controllers;

    use App\Models\Categoria;
    use App\Models\Producto;
    use Illuminate\Support\Str;

    class CategoriasController extends Controller
    {
        public function index()
        {
            $categorias = Categoria::raiz()
                ->where('estado', 'publicado')
                ->with('hijos')
                ->orderBy('titulo')
                ->get();

            return view('pages.public.categorias.index', compact('categorias'));
        }

        public function show(Categoria $categoria)
        {
            $categoria->load('hijos');

            $productos = $categoria->productos()
                ->where('estado', true)
                ->with('galeria')
                ->orderByRaw('productos_categorias.orden IS NULL, productos_categorias.orden ASC')
                ->orderBy('nombre')
                ->paginate(12);

            return view('pages.public.categorias.show', compact('categoria', 'productos'));
        }
    }