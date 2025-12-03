<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Services\Inventario\DetallePedidoService;
use Illuminate\Http\Request;


class DetallePedidosController extends Controller
{
    protected $service;

    public function __construct(DetallePedidoService $service)
    {
        $this->service = $service;
    }

    /**
     * Listar todos los detalles de pedidos
     */
    public function index()
    {
        $detalles = $this->service->obtenerDetalles();

        if (!$detalles) {
            return back()->with('error', 'No se pudieron obtener los detalles de pedidos.');
        }

        return view('inventarioviews.detallepedidos.index', compact('detalles'));
    }

    /**
     * Crear un nuevo detalle de pedido
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'idPedido' => 'required|integer',
            'idProducto' => 'required|integer',
            'cantidadProducto' => 'required|integer',
            'precioUnitario' => 'required|numeric',
            'subtotal' => 'required|numeric',
        ]);

        $response = $this->service->crearDetallePedido($data);

        if (!$response['success']) {
            return back()->with('error', $response['error']);
        }

        return back()->with('success', 'Detalle de pedido creado correctamente.');
    }

    /**
     * Editar un detalle de pedido existente
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'idPedido' => 'required|integer',
            'idProducto' => 'required|integer',
            'cantidadProducto' => 'required|integer',
            'precioUnitario' => 'required|numeric',
            'subtotal' => 'required|numeric',
        ]);

        $response = $this->service->editarDetallePedido($id, $data);

        if (!$response['success']) {
            return back()->with('error', $response['error']);
        }

        return back()->with('success', 'Detalle de pedido actualizado correctamente.');
    }

    /**
     * Eliminar un detalle de pedido por ID
     */
    public function destroy(int $id)
    {
        $response = $this->service->eliminarDetallePedido($id);

        if (!$response['success']) {
            return back()->with('error', $response['error']);
        }

        return back()->with('success', 'Detalle de pedido eliminado correctamente.');
    }
}
