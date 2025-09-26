<?php

// Incluye el controlador de estados de pedido
require_once __DIR__ . '/Controlador/EstadosPeController.php';

// Crea una instancia del controlador
$controller = new EstadosPeController();

// Ejecuta el método que maneja la petición (GET o POST)
$controller->manejarPeticion();

?>