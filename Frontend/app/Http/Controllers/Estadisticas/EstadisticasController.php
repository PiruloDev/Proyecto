<?php

namespace App\Http\Controllers\Estadisticas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Reportes\ProductosMasVendidosService;
use App\Services\Reportes\UsuariosRegistradosService;

class EstadisticasController extends Controller
{
    protected $productosMasVendidosService;
    protected $usuariosRegistradosService;

    public function __construct(
        ProductosMasVendidosService $productosMasVendidosService,
        UsuariosRegistradosService $usuariosRegistradosService
    ) {
        $this->productosMasVendidosService = $productosMasVendidosService;
        $this->usuariosRegistradosService  = $usuariosRegistradosService;
    }

    public function index()
    {
        $productosMasVendidos = $this->productosMasVendidosService->obtenerProductosMasVendidos(10);
        $usuariosRegistrados  = $this->usuariosRegistradosService->obtenerUsuariosRegistrados();

        return view('estadisticas.index', compact('productosMasVendidos', 'usuariosRegistrados'));
    }
}