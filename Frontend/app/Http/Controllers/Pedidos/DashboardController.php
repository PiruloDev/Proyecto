<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pedidos\PedidosApiService;

class DashboardController extends Controller
{
    protected $pedidosApiService;

    public function __construct(PedidosApiService $pedidosApiService)
    {
        $this->pedidosApiService = $pedidosApiService;
    }

    public function index()
    {
        // Obtenemos el ID 8 de la sesión
        $clienteId = session('usuario.id');
        
        if (!$clienteId) {
            return redirect()->route('login')->with('error', 'Sesión expirada.');
        }

        try {
            // Llamamos a Java
            $pedidos = $this->pedidosApiService->obtenerPedidosPorCliente($clienteId);
            
            // Retornamos la vista correcta según tu estructura de carpetas
            return view('dashboards.client', compact('pedidos'));
            
        } catch (\Exception $e) {
            return view('dashboards.client', ['pedidos' => []])
                   ->with('error', 'No se pudo conectar con el servicio de pedidos.');
        }
    }
}