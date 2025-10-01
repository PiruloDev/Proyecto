<?php
session_start();

if (!isset($_SESSION['rol'])) {
    $_SESSION['rol'] = 'usuario'; 
}

$rol = $_SESSION['rol'];

if ($rol === 'admin') {
    require_once __DIR__ . '/controllers/ProductosController_admin.php';
    $controller = new ProductosController_admin();
} else {
    require_once __DIR__ . '/controllers/ProductosController_usuario.php';
    $controller = new ProductosController_usuario();
}

$controller->manejarPeticion();