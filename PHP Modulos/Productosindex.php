<?php
require_once __DIR__ . '/controllers/productoscontroller/ProductosControllerAdmin.php';
require_once __DIR__ . '/controllers/productoscontroller/CategoriaProductosController.php';

$seccion = $_GET['seccion'] ?? 'productos';

include __DIR__ . '/templates/header_admin.php';

if ($seccion === 'productos') {
    $controller = new ProductosControllerAdmin();
    $productos = $controller->obtenerProductos();
    include __DIR__ . '/views/productosviews/listar_productos_admin.php';
    include __DIR__ . '/views/productosviews/form_producto.php';
} elseif ($seccion === 'categorias') {
    $controller = new CategoriaProductosController();
    $categorias = $controller->listarCategorias();
    include __DIR__ . '/views/productosviews/listar_categorias_admin.php';
    include __DIR__ . '/views/productosviews/form_categoria.php';
}

include __DIR__ . '/templates/footer_admin.php';
