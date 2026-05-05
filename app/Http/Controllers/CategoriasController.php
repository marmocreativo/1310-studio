<?php
    namespace App\Http\Controllers;

    use App\Models\Categoria;
    use App\Models\Producto;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\DB;

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

            $productos = Producto::activos()
                ->whereIn('id',
                    DB::table('productos_categorias')
                        ->where('id_categoria', $categoria->id)
                        ->pluck('id_producto')
                )
                ->with('galeria')
                ->paginate(12);
            
            return view('pages.public.categorias.show', compact('categoria', 'productos'));
        }
    }