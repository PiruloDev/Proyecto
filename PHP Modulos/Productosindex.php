<?php
session_start();

// Ejemplo: aquí seteas el rol manualmente para probar.
// En un login real, el rol vendría de la base de datos.
if (!isset($_SESSION['rol'])) {
    $_SESSION['rol'] = 'usuario'; // Cambia a 'admin' para probar como admin
}

$rol = $_SESSION['rol'];

if ($rol === 'admin') {
    require_once __DIR__ . '/Controladores/ProductosController_admin.php';
    $controller = new ProductosController_admin();
} else {
    require_once __DIR__ . '/Controladores/ProductosController_usuario.php';
    $controller = new ProductosController_usuario();
}

$controller->manejarPeticion();
