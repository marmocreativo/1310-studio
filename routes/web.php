<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminPaginasController;
use App\Http\Controllers\PaginasController;
use App\Http\Controllers\AdminCategoriasController;
use App\Http\Controllers\AdminDirectorioFloralController;
use App\Http\Controllers\AdminTalleresController;
use App\Http\Controllers\AdminProductosController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\DirectorioFloralController;
use App\Http\Controllers\TalleresController;
use App\Http\Controllers\AdminVariacionesController;
use App\Http\Controllers\AdminVariacionesDefaultController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\AdminZonasEnvioController;
use App\Http\Controllers\AdminPedidosController;
use App\Http\Controllers\AdminSlidesController;
use App\Http\Controllers\DireccionesController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\PedidoPublicoController;
use App\Http\Controllers\AdminUsuariosController;
use App\Http\Controllers\AdminConfiguracionesController;



// Rutas admin
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('paginas', AdminPaginasController::class);
        Route::resource('categorias', AdminCategoriasController::class);

        // Slides Hero
        Route::post('slides/orden', [AdminSlidesController::class, 'orden'])->name('slides.orden');
        Route::resource('slides', AdminSlidesController::class);

        // Configuraciones
        Route::get('configuraciones', [AdminConfiguracionesController::class, 'index'])->name('configuraciones.index');
        Route::post('configuraciones', [AdminConfiguracionesController::class, 'store'])->name('configuraciones.store');
        Route::patch('configuraciones/{configuracion}', [AdminConfiguracionesController::class, 'update'])->name('configuraciones.update');
        Route::delete('configuraciones/{configuracion}', [AdminConfiguracionesController::class, 'destroy'])->name('configuraciones.destroy');

        // Usuarios
        Route::patch('usuarios/{usuario}/toggle-rol', [AdminUsuariosController::class, 'toggleRol'])->name('usuarios.toggle-rol');
        Route::resource('usuarios', AdminUsuariosController::class)->except(['create', 'store']);

        // Directorio Floral + Galería
        Route::post('directorio-floral/lote', [AdminDirectorioFloralController::class, 'lote'])->name('directorio-floral.lote');
        Route::resource('directorio-floral', AdminDirectorioFloralController::class);
        Route::post('directorio-floral/{flor}/galeria', [AdminDirectorioFloralController::class, 'galeriaStore'])->name('directorio-floral.galeria.store');
        Route::delete('directorio-floral/{flor}/galeria/{imagen}', [AdminDirectorioFloralController::class, 'galeriaDestroy'])->name('directorio-floral.galeria.destroy');
        Route::patch('directorio-floral/{flor}/galeria/{imagen}/orden', [AdminDirectorioFloralController::class, 'galeriaOrden'])->name('directorio-floral.galeria.orden');

        // Talleres
        
        Route::post('talleres/lote', [AdminTalleresController::class, 'lote'])->name('talleres.lote');
        Route::resource('talleres', AdminTalleresController::class)
            ->parameters(['talleres' => 'taller']);

        // Productos + Galería + Relaciones
        // Acciones en lote y toggle
        Route::post('productos/lote', [AdminProductosController::class, 'lote'])->name('productos.lote');
        Route::patch('productos/{producto}/toggle-destacado', [AdminProductosController::class, 'toggleDestacado'])->name('productos.toggle-destacado');
        Route::resource('productos', AdminProductosController::class);
        Route::post('productos/{producto}/galeria', [AdminProductosController::class, 'galeriaStore'])->name('productos.galeria.store');
        Route::delete('productos/{producto}/galeria/{imagen}', [AdminProductosController::class, 'galeriaDestroy'])->name('productos.galeria.destroy');
        Route::patch('productos/{producto}/galeria/orden', [AdminProductosController::class, 'galeriaOrden'])->name('productos.galeria.orden');
        Route::put('productos/{producto}/categorias', [AdminProductosController::class, 'syncCategorias'])->name('productos.categorias.sync');
        Route::put('productos/{producto}/flores', [AdminProductosController::class, 'syncFlores'])->name('productos.flores.sync');

        // Variaciones - Tipos y Opciones
        Route::prefix('productos/{producto}/variaciones')->name('productos.variaciones.')->group(function () {
            // Tipos
            Route::get('/', [AdminVariacionesController::class, 'index'])->name('index');
            Route::post('/tipos', [AdminVariacionesController::class, 'tipoStore'])->name('tipos.store');
            Route::patch('/tipos/{tipo}', [AdminVariacionesController::class, 'tipoUpdate'])->name('tipos.update');
            Route::delete('/tipos/{tipo}', [AdminVariacionesController::class, 'tipoDestroy'])->name('tipos.destroy');
            Route::patch('/tipos/{tipo}/orden', [AdminVariacionesController::class, 'tipoOrden'])->name('tipos.orden');

            // Opciones
            Route::post('/tipos/{tipo}/opciones', [AdminVariacionesController::class, 'opcionStore'])->name('opciones.store');
            Route::patch('/opciones/{opcion}', [AdminVariacionesController::class, 'opcionUpdate'])->name('opciones.update');
            Route::delete('/opciones/{opcion}', [AdminVariacionesController::class, 'opcionDestroy'])->name('opciones.destroy');

            // SKUs
            Route::post('/skus/generar', [AdminVariacionesController::class, 'skuGenerar'])->name('skus.generar');
            Route::post('/skus', [AdminVariacionesController::class, 'skuStore'])->name('skus.store');
            Route::patch('/skus/{sku}', [AdminVariacionesController::class, 'skuUpdate'])->name('skus.update');
            Route::delete('/skus/{sku}', [AdminVariacionesController::class, 'skuDestroy'])->name('skus.destroy');
            Route::post('/skus/{sku}/galeria', [AdminVariacionesController::class, 'skuGaleriaStore'])->name('skus.galeria.store');
            Route::delete('/skus/{sku}/galeria/{imagen}', [AdminVariacionesController::class, 'skuGaleriaDestroy'])->name('skus.galeria.destroy');
            Route::patch('/skus/{sku}/galeria/orden', [AdminVariacionesController::class, 'skuGaleriaOrden'])->name('skus.galeria.orden');
        });

        // Defaults (catálogo global)
        Route::prefix('variaciones-defaults')->name('variaciones-defaults.')->group(function () {
            Route::get('/', [AdminVariacionesDefaultController::class, 'index'])->name('index');
            Route::post('/tipos', [AdminVariacionesDefaultController::class, 'tipoStore'])->name('tipos.store');
            Route::patch('/tipos/{tipo}', [AdminVariacionesDefaultController::class, 'tipoUpdate'])->name('tipos.update');
            Route::delete('/tipos/{tipo}', [AdminVariacionesDefaultController::class, 'tipoDestroy'])->name('tipos.destroy');
            Route::post('/tipos/{tipo}/opciones', [AdminVariacionesDefaultController::class, 'opcionStore'])->name('opciones.store');
            Route::patch('/opciones/{opcion}', [AdminVariacionesDefaultController::class, 'opcionUpdate'])->name('opciones.update');
            Route::delete('/opciones/{opcion}', [AdminVariacionesDefaultController::class, 'opcionDestroy'])->name('opciones.destroy');
        });

        // Zonas de envío
        Route::resource('zonas-envio', AdminZonasEnvioController::class)
            ->parameters(['zonas-envio' => 'zona']);

        // Pedidos
        Route::resource('pedidos', AdminPedidosController::class)
            ->only(['index', 'show', 'destroy']);
        Route::patch('pedidos/{pedido}/estado', [AdminPedidosController::class, 'cambiarEstado'])->name('pedidos.estado');
    });

    // Cuenta — requiere autenticación
    Route::middleware(['auth', 'verified'])->prefix('cuenta')->name('cuenta.')->group(function () {
        Route::get('/',                                          [CuentaController::class, 'dashboard'])->name('dashboard');
        Route::get('/pedidos',                                   [CuentaController::class, 'pedidos'])->name('pedidos');
        Route::get('/pedidos/{pedido:numero}',                   [CuentaController::class, 'pedidoShow'])->name('pedidos.show');
        Route::post('/pedidos/{pedido:numero}/cancelar',         [CuentaController::class, 'pedidoCancelar'])->name('pedidos.cancelar');
        Route::post('/pedidos/{pedido:numero}/cambio-fecha',     [CuentaController::class, 'pedidoCambioFecha'])->name('pedidos.cambio-fecha');
        Route::post('/pedidos/{pedido:numero}/reporte',          [CuentaController::class, 'pedidoReporte'])->name('pedidos.reporte');
        Route::get('/direcciones',                               [CuentaController::class, 'direcciones'])->name('direcciones');
        Route::post('/direcciones',                              [CuentaController::class, 'direccionStore'])->name('direcciones.store');
        Route::put('/direcciones/{direccion}',                   [CuentaController::class, 'direccionUpdate'])->name('direcciones.update');
        Route::delete('/direcciones/{direccion}',                [CuentaController::class, 'direccionDestroy'])->name('direcciones.destroy');
        Route::patch('/direcciones/{direccion}/predeterminada',  [CuentaController::class, 'direccionPredeterminada'])->name('direcciones.predeterminada');
        Route::get('/perfil',                                    [CuentaController::class, 'perfil'])->name('perfil');
        Route::put('/perfil',                                    [CuentaController::class, 'perfilUpdate'])->name('perfil.update');
        Route::get('/password',                                  [CuentaController::class, 'password'])->name('password');
        Route::put('/password',                                  [CuentaController::class, 'passwordUpdate'])->name('password.update');
        Route::get('/fechas',                    [CuentaController::class, 'fechas'])->name('fechas');
        Route::post('/fechas',                   [CuentaController::class, 'fechaStore'])->name('fechas.store');
        Route::delete('/fechas/{fecha}',         [CuentaController::class, 'fechaDestroy'])->name('fechas.destroy');
        
    });

    // Pedido público (invitados)
    Route::get('/mi-pedido',  [PedidoPublicoController::class, 'buscar'])->name('pedido-publico.buscar');
    Route::post('/mi-pedido', [PedidoPublicoController::class, 'show'])->name('pedido-publico.show');

    // Rutas públicas
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/visitanos', [HomeController::class, 'visitanos'])->name('visitanos');
    Route::get('/eventos', [EventosController::class, 'index'])->name('eventos');

    // Páginas de contenido
    Route::get('/paginas', [PaginasController::class, 'index'])->name('paginas.index');
    Route::get('/paginas/{slug}', [PaginasController::class, 'show'])->name('paginas.show');

    // Categorías y productos
    Route::get('/categorias', [CategoriasController::class, 'index'])->name('categorias.index');
    Route::get('/categorias/{categoria:slug}', [CategoriasController::class, 'show'])->name('categorias.show');
    Route::get('/productos/{producto:slug}', [ProductosController::class, 'show'])->name('productos.show');


    // Directorio floral
    Route::get('/directorio-floral', [DirectorioFloralController::class, 'index'])->name('directorio-floral.index');
    Route::get('/directorio-floral/{flor:slug}', [DirectorioFloralController::class, 'show'])->name('directorio-floral.show');

    // Talleres
    Route::get('/talleres', [TalleresController::class, 'index'])->name('talleres.index');
    Route::get('/talleres/{taller:slug}', [TalleresController::class, 'show'])->name('talleres.show');

    // Carrito
    Route::prefix('carrito')->name('carrito.')->group(function () {
        Route::get('/', [CarritoController::class, 'index'])->name('index');
        Route::post('/agregar', [CarritoController::class, 'agregar'])->name('agregar');
        Route::patch('/actualizar/{item}', [CarritoController::class, 'actualizar'])->name('actualizar');
        Route::delete('/eliminar/{item}', [CarritoController::class, 'eliminar'])->name('eliminar');
        Route::delete('/vaciar', [CarritoController::class, 'vaciar'])->name('vaciar');
    });

    // Checkout
    Route::get('/checkout/acceso', [CheckoutController::class, 'acceso'])->name('checkout.acceso');
    Route::post('/checkout/invitado', [CheckoutController::class, 'continuarComoInvitado'])->name('checkout.invitado');
    Route::get('/checkout/paso/1', [CheckoutController::class, 'paso1'])->name('checkout.paso1');
    Route::post('/checkout/paso/1', [CheckoutController::class, 'paso1Store'])->name('checkout.paso1.store');
    Route::get('/checkout/paso/2', [CheckoutController::class, 'paso2'])->name('checkout.paso2');
    Route::post('/checkout/paso/2', [CheckoutController::class, 'paso2Store'])->name('checkout.paso2.store');
    Route::get('/checkout/paso/3', [CheckoutController::class, 'paso3'])->name('checkout.paso3');
    Route::post('/checkout/paso/3', [CheckoutController::class, 'paso3Store'])->name('checkout.paso3.store');
    Route::get('/checkout/confirmacion/{pedido:numero}', [CheckoutController::class, 'confirmacion'])->name('checkout.confirmacion');


    // Mercado Pago
    Route::prefix('pagos')->name('pagos.')->group(function () {
        Route::post('/procesar-pago', [PagoController::class, 'procesarPagoBrick'])->name('procesar-pago');
        Route::post('/procesar-tarjeta', [PagoController::class, 'procesarTarjeta'])->name('procesar-tarjeta');
        Route::get('/pagar/{numero}', [PagoController::class, 'pagar'])->name('pagar');
        Route::post('/crear-preferencia', [PagoController::class, 'crearPreferencia'])->name('preferencia');
        Route::get('/exito', [PagoController::class, 'exito'])->name('exito');
        Route::get('/fallo', [PagoController::class, 'fallo'])->name('fallo');
        Route::get('/pendiente', [PagoController::class, 'pendiente'])->name('pendiente');
        Route::post('/webhook', [PagoController::class, 'webhook'])->name('webhook')->withoutMiddleware(['web']);
    });

require __DIR__.'/settings.php';