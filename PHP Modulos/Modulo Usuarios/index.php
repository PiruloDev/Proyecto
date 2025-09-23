<?php
require_once __DIR__ . '/controllers/obtenerClienteController.php';
$controller = new ObtenerClienteController();
$controller->peticionCliente();
?>