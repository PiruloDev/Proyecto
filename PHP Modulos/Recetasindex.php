<?php
require_once __DIR__ . '/controllers/recetascontroller/RecetasController.php';

// 👇 Crear instancia del controlador
$controller = new RecetasController();

// Acción por defecto = listar
$accion = $_GET['accion'] ?? 'listar';

switch ($accion) {
    case 'listar':
        $controller->listar();
        break;

    case 'crear':
        $controller->crear();
        break;

    case 'guardar':
        $controller->guardar($_POST);
        break;

    case 'detalle':
        $id = $_GET['id'] ?? null;
        $controller->detalle($id);
        break;

    case 'editar':
        $id = $_GET['id'] ?? null;
        $controller->editar($id);
        break;

    case 'actualizar':
        $id = $_POST['id'] ?? null;
        $controller->actualizar($id, $_POST);
        break;

    case 'eliminar':
        $id = $_GET['id'] ?? null;
        $controller->eliminar($id);
        break;

    default:
        echo "⚠️ Acción no válida.";
}
