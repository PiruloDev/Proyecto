<?php
session_start();

if (!isset($_SESSION['rol'])) {
    $_SESSION['rol'] = 'usuario'; 
}

$rol = $_SESSION['rol'];

if ($rol === 'admin') {
    require_once __DIR__ . '/controllers/productoscontroller/ProductosController_admin.php';
    $controller = new ProductosControllerAdmin();
} else {
    require_once __DIR__ . '/controllers/productoscontroller/ProductosController_usuario.php';
    $controller = new ProductosControllerUsuario();
}

$controller->manejarPeticion();
