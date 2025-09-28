<?php
$modulo = $_GET['modulo'] ?? 'menu'; 
$accion = $_GET['accion'] ?? 'listar';

switch ($modulo) {
    case 'detallePedidos':
        require_once __DIR__ . '/controllers/ingredientecontroller/detallePedidoController.php';
        $controller = new DetallePedidosController();
        $controller->manejarPeticion($accion);
        break;

    case 'proveedores':
        require_once __DIR__ . '/controllers/ingredientecontroller/proveedoresController.php';
        $controller = new ProveedoresController();
        $controller->manejarPeticion($accion);
        break;

    case 'categoria':
        require_once __DIR__ . '/controllers/ingredientecontroller/categoriaController.php';
        $controller = new CategoriaController();
        $controller->manejarPeticion($accion);
        break;

    case 'ingredientes':
        require_once __DIR__ . '/controllers/ingredientecontroller/ingredientesController.php';
        $controller = new IngredientesController();
        $controller->manejarPeticion($accion);
        break;

    case 'menu':
default:
    include __DIR__ . '/views/ingredienteviews/menu.php';
    break;


}
