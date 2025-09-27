<?php
require_once __DIR__ . '/controllers/userscontroller/obtenerEmpleadoController.php';
$controller = new ObtenerEmpleadoController();
$controller->manejoPeticionEmpleado();
?>