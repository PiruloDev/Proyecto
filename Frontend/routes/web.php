<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\IngredientesController;
use App\Http\Controllers\Inventario\CategoriaController;
use App\Http\Controllers\Inventario\ProveedoresController;
use App\Http\Controllers\Inventario\DetallePedidosController;
use App\Http\Controllers\Productos\Categoriaproductos;
use App\Http\Controllers\Productos\Menuproductos;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\PedidosController;

// ============================================
// RUTAS PÚBLICAS - El Castillo del Pan
// ============================================

// ============================================
// MODULO PRODUCTOS
// ============================================

// Homepage
Route::get('/', [Categoriaproductos::class, 'index'])->name('home');
// Menú de productos
Route::get('/menu', [Menuproductos::class, 'index'])->name('menu');

// ============================================
// AUTENTICACIÓN
// ============================================

// Login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
        // TODO: Implementar lógica de autenticación

    return back()->with('error', 'Funcionalidad en desarrollo');
})->name('login.submit');

// Registro
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', function () {
        // TODO: Implementar lógica de registro

    return back()->with('success', 'Funcionalidad en desarrollo');
})->name('register.submit');

// Logout
Route::post('/logout', function () {
        // Auth::logout();
    return redirect()->route('home');
})->name('logout');

// ============================================
// CARRITO
// ============================================

Route::get('/cart', function () {
    $cartItems = [];
    $subtotal = 0;
    return view('cart', compact('cartItems', 'subtotal'));
})->name('cart');

// ============================================
// DASHBOARDS (sin autenticación por ahora)
// ============================================

// Dashboard Cliente
Route::get('/dashboardcliente', function () {
    $totalPedidos = 0;
    $pedidosPendientes = 0;
    $pedidosRecientes = [];
    return view('dashboards.client', compact('totalPedidos', 'pedidosPendientes', 'pedidosRecientes'));
})->name('dashboard.client');

// Dashboard Empleado
Route::get('/dashboardempleado', function () {
    $pedidosHoy = 0;
    $pedidosPendientes = 0;
    $productosDisponibles = 0;
    $totalPedidos = 0;
    return view('dashboards.employee', compact('pedidosHoy', 'pedidosPendientes', 'productosDisponibles', 'totalPedidos'));
})->name('dashboard.employee');

// Dashboard Admin
Route::get('/dashboardadmin', function () {
    $totalProductos = 0;
    $productosActivos = 0;
    $empleadosActivos = 0;
    $clientesActivos = 0;
    $pedidosHoy = 0;
    return view('dashboards.admin', compact('totalProductos', 'productosActivos', 'empleadosActivos', 'clientesActivos', 'pedidosHoy'));
})->name('dashboard.admin');

// ============================================
// MÓDULO DE INVENTARIO (COMPLETAMENTE MODULAR)
// ============================================

// Vista principal
Route::get('/dashboard/inventario', function () {
    return view('inventarioviews.indexinventario');
})->name('dashboard.inventario');


Route::prefix('/dashboard/inventario/categorias')->group(function () {
    Route::get('/', [CategoriaController::class, 'index'])->name('categorias.index');
    Route::post('/store', [CategoriaController::class, 'store'])->name('categorias.store');
    Route::post('/update', [CategoriaController::class, 'update'])->name('categorias.update');
    Route::post('/delete', [CategoriaController::class, 'destroy'])->name('categorias.destroy');
});


Route::prefix('/dashboard/inventario/proveedores')->group(function() {

    Route::get('/', [ProveedoresController::class, 'index'])->name('proveedores.index');

    Route::post('/store', [ProveedoresController::class, 'store'])->name('proveedores.store');

    Route::put('/update/{id}', [ProveedoresController::class, 'update'])->name('proveedores.update');

    Route::delete('/delete/{id}', [ProveedoresController::class, 'destroy'])->name('proveedores.destroy');

});


// -------------- DETALLE PEDIDOS --------------
Route::prefix('/dashboard/inventario/detalle-pedidos')->group(function () {

    // Listar detalles de pedidos
    Route::get('/', [DetallePedidosController::class, 'index'])
        ->name('detallePedidos.index');

    // Crear un nuevo detalle de pedido
    Route::post('/store', [DetallePedidosController::class, 'store'])
        ->name('detallePedidos.store');

    // Actualizar un detalle de pedido existente
    Route::put('/update/{id}', [DetallePedidosController::class, 'update'])
        ->name('detallePedidos.update');

    // Eliminar un detalle de pedido
    Route::delete('/delete/{id}', [DetallePedidosController::class, 'destroy'])
        ->name('detallePedidos.destroy');
});


// ============================================
// INGREDIENTES - CRUD COMPLETO
// ============================================
// **CORRECCIÓN:** La sintaxis de group(function) () {} ha sido corregida a group(function () {})
Route::prefix('/dashboard/inventario/ingredientes')->group(function () {

    // Listar ingredientes
    Route::get('/', [IngredientesController::class, 'index'])
        ->name('ingredientes.index');

    // Crear ingrediente
    Route::post('/store', [IngredientesController::class, 'store'])
        ->name('ingredientes.store');

    // Actualizar ingrediente
    Route::post('/update/{id}', [IngredientesController::class, 'update'])
        ->name('ingredientes.update');

    // Actualizar solo cantidad
    Route::post('/update-cantidad/{id}', [IngredientesController::class, 'updateCantidad'])
        ->name('ingredientes.updateCantidad');

    // Eliminar ingrediente
    Route::post('/delete/{id}', [IngredientesController::class, 'destroy'])
        ->name('ingredientes.destroy');
}); // <-- CIERRE DEL GRUPO DE INGREDIENTES QUE FALTABA

// ============================================
// PEDIDOS CLIENTES - CRUD COMPLETO
// ============================================
Route::prefix('pedidos')->group(function () {
    Route::get('/', [PedidosController::class, 'index'])->name('pedidos.index');
    Route::get('/create', [PedidosController::class, 'create'])->name('pedidos.create');
    Route::post('/', [PedidosController::class, 'store'])->name('pedidos.store');

    Route::get('/edit/{id}', [PedidosController::class, 'edit'])->name('pedidos.edit');
    Route::put('/{id}', [PedidosController::class, 'update'])->name('pedidos.update');

    Route::delete('/{id}', [PedidosController::class, 'destroy'])->name('pedidos.destroy');
});

require __DIR__.'/settings.php';
