<?php 
require_once __DIR__ . '/Controlador/ReportesVentasController.php';
require_once __DIR__ . '/Controlador/ReportesUsuariosController.php';

$modulo = $_GET['modulo'] ?? 'ventas';

if ($modulo === 'ventas') {
    $controller = new ReportesVentasController();
    $controller->manejarPeticion();
} elseif ($modulo === 'usuarios') {
    $controller = new ReportesUsuariosController();
    $controller->manejarPeticion();
} else {
    echo "Módulo no válido";
}

