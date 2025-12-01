<?php

use Illuminate\Support\Facades\Route;

// ============================================
// RUTAS PÚBLICAS - El Castillo del Pan
// ============================================

// Homepage
Route::get('/', function () {
    return view('home');
})->name('home');

// Menú de productos
Route::get('/menu', function () {
    $productos = []; // Aquí se cargarán productos desde la BD
    return view('menu', compact('productos'));
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

require __DIR__.'/settings.php';
