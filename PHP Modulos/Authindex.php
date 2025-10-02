<?php
require_once __DIR__ . '/controllers/authcontroller/AuthController.php';

// Crear instancia del controlador de autenticación
$authController = new AuthController();

// Procesar la solicitud de login
$authController->procesarLogin();
?>