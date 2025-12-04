<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\IngredientesController;
use App\Http\Controllers\Inventario\CategoriaController;
use App\Http\Controllers\Inventario\ProveedoresController;
use App\Http\Controllers\Inventario\PedidosProveedoresController; 
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

// Vista principal (Dashboard del Módulo)
Route::get('/dashboard/inventario', function () {
    return view('inventarioviews.indexinventario');
})->name('dashboard.inventario');


// --------------------------------------------
// CATEGORÍAS - CRUD COMPLETO
// --------------------------------------------
Route::prefix('/dashboard/inventario/categorias')->group(function () {
    // 1. Listar/Index
    Route::get('/', [CategoriaController::class, 'index'])->name('categorias.index');
    
    // 2. Formulario de Creación
    Route::get('/create', [CategoriaController::class, 'create'])->name('categorias.create');
    
    // 3. Almacenar (Store)
    Route::post('/store', [CategoriaController::class, 'store'])->name('categorias.store');
    
    // 4. Mostrar Detalle (Show)
    Route::get('/show/{id}', [CategoriaController::class, 'show'])->name('categorias.show');
    
    // 5. Actualizar (Update - Usando POST en tu implementación)
    Route::post('/update', [CategoriaController::class, 'update'])->name('categorias.update');
    
    // 6. Eliminar (Destroy - Usando POST en tu implementación)
    Route::post('/delete', [CategoriaController::class, 'destroy'])->name('categorias.destroy');
});


// --------------------------------------------
// PROVEEDORES - CRUD COMPLETO
// --------------------------------------------
Route::prefix('/dashboard/inventario/proveedores')->group(function() {
    // 1. Listar/Index
    Route::get('/', [ProveedoresController::class, 'index'])->name('proveedores.index');
    
    // 2. Formulario de Creación
    Route::get('/create', [ProveedoresController::class, 'create'])->name('proveedores.create');
    
    // 3. Almacenar (Store)
    Route::post('/store', [ProveedoresController::class, 'store'])->name('proveedores.store');
    
    // 4. Mostrar Detalle (Show)
    Route::get('/show/{id}', [ProveedoresController::class, 'show'])->name('proveedores.show');
    
    // 5. Actualizar (Update)
    Route::put('/update/{id}', [ProveedoresController::class, 'update'])->name('proveedores.update');
    
    // 6. Eliminar (Destroy)
    Route::delete('/delete/{id}', [ProveedoresController::class, 'destroy'])->name('proveedores.destroy');
});


// --------------------------------------------
// PEDIDOS A PROVEEDORES - CRUD COMPLETO
// --------------------------------------------
Route::prefix('/dashboard/inventario/pedidoproveedores')->group(function () {
    // 1. Listar/Index
    Route::get('/', [PedidosProveedoresController::class, 'index'])->name('pedidoproveedores.index');
    
    // 2. Formulario de Creación
    Route::get('/create', [PedidosProveedoresController::class, 'create'])->name('pedidoproveedores.create');
    
    // 3. Almacenar (Store)
    Route::post('/store', [PedidosProveedoresController::class, 'store'])->name('pedidoproveedores.store');
    
    // 4. Mostrar Detalle (Show)
    Route::get('/show/{id}', [PedidosProveedoresController::class, 'show'])->name('pedidoproveedores.show');
    
    // 5. Actualizar (Update)
    Route::put('/update/{id}', [PedidosProveedoresController::class, 'update'])->name('pedidoproveedores.update');
    
    // 6. Eliminar (Destroy)
    Route::delete('/delete/{id}', [PedidosProveedoresController::class, 'destroy'])->name('pedidoproveedores.destroy');
});


// --------------------------------------------
// INGREDIENTES - CRUD COMPLETO
// --------------------------------------------
Route::prefix('/dashboard/inventario/ingredientes')->group(function () {
    // 1. Listar/Index
    Route::get('/', [IngredientesController::class, 'index'])->name('ingredientes.index');
    
    // 2. Formulario de Creación
    Route::get('/create', [IngredientesController::class, 'create'])->name('ingredientes.create');
    
    // 3. Almacenar (Store)
    Route::post('/store', [IngredientesController::class, 'store'])->name('ingredientes.store');
    
    // 4. Mostrar Detalle (Show)
    Route::get('/show/{id}', [IngredientesController::class, 'show'])->name('ingredientes.show');
    
    // 5. Actualización (Update)
    Route::post('/update/{id}', [IngredientesController::class, 'update'])->name('ingredientes.update');
    
    // 5b. Actualización Especial de Cantidad (Stock)
    Route::post('/update-cantidad/{id}', [IngredientesController::class, 'updateCantidad'])->name('ingredientes.updateCantidad');
    
    // 6. Eliminar (Destroy)
    Route::post('/delete/{id}', [IngredientesController::class, 'destroy'])->name('ingredientes.destroy');
});

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
