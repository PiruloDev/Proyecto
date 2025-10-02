<?php
require_once __DIR__ . '/controllers/authcontroller/RegistroController.php';

// Crear instancia del controlador de registro
$registroController = new RegistroController();

// Procesar la solicitud de registro
$registroController->procesarRegistro();
?>