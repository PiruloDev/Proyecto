<?php

// Incluye el controlador
require_once __DIR__ . '/Controlador/PedidosController.php';

// Crea una instancia del controlador
$controller = new PedidosController();

// Ejecuta el método que maneja la petición (GET o POST)
$controller->manejarPeticion();

?>