<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use App\Services\Pedidos\PedidosApiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardEmpleadoController extends Controller
{
    protected $pedidosApiService;

    public function __construct(PedidosApiService $pedidosApiService)
    {
        $this->pedidosApiService = $pedidosApiService;
    }

    public function index()
    {
        try {
            // 1. Obtener datos de la API
            $pedidosRaw = $this->pedidosApiService->obtenerTodosLosPedidos();
            $productosRaw = $this->pedidosApiService->obtenerProductos();

            $pedidos = collect($pedidosRaw);
            $hoy = Carbon::today()->format('Y-m-d');

            // 2. Calcular "Pedidos Hoy"
            $pedidosHoy = $pedidos->filter(function($p) use ($hoy) {
                // Buscamos la fecha en las posibles llaves que devuelve tu API Java
                $fecha = $p['fechaIngreso'] ?? $p['FECHA_INGRESO'] ?? $p['fecha_INGRESO'] ?? '';
                return strpos($fecha, $hoy) !== false;
            })->count();

            // 3. Calcular "Pendientes"
            $pedidosPendientes = $pedidos->filter(function($p) {
                // Ajusta el '1' según el ID que uses en Java para el estado "Pendiente"
                $estado = $p['idEstadoPedido'] ?? $p['ID_ESTADO_PEDIDO'] ?? 0;
                return $estado == 1; 
            })->count();

            // 4. Calcular "Productos Disponibles" (Stock > 0)
            $productosDisponibles = collect($productosRaw)->filter(function($prod) {
                $stock = $prod['stockActual'] ?? $prod['STOCK_ACTUAL'] ?? 0;
                return $stock > 0;
            })->count();

            // 5. Total histórico
            $totalPedidos = $pedidos->count();

            return view('dashboard.empleado', compact(
                'pedidosHoy', 
                'pedidosPendientes', 
                'productosDisponibles', 
                'totalPedidos'
            ));

        } catch (\Exception $e) {
            Log::error("Error en Dashboard Empleado: " . $e->getMessage());
            return view('dashboard.empleado', [
                'pedidosHoy' => 0,
                'pedidosPendientes' => 0,
                'productosDisponibles' => 0,
                'totalPedidos' => 0
            ]);
        }
    }
}