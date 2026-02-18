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
    // 1. Validación estricta
    $request->validate([
        'ID_CLIENTE'       => 'required|integer|min:1',
        'ID_EMPLEADO'      => 'required|integer|min:1',
        'ID_ESTADO_PEDIDO' => 'required|integer|min:1',
        'TOTAL_PRODUCTO'   => 'required|numeric|min:0',
        'productos'        => 'required_without:carrito|array', // Valida que lleguen productos del modal si no hay carrito
    ]);

    $detallesApi = [];

    // 2. Lógica Híbrida: ¿Viene del Modal de Admin o del Carrito de Cliente?
    if ($request->has('productos')) {
        // FLUJO ADMINISTRADOR (Datos desde el Formulario/Modal)
        foreach ($request->input('productos') as $key => $productoId) {
            $cantidad = (int)$request->input('cantidades')[$key];
            
            $detallesApi[] = [
                'idProducto'       => (int)$productoId,
                'cantidadProducto' => $cantidad,
                // Nota: El precio unitario y subtotal deberían venir del request 
                // o consultarse para asegurar integridad.
                'precioUnitario'   => (float)($request->input('precios_unitarios')[$key] ?? 0), 
                'subtotal'         => (float)($request->input('subtotales')[$key] ?? 0)
            ];
        }
    } else {
        // FLUJO CLIENTE (Datos desde la Sesión/Carrito)
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

    // 3. Preparación de datos para la API de Java
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
        // 4. Envío a Java
        // Al enviar el objeto 'detalles' a Java, tu API de Spring Boot debe estar 
        // programada para recorrer ese array y restar el stock en su propia base de datos.
        $this->apiService->crearPedido($dataApi); 

        // Limpiar carrito si existía
        if (session()->has('carrito')) {
            session()->forget('carrito');
        }

        $route = $request->routeIs('admin.*') ? 'admin.pedidos.index' : 'pedidos.index';
        return redirect()->route($route)->with('success', 'Pedido creado y stock actualizado en el sistema.');
        
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
            
            $estados = []; 
            return view('pedidosviews.PedidosClientes.edit', compact('pedido', 'estados'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar pedido: ' . $e->getMessage());
        }
    }

    
   public function update(Request $request, $id)
{
    // Aplicamos la misma restricción para las ediciones
    $request->validate([
        'ID_CLIENTE'       => 'required|integer|min:1',
        'ID_EMPLEADO'      => 'required|integer|min:1',
        'ID_ESTADO_PEDIDO' => 'required|integer|min:1',
        'TOTAL_PRODUCTO'   => 'required|numeric|min:0',
        'FECHA_ENTREGA'    => 'nullable|date', 
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
    $productosDisponibles = 0;

    try {
        // 1. Obtener Pedidos
        $pedidos = $this->apiService->obtenerPedidos();
        $totalPedidos = count($pedidos);

        // 2. Obtener Productos (Para que no marque 0 en stock)
        $productosRaw = $this->apiService->obtenerProductos();
        $productosDisponibles = collect($productosRaw)->filter(function($prod) {
            // Buscamos la llave del stock que devuelve tu API de Java
            $stock = $prod['stockActual'] ?? $prod['STOCK_ACTUAL'] ?? 0;
            return $stock > 0;
        })->count();

        // 3. Lógica de conteo de fechas y estados
        $fechaActual = now()->format('Y-m-d');
        
        foreach ($pedidos as $pedido) {
            // Verificamos fecha (ajusta 'fecha_ingreso' si en Java llega diferente)
            $fechaIngreso = $pedido['fecha_ingreso'] ?? $pedido['fechaIngreso'] ?? '';
            if (str_contains($fechaIngreso, $fechaActual)) {
                $pedidosHoy++;
            }
            
            // Verificamos estado pendiente (ID 1)
            $estadoId = $pedido['estado_pedido_id'] ?? $pedido['idEstadoPedido'] ?? 0;
            if ($estadoId == 1) {
                $pedidosPendientes++;
            }
        }

    } catch (Exception $e) {
        \Log::error("Error en dashboardEmpleado: " . $e->getMessage());
    }

    // Retornamos la vista con todos los datos calculados
    return view('dashboards.employee', compact(
        'pedidos', 
        'pedidosHoy', 
        'pedidosPendientes', 
        'productosDisponibles', 
        'totalPedidos' 
    ));
}
}