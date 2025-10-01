<?php
require_once __DIR__ . '/../../services/productosservices/CategoriaProductosService.php';

$service = new CategoriaProductosService();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion']) && $_POST['accion'] === 'crear') {
        $data = [
            "nombre" => $_POST['nombre']
        ];
        $service->crearCategoria($data);
        header("Location: ../../CategoriaProductosindex.php");
        exit;
    }
    if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
        $id = $_POST['id'];
        $service->eliminarCategoria($id);
        header("Location: ../../CategoriaProductosindex.php");
        exit;
    }
}

$categorias = $service->obtenerCategorias();
