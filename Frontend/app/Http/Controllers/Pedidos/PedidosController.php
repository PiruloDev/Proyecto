<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use App\Services\Pedidos\PedidosApiService; 
use Illuminate\Http\Request;
use Exception;

class PedidosController extends Controller
{
    protected $apiService;

    public function __construct(PedidosApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    
    public function index()
    {
        try {
            $pedidos = $this->apiService->obtenerPedidos();
            return view('pedidosviews.PedidosClientes.index', compact('pedidos'));
            
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar los pedidos: ' . $e->getMessage());
        }
    }

    
    public function create()
    {
         $estados = []; 
         return view('pedidosviews.PedidosClientes.create', compact('estados'));
    }

    
    public function store(Request $request)
    {
    
        $request->validate([
            'ID_CLIENTE' => 'required|integer',
            'ID_EMPLEADO' => 'required|integer',
            'ID_ESTADO_PEDIDO' => 'required|integer',
            'TOTAL_PRODUCTO' => 'required|numeric',
            'FECHA_ENTREGA' => 'nullable|date', 
        ]);
        
        
        $dataApi = [
            'id_CLIENTE' => $request->input('ID_CLIENTE'), 
            'id_EMPLEADO' => $request->input('ID_EMPLEADO'),
            'id_ESTADO_PEDIDO' => $request->input('ID_ESTADO_PEDIDO'),
            'total_PRODUCTO' => $request->input('TOTAL_PRODUCTO'),
            'fecha_ENTREGA' => $request->input('FECHA_ENTREGA'), 
        ];
        
        try {
            $this->apiService->crearPedido($dataApi); 

            return redirect()->route('pedidos.index')
                 ->with('success', 'Pedido creado correctamente.');
        } catch (Exception $e) {
            
            return redirect()->back()->withInput()->with('error', 'Error al crear pedido: ' . $e->getMessage());
        }
    }

    
    public function dashboardEmpleado()
    {
        $pedidos = [];
        $pedidosHoy = 0;
        $pedidosPendientes = 0;
        $productosDisponibles = 0;
        $totalPedidos = 0;
        
        try {
            $pedidos = $this->apiService->obtenerPedidos();
            $totalPedidos = count($pedidos); 
        } catch (Exception $e) {
             
        }

        return view('dashboards.employee', compact(
            'pedidos', 
            'pedidosHoy', 
            'pedidosPendientes', 
            'productosDisponibles', 
            'totalPedidos' 
        ));
    }


    
    public function edit($id)
    {
        try {
            $pedido = $this->apiService->obtenerPedidoPorId($id);
            if (!$pedido) {
                return redirect()->back()->with('error', 'Pedido no encontrado.');
            }
            
            $estados = []; 

            return view('pedidosviews.PedidosClientes.edit', compact('pedido', 'estados'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar pedido: ' . $e->getMessage());
        }
    }

   
    public function update(Request $request, $id)
    {
    
        $request->validate([
            'ID_CLIENTE' => 'required|integer',
            'ID_EMPLEADO' => 'required|integer',
            'ID_ESTADO_PEDIDO' => 'required|integer',
            'TOTAL_PRODUCTO' => 'required|numeric',
            'FECHA_ENTREGA' => 'nullable|date', 
        ]);

        
        $dataApi = [
            'id_CLIENTE' => $request->input('ID_CLIENTE'),
            'id_EMPLEADO' => $request->input('ID_EMPLEADO'),
            'id_ESTADO_PEDIDO' => $request->input('ID_ESTADO_PEDIDO'),
            'total_PRODUCTO' => $request->input('TOTAL_PRODUCTO'),
            'fecha_ENTREGA' => $request->input('FECHA_ENTREGA'), 
        ];

        try {
            $this->apiService->actualizarPedido($id, $dataApi); 

            return redirect()->route('pedidos.index')
                 ->with('success', "Pedido con ID $id actualizado correctamente.");
        } catch (Exception $e) {
            
            return redirect()->back()->withInput()->with('error', 'Error al actualizar pedido: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->apiService->eliminarPedido($id);

            return redirect()->route('pedidos.index')
                 ->with('success', 'Pedido eliminado.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar pedido: ' . $e->getMessage());
        }
    }
}