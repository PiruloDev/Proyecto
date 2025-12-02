<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventarioController;

// ============================================
// RUTAS PÚBLICAS - El Castillo del Pan
// ============================================

// Homepage
Route::get('/', function () {
    return view('home.home  ');
})->name('home');

// Menú de productos
Route::get('/menu', function () {
    $productos = []; // Aquí se cargarán productos desde la BD
    return view('menu.menu', compact('productos'));
})->name('menu');


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
// DASHBOARDS (requieren autenticación)
// ============================================

Route::middleware(['auth'])->group(function () {
    
    // Dashboard Cliente
    Route::get('/dashboard/client', function () {
        $totalPedidos = 0;
        $pedidosPendientes = 0;
        $pedidosRecientes = [];
        return view('dashboards.client', compact('totalPedidos', 'pedidosPendientes', 'pedidosRecientes'));
    })->name('dashboard.client');
    
    // Dashboard Empleado
    Route::get('/dashboard/employee', function () {
        $pedidosHoy = 0;
        $pedidosPendientes = 0;
        $productosDisponibles = 0;
        $totalPedidos = 0;
        return view('dashboards.employee', compact('pedidosHoy', 'pedidosPendientes', 'productosDisponibles', 'totalPedidos'));
    })->name('dashboard.employee');
    
    // Dashboard Admin
    Route::get('/dashboard/admin', function () {
        $totalProductos = 0;
        $productosActivos = 0;
        $empleadosActivos = 0;
        $clientesActivos = 0;
        $pedidosHoy = 0;
        return view('dashboards.admin', compact('totalProductos', 'productosActivos', 'empleadosActivos', 'clientesActivos', 'pedidosHoy'));
    })->name('dashboard.admin');
});

// ============================================
// MÓDULO DE INVENTARIO: GESTIÓN DE INVENTARIO
// ============================================


// routes/web.php
Route::get('/dashboard/inventario', function () {
    // Renderiza la vista que acabas de crear
    return view('inventarioviews.indexinventario'); 
})->name('dashboard.inventario');

// Rutas de Categorías 
Route::prefix('/dashboard/inventario/categorias')->group(function () {
    Route::get('/', [InventarioController::class, 'indexCategorias'])->name('categorias.index'); 
    // Aquí irán las rutas 'store', 'update', 'destroy' de categorías
});

// Rutas de Proveedores <-- ¡NUEVO BLOQUE!
Route::prefix('/dashboard/inventario/proveedores')->group(function () {
    Route::get('/', [InventarioController::class, 'indexProveedores'])->name('proveedores.index'); 
    // Aquí irán las rutas 'store', 'update', 'destroy' de proveedores
});

// Rutas de Detalle de Pedidos <-- ¡NUEVO BLOQUE!
Route::prefix('/dashboard/inventario/detalle-pedidos')->group(function () {
    Route::get('/', [InventarioController::class, 'indexDetallePedidos'])->name('detallePedidos.index'); 
    // Aquí irán las rutas 'show', 'update', 'delete' de pedidos
    });


Route::prefix('/dashboard/inventario/ingredientes')->group(function () {
    // Listar ingredientes (GET /ingredientes) -> Referenciado como 'ingredientes.index'
    Route::get('/', [InventarioController::class, 'index'])->name('ingredientes.index');

    // Agregar nuevo ingrediente (POST /ingredientes) -> Referenciado como 'ingredientes.store'
    Route::post('/', [InventarioController::class, 'store'])->name('ingredientes.store');

    // Actualizar ingrediente completo (POST /ingredientes/update)
    Route::post('/update', [InventarioController::class, 'update'])->name('ingredientes.update');

    // Actualizar cantidad (POST /ingredientes/cantidad)
    Route::post('/cantidad', [InventarioController::class, 'updateCantidad'])->name('ingredientes.updateCantidad');

    // Eliminar ingrediente (POST /ingredientes/delete)
    Route::post('/delete', [InventarioController::class, 'destroy'])->name('ingredientes.destroy');
});




require __DIR__.'/settings.php';
