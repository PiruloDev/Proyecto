<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\IngredientesController;
use App\Http\Controllers\Inventario\CategoriaIngredientesController;
use App\Http\Controllers\Inventario\ProveedoresController;
use App\Http\Controllers\Inventario\PedidosProveedoresController;
use App\Http\Controllers\Inventario\RecetasController;
use App\Http\Controllers\Inventario\ProduccionController;
use App\Http\Controllers\Productos\Categoriaproductos;
use App\Http\Controllers\Productos\Menuproductos;
use App\Http\Controllers\Pedidos\PedidosController;
use App\Http\Controllers\Pedidos\EstadoPedidoController;
use App\Http\Controllers\Productos\ProductoController;
use App\Http\Controllers\Usuarios\EmpleadoController;
use App\Http\Controllers\Usuarios\ClienteController;
use App\Http\Controllers\Auth\RegisterController;
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
// Ruta para la vista de productos (CRUD completo)
Route::prefix('productos')->group(function() {
    Route::get('/', [ProductoController::class, 'index'])->name('productos.index');
    Route::post('/store', [ProductoController::class, 'store'])->name('productos.store');
    Route::put('/update/{id}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/delete/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
});
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
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

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
Route::prefix('/dashboard/inventario/categorias-ingredientes')->group(function () {
    // 1. Listar/Index
    Route::get('/', [CategoriaIngredientesController::class, 'index'])
         ->name('categorias-ingredientes.index');

    // 2. Formulario de Creación
    Route::get('/create', [CategoriaIngredientesController::class, 'create'])
         ->name('categorias-ingredientes.create');

    // 3. Almacenar (Store)
    Route::post('/store', [CategoriaIngredientesController::class, 'store'])
         ->name('categorias-ingredientes.store');

    // 4. Mostrar Detalle (Show) - Opcional
    Route::get('/show/{id}', [CategoriaIngredientesController::class, 'show'])
         ->name('categorias-ingredientes.show');

    // 5. Actualizar (Update)
    Route::put('/update/{id}', [CategoriaIngredientesController::class, 'update'])
         ->name('categorias-ingredientes.update');

    // 6. Eliminar (Destroy)
    Route::delete('/delete/{id}', [CategoriaIngredientesController::class, 'destroy'])
         ->name('categorias-ingredientes.destroy');
});


// --------------------------------------------
// PROVEEDORES - CRUD COMPLETO
// --------------------------------------------
Route::prefix('/dashboard/inventario/categorias-ingredientes')->group(function () {
    // 1. Listar/Index
    Route::get('/', [CategoriaIngredientesController::class, 'index'])
         ->name('categorias-ingredientes.index');

    // 2. Formulario de Creación
    Route::get('/create', [CategoriaIngredientesController::class, 'create'])
         ->name('categorias-ingredientes.create');

    // 3. Almacenar (Store)
    Route::post('/store', [CategoriaIngredientesController::class, 'store'])
         ->name('categorias-ingredientes.store');

    // 4. Mostrar Detalle (Show) - Opcional
    Route::get('/show/{id}', [CategoriaIngredientesController::class, 'show'])
         ->name('categorias-ingredientes.show');

    // 5. Actualizar (Update)
    Route::put('/update/{id}', [CategoriaIngredientesController::class, 'update'])
         ->name('categorias-ingredientes.update');

    // 6. Eliminar (Destroy)
    Route::delete('/delete/{id}', [CategoriaIngredientesController::class, 'destroy'])
         ->name('categorias-ingredientes.destroy');
});

// --------------------------------------------
// PROVEEDORES - CRUD COMPLETO (REEMPLAZO)
// --------------------------------------------
Route::prefix('/dashboard/inventario/proveedores')->group(function() {
    // 1. Listar/Index
    Route::get('/', [ProveedoresController::class, 'index'])
         ->name('proveedores.index');

    // 2. Formulario de Creación
    Route::get('/create', [ProveedoresController::class, 'create'])
         ->name('proveedores.create');

    // 3. Almacenar (Store)
    Route::post('/store', [ProveedoresController::class, 'store'])
         ->name('proveedores.store');

    // 4. Mostrar Detalle (Show)
    Route::get('/show/{id}', [ProveedoresController::class, 'show'])
         ->name('proveedores.show');

    // 5. Actualizar (Update)
    Route::put('/update/{id}', [ProveedoresController::class, 'update'])
         ->name('proveedores.update');

    // 6. Eliminar (Destroy)
    Route::delete('/delete/{id}', [ProveedoresController::class, 'destroy'])
         ->name('proveedores.destroy');
});


// --------------------------------------------
// PEDIDOS A PROVEEDORES - CRUD COMPLETO
// --------------------------------------------
Route::prefix('/dashboard/inventario/pedidos-proveedores')->group(function () {
    // 1. Listar/Index
    Route::get('/', [PedidosProveedoresController::class, 'index'])
         ->name('pedidoproveedores.index');

    // 2. Almacenar (Store - POST)
    Route::post('/store', [PedidosProveedoresController::class, 'store'])
         ->name('pedidoproveedores.store');

    // 3. Mostrar Detalle (Show)
    Route::get('/show/{id}', [PedidosProveedoresController::class, 'show'])
         ->name('pedidoproveedores.show');

    // 4. Actualizar (Update - PUT)
    Route::put('/update/{id}', [PedidosProveedoresController::class, 'update'])
         ->name('pedidoproveedores.update');

    // 5. Eliminar (Destroy - DELETE)
    Route::delete('/delete/{id}', [PedidosProveedoresController::class, 'destroy'])
         ->name('pedidoproveedores.destroy');
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
    Route::put('/update/{id}', [IngredientesController::class, 'update'])->name('ingredientes.update');

    // 5b. Actualización Especial de Cantidad (Stock)
    Route::patch('/update-cantidad/{id}', [IngredientesController::class, 'updateCantidad'])->name('ingredientes.updateCantidad');

    // 6. Eliminar (Destroy)
    Route::delete('/delete/{id}', [IngredientesController::class, 'destroy'])->name('ingredientes.destroy');
});

// --------------------------------------------
// RECETAS - CRUD
// --------------------------------------------
Route::prefix('/dashboard/inventario/recetas')->group(function () {
    // 1. Listar todas las recetas (RecetasController.java: obtenerTodasLasRecetas)
    Route::get('/', [RecetasController::class, 'index'])
         ->name('recetas.index');

    // 2. Mostrar/Detalle de una receta por ID de Producto (RecetasController.java: obtenerRecetaPorProducto)
    Route::get('/show/{idProducto}', [RecetasController::class, 'show'])
         ->name('recetas.show');

    // 3. Crear Receta (RecetasController.java: crearReceta)
    Route::post('/store', [RecetasController::class, 'store'])
         ->name('recetas.store');

    // 4. Actualizar Receta por ID de Producto (RecetasController.java: actualizarReceta)
    Route::put('/update/{idProducto}', [RecetasController::class, 'update'])
         ->name('recetas.update');

    // 5. Eliminar Receta por ID de Producto (RecetasController.java: eliminarReceta)
    Route::delete('/delete/{idProducto}', [RecetasController::class, 'destroy'])
         ->name('recetas.destroy');
});

// --------------------------------------------
// PRODUCCIÓN - CRUD
// --------------------------------------------
Route::prefix('/dashboard/inventario/produccion')->group(function () {
    // 1. Listar Historial (Index)
    Route::get('/', [ProduccionController::class, 'index'])
         ->name('produccion.index');

    // 2. Registrar Producción (Store)
    Route::post('/store', [ProduccionController::class, 'store'])
         ->name('produccion.store');

    // 3. Eliminar Producción (Destroy)
    Route::delete('/delete/{id}', [ProduccionController::class, 'destroy'])
         ->name('produccion.destroy');
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

// ============================================
// ESTADO PEDIDO - CRUD COMPLETO
// ============================================

Route::prefix('estados')->group(function () {
    Route::get('/', [EstadoPedidoController::class, 'index'])->name('estados.index');
    Route::get('/create', [EstadoPedidoController::class, 'create'])->name('estados.create');
    Route::post('/', [EstadoPedidoController::class, 'store'])->name('estados.store');
    Route::get('/edit/{id}', [EstadoPedidoController::class, 'edit'])->name('estados.edit');
    Route::put('/{id}', [EstadoPedidoController::class, 'update'])->name('estados.update');
    Route::delete('/{id}', [EstadoPedidoController::class, 'destroy'])->name('estados.destroy');
});

// ============================================
// EMPLEADOS - CRUD COMPLETO
// ============================================
Route::prefix('empleados')->name('empleados.')->group(function () {
    Route::get('/', [EmpleadoController::class, 'index'])->name('index');
    Route::get('/create', [EmpleadoController::class, 'create'])->name('create');
    Route::post('/', [EmpleadoController::class, 'store'])->name('store');
    Route::get('/{id}', [EmpleadoController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [EmpleadoController::class, 'edit'])->name('edit');
    Route::patch('/{id}', [EmpleadoController::class, 'update'])->name('update');
    Route::delete('/{id}', [EmpleadoController::class, 'destroy'])->name('destroy');
});

// ============================================
// CLIENTES - SOLO LECTURA
// ============================================
Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');

require __DIR__.'/settings.php';
