<?php
$modulo = $_GET['modulo'] ?? 'menu'; // por defecto muestra el menú
$accion = $_GET['accion'] ?? 'listar';

switch ($modulo) {
    case 'ingredientes':
        require_once __DIR__ . '/controlador/ingredientesController.php';
        $controller = new IngredientesController();
        $controller->manejarPeticion($accion);
        break;

    case 'categoria':
        require_once __DIR__ . '/controlador/categoriaController.php';
        $controller = new CategoriaController();
        $controller->manejarPeticion($accion);
        break;

    case 'proveedores':
        require_once __DIR__ . '/controlador/proveedoresController.php';
        $controller = new ProveedoresController();
        $controller->manejarPeticion($accion);
        break;

    case 'detallepedidos':
        require_once __DIR__ . '/controlador/detallePedidoController.php';
        $controller = new DetallePedidosController();
        $controller->manejarPeticion($accion);
        break;

    case 'menu':
    default:
        include __DIR__ . '/vista/menu.php';
        break;
}
