<?php
require_once __DIR__ . '/config/configCategoriaProductos.php';
require_once __DIR__ . '/controllers/productoscontroller/CategoriaProductosController.php';

// Controlador
$controller = new CategoriaProductosController();

// Acción
$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'crear':
        $controller->crear($_POST['nombre'] ?? null);
        break;

    case 'actualizar':
        $controller->actualizar($_POST['id'] ?? null, $_POST['nombre'] ?? null);
        break;

    case 'eliminar':
        $controller->eliminar($_GET['id'] ?? null);
        break;

    default:
        $controller->index();
        break;
}
