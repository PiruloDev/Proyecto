<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use App\Services\Pedidos\PedidosApiService; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function indexAdmin()
    {
        try {
            $pedidos = $this->apiService->obtenerPedidos();
            return view('pedidosviews.PedidosClientes.index-admin', compact('pedidos'));
            
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
    // 1. Validación de los datos del encabezado
    $request->validate([
        'ID_CLIENTE' => 'required|integer',
        'ID_EMPLEADO' => 'required|integer',
        'ID_ESTADO_PEDIDO' => 'required|integer',
        'TOTAL_PRODUCTO' => 'required|numeric',
    ]);

    // 2. EXTRAER EL CARRITO DE LA SESIÓN
    $carrito = session('carrito', []);
    $detallesApi = [];

    // Convertimos cada ítem del carrito al formato que Java espera
    foreach ($carrito as $item) {
        $detallesApi[] = [
            'idProducto'       => (int)$item['id'],
            'cantidadProducto' => (int)$item['cantidad'],
            'precioUnitario'   => (float)$item['precio'],
            'subtotal'         => (float)($item['precio'] * $item['cantidad'])
        ];
    }

    // 3. Construir el paquete completo para Java
    $dataApi = [
        'cliente_id'       => (int)$request->input('ID_CLIENTE'), 
        'empleado_id'      => (int)$request->input('ID_EMPLEADO'),
        'estado_pedido_id' => (int)$request->input('ID_ESTADO_PEDIDO'),
        'total_producto'   => (float)$request->input('TOTAL_PRODUCTO'),
        'fecha_ingreso'    => now()->format('Y-m-d H:i:s'),
        'fecha_entrega'    => $request->input('FECHA_ENTREGA') ? $request->input('FECHA_ENTREGA') . ' 23:59:59' : null,
        'detalles'         => $detallesApi, // <--- ¡AQUÍ VAN LOS PRODUCTOS!
    ];

    // LOG DE CONTROL: Para que verifiques en tu consola si ahora sí se van los detalles
    \Log::info("Enviando pedido a API Java con detalles: ", $dataApi);

    try {
        $this->apiService->crearPedido($dataApi); 

        // Si se creó con éxito, limpiamos el carrito
        session()->forget('carrito');

        $route = $request->routeIs('admin.*') ? 'admin.pedidos.index' : 'pedidos.index';
        return redirect()->route($route)->with('success', 'Pedido creado exitosamente con sus productos.');

    } catch (Exception $e) {
        \Log::error("Error en API Java: " . $e->getMessage());
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

    $fechaEntrega = $request->input('FECHA_ENTREGA');
    if ($fechaEntrega && strlen($fechaEntrega) == 10) {
        $fechaEntrega .= ' 23:59:59';
    }

    $dataApi = [
        'cliente_id' => $request->input('ID_CLIENTE'),
        'empleado_id' => $request->input('ID_EMPLEADO'),
        'estado_pedido_id' => $request->input('ID_ESTADO_PEDIDO'),
        'total_producto' => $request->input('TOTAL_PRODUCTO'),
        'fecha_entrega' => $fechaEntrega ?: null, 
    ];

        try {
            $this->apiService->actualizarPedido($id, $dataApi); 

            
            if ($request->routeIs('admin.*')) {
                return redirect()->route('admin.pedidos.index')
                                 ->with('success', "Pedido con ID $id actualizado correctamente (Admin).");
            }
            
            
            return redirect()->route('pedidos.index')
                             ->with('success', "Pedido con ID $id actualizado correctamente (Empleado).");

        } catch (Exception $e) {
            
            return redirect()->back()->withInput()->with('error', 'Error al actualizar pedido: ' . $e->getMessage());
        }
    }

    
    public function destroy($id, Request $request)
    {
        try {
            $this->apiService->eliminarPedido($id);

            
            if ($request->routeIs('admin.*')) {
                return redirect()->route('admin.pedidos.index')
                                 ->with('success', 'Pedido eliminado correctamente (Admin).');
            }
            
            return redirect()->route('pedidos.index')
                             ->with('success', 'Pedido eliminado correctamente (Empleado).');
                             
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar pedido: ' . $e->getMessage());
        }
    }
    
    public function dashboardCliente()
    {
    
        $clienteId = Auth::id();
        
        $pedidos = [];

        dd($clienteId);
        
        try {
            $pedidos = $this->apiService->obtenerPedidosPorCliente($clienteId);
        } catch (Exception $e) {
             return view('dashboards.client', compact('pedidos'))
                          ->with('error', 'Error al cargar tus pedidos: ' . $e->getMessage());
        }

        return view('dashboards.client', compact('pedidos'));
    }
    
    public function dashboardEmpleado()
{
    $pedidos = [];
    $pedidosHoy = 0;
    $pedidosPendientes = 0;
    $totalPedidos = 0;
    $productosDisponibles = 0; // Este valor podrías traerlo de otro servicio de productos

    try {
        $pedidos = $this->apiService->obtenerPedidos();
        $totalPedidos = count($pedidos);

        // Lógica para contar pedidos de hoy y pendientes
        $fechaActual = now()->format('Y-m-d');
        
        foreach ($pedidos as $pedido) {
            // Contar pedidos de hoy
            if (isset($pedido['fecha_ingreso']) && str_contains($pedido['fecha_ingreso'], $fechaActual)) {
                $pedidosHoy++;
            }
            // Contar pendientes (Asumiendo que ID_ESTADO_PEDIDO = 1 es Pendiente)
            if (isset($pedido['estado_pedido_id']) && $pedido['estado_pedido_id'] == 1) {
                $pedidosPendientes++;
            }
        }

    } catch (Exception $e) {
        // Silenciamos o logueamos el error
    }

    return view('dashboards.employee', compact(
        'pedidos', 
        'pedidosHoy', 
        'pedidosPendientes', 
        'productosDisponibles', 
        'totalPedidos' 
    ));
}
}