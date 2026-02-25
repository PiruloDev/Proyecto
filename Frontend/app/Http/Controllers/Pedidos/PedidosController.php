<?php

namespace App\Http\Controllers\Pedidos;

use App\Services\Reportes\OrdenSalidaService;
use App\Http\Controllers\Controller;
use App\Services\Pedidos\PedidosApiService; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class PedidosController extends Controller
{
    protected $apiService;
    protected $ordenSalidaService;

    public function __construct(PedidosApiService $apiService, OrdenSalidaService $ordenSalidaService)
    {
    $this->apiService = $apiService;
    $this->ordenSalidaService = $ordenSalidaService;
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
    
    $request->validate([
        'ID_CLIENTE'       => 'required|integer|min:1',
        'ID_EMPLEADO'      => 'required|integer|min:1',
        'ID_ESTADO_PEDIDO' => 'required|integer|min:1',
        'TOTAL_PRODUCTO'   => 'required|numeric|min:0',
        'productos'        => 'required_without:carrito|array', 
    ]);

    $detallesApi = [];

    
    if ($request->has('productos')) {
        
        foreach ($request->input('productos') as $key => $productoId) {
            $cantidad = (int)$request->input('cantidades')[$key];
            
            $detallesApi[] = [
                'idProducto'       => (int)$productoId,
                'cantidadProducto' => $cantidad,
                'precioUnitario'   => (float)($request->input('precios_unitarios')[$key] ?? 0), 
                'subtotal'         => (float)($request->input('subtotales')[$key] ?? 0)
            ];
        }
    } else {
        
        $carrito = session('carrito', []);
        foreach ($carrito as $item) {
            $detallesApi[] = [
                'idProducto'       => (int)$item['id'],
                'cantidadProducto' => (int)$item['cantidad'],
                'precioUnitario'   => (float)$item['precio'],
                'subtotal'         => (float)($item['precio'] * $item['cantidad'])
            ];
        }
    }

    $dataApi = [
        'cliente_id'       => (int)$request->input('ID_CLIENTE'), 
        'empleado_id'      => (int)$request->input('ID_EMPLEADO'),
        'estado_pedido_id' => (int)$request->input('ID_ESTADO_PEDIDO'),
        'total_producto'   => (float)$request->input('TOTAL_PRODUCTO'),
        'fecha_ingreso'    => now()->format('Y-m-d H:i:s'),
        'fecha_entrega'    => $request->input('FECHA_ENTREGA') ? $request->input('FECHA_ENTREGA') . ' 23:59:59' : null,
        'detalles'         => $detallesApi,
    ];

try {
    $pedidoCreado = $this->apiService->crearPedido($dataApi);

    
    $idPedido  = $pedidoCreado['ID_PEDIDO']      ?? $pedidoCreado['id_pedido']      ?? null;
    $idCliente = $pedidoCreado['cliente_id']      ?? $pedidoCreado['ID_CLIENTE']     ?? $dataApi['cliente_id'];
    $total     = $pedidoCreado['total_producto']  ?? $pedidoCreado['TOTAL_PRODUCTO'] ?? $dataApi['total_producto'];

    if ($idPedido && $idCliente) {
        $resultadoOrden = $this->ordenSalidaService->agregarVenta([
            'ID_CLIENTE'        => $idCliente,
            'ID_PEDIDO'         => $idPedido,
            'FECHA_FACTURACION' => now()->format('Y-m-d\TH:i:s'), 
            'TOTAL_FACTURA'     => $total,
        ]);

        if (isset($resultadoOrden['error'])) {
            \Log::warning("Pedido {$idPedido} creado pero falló la orden de salida: " . $resultadoOrden['error']);
        }
    }

    if (session()->has('carrito')) {
        session()->forget('carrito');
    }

    $route = $request->routeIs('admin.*') ? 'admin.pedidos.index' : 'pedidos.index';
    return redirect()->route($route)->with('success', 'Pedido creado y orden de salida generada correctamente.');

} catch (Exception $e) {
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

        if (!isset($pedido['detalles']) || empty($pedido['detalles'])) {
            
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
        'ID_CLIENTE'       => 'required|integer',
        'ID_EMPLEADO'      => 'required|integer',
        'ID_ESTADO_PEDIDO' => 'required|integer',
        'TOTAL_PRODUCTO'   => 'required|numeric',
        'productos'        => 'required|array|min:1',
        'cantidades'       => 'required|array',
    ]);

    try {
        $detallesApi = [];
        foreach ($request->input('productos') as $key => $productoId) {
            if (!empty($productoId)) {
                $detallesApi[] = [
                    'idProducto'       => (int)$productoId,
                    'cantidadProducto' => (int)$request->input('cantidades')[$key],
                    'precioUnitario'   => (float)($request->input('precios_unitarios')[$key] ?? 0),
                    'subtotal'         => (float)($request->input('subtotales')[$key] ?? 0)
                ];
            }
        }

        $dataApi = [
            'id_pedido'        => (int)$id, 
            'cliente_id'       => (int)$request->input('ID_CLIENTE'),
            'empleado_id'      => (int)$request->input('ID_EMPLEADO'),
            'estado_pedido_id' => (int)$request->input('ID_ESTADO_PEDIDO'),
            'total_producto'   => (float)$request->input('TOTAL_PRODUCTO'),
            'fecha_entrega'    => $request->input('FECHA_ENTREGA') ? $request->input('FECHA_ENTREGA') . ' 23:59:59' : null,
            'detalles'         => $detallesApi,
        ];

        
        $this->apiService->actualizarPedido($id, $dataApi);

        $msg = "Pedido #{$id} actualizado correctamente.";
        
        if (str_contains($request->url(), 'admin')) {
            return redirect()->route('admin.pedidos.index')->with('success', $msg);
        }
        
        return redirect()->route('pedidos.index')->with('success', $msg);

    } catch (Exception $e) {
        \Log::error("Error actualizando pedido {$id}: " . $e->getMessage());
        return redirect()->back()
            ->withInput()
            ->with('error', 'No se pudo actualizar el pedido: ' . $e->getMessage());
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
    $productosDisponibles = 0;

    try {
        
        $pedidos = $this->apiService->obtenerPedidos();
        $totalPedidos = count($pedidos);

        
        $productosRaw = $this->apiService->obtenerProductos();
        $productosDisponibles = collect($productosRaw)->filter(function($prod) {
            $stock = $prod['stockActual'] ?? $prod['STOCK_ACTUAL'] ?? 0;
            return $stock > 0;
        })->count();

        $fechaActual = now()->format('Y-m-d');
        
        foreach ($pedidos as $pedido) {
            $fechaIngreso = $pedido['fecha_ingreso'] ?? $pedido['fechaIngreso'] ?? '';
            if (str_contains($fechaIngreso, $fechaActual)) {
                $pedidosHoy++;
            }
            
            $estadoId = $pedido['estado_pedido_id'] ?? $pedido['idEstadoPedido'] ?? 0;
            if ($estadoId == 1) {
                $pedidosPendientes++;
            }
        }

    } catch (Exception $e) {
        \Log::error("Error en dashboardEmpleado: " . $e->getMessage());
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