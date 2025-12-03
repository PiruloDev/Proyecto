<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PedidosService;

class PedidosController extends Controller
{
    protected $service;

    public function __construct(PedidosService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $pedidos = $this->service->obtenerPedidos();

        // CORRECCIÓN DE VISTA: Añadir 'PedidosClientes'
        return view('pedidosviews.PedidosClientes.index', compact('pedidos')); 
    }

    public function create()
    {
        // CORRECCIÓN DE VISTA: Añadir 'PedidosClientes'
        return view('pedidosviews.PedidosClientes.create'); 
    }

   public function store(Request $request)
{
    // 1. Definir y validar la variable $data (¡ESTO FALTABA!)
    $data = $request->validate([
        'ID_CLIENTE' => 'required|integer',
        'ID_EMPLEADO' => 'required|integer',
        'ID_ESTADO_PEDIDO' => 'required|integer',
        'TOTAL_PRODUCTO' => 'required|numeric',
    ]);

    // 2. Asignar la fecha de ingreso
    $data['FECHA_INGRESO'] = now();

    // 3. Ejecutar la creación (Ahora $data está definida)
    $this->service->agregarPedido($data);

    // 4. Redireccionar
    return redirect()->route('pedidos.index')
                     ->with('success', 'Pedido creado correctamente');
}

    public function edit($id)
    {
        $pedido = $this->service->obtenerPedidoPorId($id);

        // CORRECCIÓN DE VISTA: Añadir 'PedidosClientes'
        return view('pedidosviews.PedidosClientes.edit', compact('pedido'));
    }

    public function update(Request $request, $id)
{
    // 1. Obtener el pedido original para mantener las fechas
    $pedido_original = $this->service->obtenerPedidoPorId($id);

    if (!$pedido_original) {
        return redirect()->back()->with('error', 'Pedido no encontrado');
    }

    // 2. Definir y validar la variable $data
    $data = $request->validate([
        'ID_CLIENTE' => 'required|integer',
        'ID_EMPLEADO' => 'required|integer',
        'ID_ESTADO_PEDIDO' => 'required|integer',
        'TOTAL_PRODUCTO' => 'required|numeric',
    ]);

    // 3. Mantener fechas originales (Necesario para que la base de datos no tenga campos vacíos)
    // Usamos el acceso por clave de array que parece estar usando tu servicio
    $data['FECHA_INGRESO'] = $pedido_original['FECHA_INGRESO'];
    $data['FECHA_ENTREGA'] = $pedido_original['FECHA_ENTREGA'];

    // 4. Ejecutar la actualización (Ahora $data está definida)
    $this->service->actualizarPedido($id, $data);

    // 5. Redireccionar
    return redirect()->route('pedidos.index')
                     ->with('success', 'Pedido con ID ' . $id . ' actualizado correctamente');
}

    public function destroy($id)
    {
        $this->service->eliminarPedido($id);

        // CORRECCIÓN DE RUTA: Cambiado a 'pedidos.index'
        return redirect()->route('pedidos.index')
                         ->with('success', 'Pedido eliminado');
    }
}