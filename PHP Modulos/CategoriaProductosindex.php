<?php
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/controllers/CategoriaProductosController.php';

// Conexión
$database = new Database();
$db = $database->getConnection();

// Controlador
$controller = new CategoriaProductosController($db);

// Acciones según la URL
$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'crear':
        $controller->crear($_POST['nombre']);
        break;
    case 'actualizar':
        $controller->actualizar($_POST['id'], $_POST['nombre']);
        break;
    case 'eliminar':
        $controller->eliminar($_GET['id']);
        break;
    default:
        $controller->index();
        break;
}
