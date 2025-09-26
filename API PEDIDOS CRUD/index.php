<?php

require_once __DIR__ . '/Config.php'; 

$ruta = $_GET['ruta'] ?? 'pedidos'; 

switch (strtolower($ruta)) {
    case 'pedidos':
        $nombreClase = 'PedidosController';
        $archivoControlador = __DIR__ . '/Controlador/PedidosController.php';
        break;
        
    case 'estados':
        $nombreClase = 'EstadosPeController';
        $archivoControlador = __DIR__ . '/Controlador/EstadosPeController.php';
        break;
        
    case 'pedidosproveedores':
        $nombreClase = 'PedidosProveedoresController';
        $archivoControlador = __DIR__ . '/Controlador/PedidosProveedoresController.php';
        break;
        
    default:
    
        http_response_code(404);
        die("Error 404: Recurso no encontrado.");
}


if (file_exists($archivoControlador)) {
  
    require_once $archivoControlador;

    if (class_exists($nombreClase)) {
        $controller = new $nombreClase();

        $controller->manejarPeticion();
    } else {
        die("Error interno: La clase controladora '{$nombreClase}' no existe.");
    }
} else {
    die("Error interno: Archivo controlador no encontrado en '{$archivoControlador}'.");
}