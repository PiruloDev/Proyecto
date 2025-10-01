<?php
require_once __DIR__ . '/../services/ReportesUsuariosService.php';

class ReportesUsuariosController {
    private $reportesUsuariosService;

    public function __construct() {
        $this->reportesUsuariosService = new ReportesUsuariosService();
    }

    public function manejarPeticion() {
        // ----- SOLO OBTENER LISTA DE USUARIOS -----
        $usuarios = $this->reportesUsuariosService->obtenerUsuarios();

        // ----- CARGAR VISTA -----
        require __DIR__ . '/../views/ReportesUsuariosIndex.php';
    }
}
