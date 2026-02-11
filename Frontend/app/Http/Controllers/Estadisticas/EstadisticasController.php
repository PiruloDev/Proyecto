<?php

namespace App\Http\Controllers\Estadisticas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Reportes\ProductosMasVendidosService;

class EstadisticasController extends Controller
{
    protected $productosMasVendidosService;

    public function __construct(ProductosMasVendidosService $productosMasVendidosService)
    {
        $this->productosMasVendidosService = $productosMasVendidosService;
    }

    public function index()
    {
        $productosMasVendidos = $this->productosMasVendidosService->obtenerProductosMasVendidos(10);
        return view('estadisticas.index', compact('productosMasVendidos'));
    }
}
