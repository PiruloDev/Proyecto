<?php

// Incluye el controlador de pedidos a proveedores
require_once __DIR__ . '/Controlador/PedidosProveedoresController.php';

// Crea una instancia del controlador
$controller = new PedidosProveedoresController();

// Ejecuta el método que maneja la petición (GET o POST)
$controller->manejarPeticion();

?>