<?php
require_once __DIR__ . '/../Modelo/ReportesProductosMasVendidosService.php';

class ReportesProductosMasVendidosController {
    private $reportesProductosMasVendidosService;

    public function __construct() {
        $this->reportesProductosMasVendidosService = new ReportesProductosMasVendidosService();
    }

    public function manejarPeticion() {
        // Obtener límite si se envía por GET
        $limite = isset($_GET['limite']) ? intval($_GET['limite']) : 10;
        
        // Obtener productos más vendidos
        $productos = $this->reportesProductosMasVendidosService->obtenerProductosMasVendidos($limite);
        
        // Cargar vista
        require __DIR__ . '/../Vista/ReportesProductosMasVendidosIndex.php';
    }
}
?>