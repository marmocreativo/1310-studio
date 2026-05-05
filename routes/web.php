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



// Rutas admin
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('paginas', AdminPaginasController::class);
        Route::resource('categorias', AdminCategoriasController::class);

        // Directorio Floral + Galería
        Route::resource('directorio-floral', AdminDirectorioFloralController::class);
        Route::post('directorio-floral/{flor}/galeria', [AdminDirectorioFloralController::class, 'galeriaStore'])->name('directorio-floral.galeria.store');
        Route::delete('directorio-floral/{flor}/galeria/{imagen}', [AdminDirectorioFloralController::class, 'galeriaDestroy'])->name('directorio-floral.galeria.destroy');
        Route::patch('directorio-floral/{flor}/galeria/{imagen}/orden', [AdminDirectorioFloralController::class, 'galeriaOrden'])->name('directorio-floral.galeria.orden');

        // Talleres
        Route::resource('talleres', AdminTalleresController::class)
            ->parameters(['talleres' => 'taller']);

        // Productos + Galería + Relaciones
        Route::resource('productos', AdminProductosController::class);
        Route::post('productos/{producto}/galeria', [AdminProductosController::class, 'galeriaStore'])->name('productos.galeria.store');
        Route::delete('productos/{producto}/galeria/{imagen}', [AdminProductosController::class, 'galeriaDestroy'])->name('productos.galeria.destroy');
        Route::patch('productos/{producto}/galeria/{imagen}/orden', [AdminProductosController::class, 'galeriaOrden'])->name('productos.galeria.orden');
        Route::put('productos/{producto}/categorias', [AdminProductosController::class, 'syncCategorias'])->name('productos.categorias.sync');
        Route::put('productos/{producto}/flores', [AdminProductosController::class, 'syncFlores'])->name('productos.flores.sync');
    });

// Rutas públicas
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

require __DIR__.'/settings.php';