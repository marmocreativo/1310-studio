<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminPaginasController;
use App\Http\Controllers\PaginasController;
use App\Http\Controllers\AdminCategoriasController;
use App\Http\Controllers\CategoriasController;


// Rutas admin
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('paginas', AdminPaginasController::class);
        Route::resource('categorias', AdminCategoriasController::class);
    });

// Rutas públicas
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/nuestro-estudio', [HomeController::class, 'estudio'])->name('estudio');
Route::get('/soluciones-empresariales', [HomeController::class, 'empresas'])->name('empresas');
// Páginas
Route::get('/paginas', [PaginasController::class, 'index'])->name('paginas.index');
Route::get('/paginas/{slug}', [PaginasController::class, 'show'])->name('paginas.show');
// Categorías
Route::get('/categorias', [CategoriasController::class, 'index'])->name('categorias.index');
Route::get('/categorias/{slug}', [CategoriasController::class, 'show'])->name('categorias.show');

require __DIR__.'/settings.php';