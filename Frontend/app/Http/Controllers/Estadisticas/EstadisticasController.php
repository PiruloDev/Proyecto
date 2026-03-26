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
        Log::info('Accediendo a EstadisticasController@index');
        
        try {
            $productosMasVendidos = $this->productosMasVendidosService->obtenerProductosMasVendidos(10);
            $usuariosRegistrados = $this->usuariosRegistradosService->obtenerUsuariosRegistrados();

            Log::info('Estadísticas obtenidas correctamente', [
                'productos_count' => count($productosMasVendidos),
                'usuarios_count' => count($usuariosRegistrados)
            ]);

            return view('estadisticas.index', compact('productosMasVendidos', 'usuariosRegistrados'));
        } catch (\Exception $e) {
            Log::error('Excepción en EstadisticasController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('estadisticas.index', [
                'productosMasVendidos' => [],
                'usuariosRegistrados' => []
            ])->with('error', 'Ocurrió un error al cargar las estadísticas. Por favor, revisa los logs.');
        }
    }
}