<?php

use Inertia\Inertia;
use App\Models\Proveedor;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\CategoriasController;
use App\Http\Controllers\Web\AlmacenController;
use App\Http\Controllers\Web\ClienteController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\ProductoController;
use App\Http\Controllers\Web\EntidadesController;
use App\Http\Controllers\Web\InventarioController;
use App\Http\Controllers\Web\SendEmaillController;
use App\Http\Controllers\Web\ProveedoresController;
use App\Http\Controllers\Web\DetallesVentaController;
use App\Http\Controllers\Web\DetallesCompraController;

// EDIT PROFILE
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// LOGIN
Route::get('/', function () {
    return Inertia::render('Auth/Login');
});

// NAV
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [InventarioController::class ,'dashboard'])->name('dashboard.index');
    Route::get('/inventario', [AlmacenController::class ,'index'])->name('inventario.index');
    Route::get('/pedidos', [DetallesCompraController::class ,'index'])->name('pedidos.index');
    Route::get('/ventas', [DetallesVentaController::class ,'index'])->name('ventas.index');
    Route::get('/detalles', [ProductoController::class ,'defaultIndex'])->name('producto.default');
    Route::get('/detalles/{id}', [ProductoController::class ,'index'])->name('producto.index');
    Route::get('/proveedores', [ProveedoresController::class ,'index'])->name('proveedores.index'); 
    Route::get('/entidades', [EntidadesController::class ,'index'])->name('entidades.index'); 
});

// CRUD DE ALMACENES
Route::middleware('auth')->group(function () {
    Route::delete('/inventario', [AlmacenController::class ,'delete'])->name('inventario.delete');
    Route::post('/inventario', [AlmacenController::class ,'store'])->name('inventario.store');
    Route::patch('inventario/almacen', [AlmacenController::class ,'update'])->name('almacen.patch');
    
    Route::put('inventario/producto', [ProductoController::class ,'update'])->name('producto.editar');
    Route::patch('inventario/producto', [ProductoController::class ,'patch'])->name('producto.reduce');
    
});


// CRUD DE ENTIDADES
Route::middleware('auth')->group(function () {

    Route::post('/entidades/almacen', [AlmacenController::class ,'storeEntidades'])->name('entidad.almacen.store');
    Route::patch('/entidades/almacen', [AlmacenController::class ,'update'])->name('entidad.almacen.update');
    Route::delete('/entidades/almacen', [AlmacenController::class ,'deleteEntidades'])->name('entidad.almacen.delete');

    Route::patch('/entidades/categoria', [CategoriasController::class ,'store'])->name('entidad.categoria.store');
    Route::post('/entidades/categoria', [CategoriasController::class ,'patch'])->name('entidad.categoria.update');
    Route::delete('/entidades/categoria', [CategoriasController::class ,'destroy'])->name('entidad.categoria.delete');

    Route::patch('/entidades/cliente', [ClienteController::class ,'store'])->name('entidad.cliente.store');
    Route::post('/entidades/cliente', [ClienteController::class ,'patch'])->name('entidad.cliente.update');
    Route::delete('/entidades/cliente', [ClienteController::class ,'destroy'])->name('entidad.cliente.delete');

    Route::patch('/entidades/proveedor', [ProveedoresController::class ,'store'])->name('entidad.proveedor.store');
    Route::post('/entidades/proveedor', [ProveedoresController::class ,'patch'])->name('entidad.proveedor.update');
    Route::delete('/entidades/proveedor', [ProveedoresController::class ,'destroy'])->name('entidad.proveedor.delete');

});


// CRUD DE PRODUCTOS AÑADIR Y BORRAR
Route::middleware('auth')->group(function () {
    Route::delete('/inventario/producto', [ProductoController::class ,'delete'])->name('producto.delete');
    Route::post('/inventario/producto', [ProductoController::class ,'store'])->name('producto.store');
    Route::patch('/inventario/producto', [ProductoController::class ,'patch'])->name('producto.patch');
});

// CRUD DE PEDIDOS
Route::middleware('auth')->group(function () {
    Route::post('/pedidos/add', [DetallesCompraController::class ,'addInventario'])->name('pedidos.addInventario');
    Route::delete('/pedidos', [DetallesCompraController::class ,'destroy'])->name('pedidos.destroy');
    Route::put('/pedidos/patch', [DetallesCompraController::class ,'patch'])->name('pedidos.patchInventario');
    Route::post('/pedidos', [DetallesCompraController::class ,'store'])->name('pedidos.store');
});

//CRUD DE VENTAS
Route::middleware('auth')->group(function () {
    Route::post('/ventas', [DetallesVentaController::class ,'store'])->name('ventas.store');
    Route::delete('/ventas', [DetallesVentaController::class ,'destroy'])->name('ventas.destroy');
});

//CRUD DE PROVEEDORES
Route::middleware('auth')->group(function () {
    Route::post('/proveedor', [ProveedoresController::class ,'store'])->name('proveedor.store');
    Route::delete('/proveedor', [ProveedoresController::class ,'destroy'])->name('proveedor.destroy');
    Route::patch('/proveedor', [ProveedoresController::class ,'patch'])->name('proveedor.patch');
    Route::post('/proveedor/email', [SendEmaillController::class, 'sendEmail'])->name('proveedor.email');
});

//RAILWAY
Route::get('/healthz', fn () => response('ok', 200));



require __DIR__ . '/auth.php';
