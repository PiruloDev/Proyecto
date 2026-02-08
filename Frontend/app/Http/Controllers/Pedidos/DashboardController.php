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
    $clienteId = session('usuario.id');
    
    if (!$clienteId) {
        return redirect()->route('login')->with('error', 'Sesión expirada.');
    }

    try {
        $pedidosRaw = $this->pedidosApiService->obtenerPedidosPorCliente($clienteId);
        
        // IMPORTANTE: Ordenamos por 'id_PEDIDO' que es lo que manda tu Java
        $pedidos = collect($pedidosRaw)->sortByDesc(function($item) {
            return $item['id_PEDIDO'] ?? $item['ID_PEDIDO'] ?? 0;
        })->values()->all();
        
        return view('dashboards.client', compact('pedidos'));
        
    } catch (\Exception $e) {
        return view('dashboards.client', ['pedidos' => []])
               ->with('error', 'Error: ' . $e->getMessage());
    }
}
}