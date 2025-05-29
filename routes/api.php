<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiAlmacenController;
use App\Http\Controllers\Api\ApiProductoController;
use App\Http\Controllers\Web\AlmacenController;

// CRUD PRODUCTOS
Route::get('/productos', [ApiProductoController::class, 'index'])->name('productos.index');
Route::get('/productos/{id}', [ApiProductoController::class, 'show'])->name('productos.show');
Route::post('/productos', [ApiProductoController::class, 'store'])->name('productos.store');
Route::patch('/productos/{id}', [ApiProductoController::class, 'update'])->name('productos.update');
Route::delete('/productos', [ApiProductoController::class, 'destroy'])->name('productos.destroy');


// productos de los almacenes del usuario autenticado
Route::middleware('auth:sanctum')->get('/productos_user', [ApiProductoController::class, 'productos_user']);

Route::middleware('auth:sanctum')->get('/almacenes', [ApiAlmacenController::class, 'index']);


// CRUD ALMACENES   
Route::middleware('auth:sanctum')->group(function(){
    Route::get('/almacenes', [ApiAlmacenController::class, 'index'])->name('almacenes.index');
});
Route::middleware('auth:sanctum')->group(function(){
    Route::get('/almacenes/{id}', [ApiAlmacenController::class, 'show'])->name('almacenes.show');
});
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/almacenes', [AlmacenController::class, 'store'])->name('almacenes.store');
});

// TOKENS
Route::post('/login', [ApiAuthController::class, 'login'])->name('login.api');