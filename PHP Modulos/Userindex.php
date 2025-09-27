<?php
require_once __DIR__ . '/controllers/userscontroller/obtenerClienteController.php';
$controller = new ObtenerClienteController();
$controller->manejoPeticionCliente();
?>