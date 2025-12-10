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
            // La vista 'index' ya fue corregida para usar claves como 'id_PEDIDO'
            return view('pedidosviews.PedidosClientes.index', compact('pedidos'));
            
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
         // NOTA: Si EstadosPedidos no está migrado, este array es temporal
         $estados = []; 
         return view('pedidosviews.PedidosClientes.create', compact('estados'));
    }

    public function store(Request $request)
    {
        // 1. Validación (Usamos los nombres que vienen del formulario/input)
        $request->validate([
            'ID_CLIENTE' => 'required|integer',
            'ID_EMPLEADO' => 'required|integer',
            'ID_ESTADO_PEDIDO' => 'required|integer',
            'TOTAL_PRODUCTO' => 'required|numeric',
        ]);
        
        // 2. Mapeo: Convertimos las claves del formulario (MAYÚSCULAS)
        //    al formato exacto que la API de Java necesita (id_CLIENTE, total_PRODUCTO)
        $dataApi = [
            'id_CLIENTE' => $request->input('ID_CLIENTE'),
            'id_EMPLEADO' => $request->input('ID_EMPLEADO'),
            'id_ESTADO_PEDIDO' => $request->input('ID_ESTADO_PEDIDO'),
            'total_PRODUCTO' => $request->input('TOTAL_PRODUCTO'),
            // NOTA: fecha_INGRESO y fecha_ENTREGA generalmente se manejan en Java (servidor)
            // Si Java requiere fecha_INGRESO, debes añadirla aquí: 'fecha_INGRESO' => now()->format('Y-m-d')
        ];
        
        try {
            $this->apiService->crearPedido($dataApi); // Enviamos los datos mapeados

            return redirect()->route('pedidos.index')
                 ->with('success', 'Pedido creado correctamente.');
        } catch (Exception $e) {
            // Se mostrará el error devuelto por la API de Java
            return redirect()->back()->withInput()->with('error', 'Error al crear pedido: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $pedido = $this->apiService->obtenerPedidoPorId($id);
            if (!$pedido) {
                return redirect()->back()->with('error', 'Pedido no encontrado.');
            }
            
            // NOTA: Si EstadosPedidos no está migrado, este array es temporal
            $estados = []; 

            // La vista 'edit' ya fue corregida para usar claves como 'id_PEDIDO'
            return view('pedidosviews.PedidosClientes.edit', compact('pedido', 'estados'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar pedido: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        // 1. Validación
        $request->validate([
            'ID_CLIENTE' => 'required|integer',
            'ID_EMPLEADO' => 'required|integer',
            'ID_ESTADO_PEDIDO' => 'required|integer',
            'TOTAL_PRODUCTO' => 'required|numeric',
        ]);

        // 2. Mapeo: Convertimos las claves del formulario (MAYÚSCULAS)
        //    al formato exacto que la API de Java necesita (id_CLIENTE, total_PRODUCTO)
        $dataApi = [
            'id_CLIENTE' => $request->input('ID_CLIENTE'),
            'id_EMPLEADO' => $request->input('ID_EMPLEADO'),
            'id_ESTADO_PEDIDO' => $request->input('ID_ESTADO_PEDIDO'),
            'total_PRODUCTO' => $request->input('TOTAL_PRODUCTO'),
            // NOTA: Para PUT/UPDATE, no necesitas enviar ID_PEDIDO, ya que va en la URL.
        ];

        try {
            $this->apiService->actualizarPedido($id, $dataApi); // Enviamos los datos mapeados

            return redirect()->route('pedidos.index')
                 ->with('success', "Pedido con ID $id actualizado correctamente.");
        } catch (Exception $e) {
            // Se mostrará el error devuelto por la API de Java
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