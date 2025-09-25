<?php
session_start();

// Aquí puedes decidir si es admin o usuario (ejemplo simple con variable de sesión)
$rol = $_SESSION['rol'] ?? 'usuario';

if ($rol === 'admin') {
    require_once __DIR__ . '/Controladores/ProductosController_admin.php';
    $controller = new ProductosController_admin();
} else {
    require_once __DIR__ . '/Controladores/ProductosController_usuario.php';
    $controller = new ProductosController_usuario();
}

$controller->manejarPeticion();
