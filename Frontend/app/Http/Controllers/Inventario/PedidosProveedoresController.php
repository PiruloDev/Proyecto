<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Services\Inventario\PedidosProveedoresService; // Usamos el nuevo nombre del servicio
use Illuminate\Http\Request;
    
class PedidosProveedoresController extends Controller // Nuevo nombre de la clase
{
    protected $service;

    public function __construct(PedidosProveedoresService $service) // Inyección del nuevo servicio
    {
        $this->service = $service;
    }

    /**
     * Listar todos los pedidos de proveedores (encabezados)
     */
    public function index()
    {
        $pedidos = $this->service->obtenerTodos(); // Nuevo método en el servicio

        if (!$pedidos) {
            return back()->with('error', 'No se pudieron obtener los pedidos de proveedores.');
        }

        // Cambiamos la vista para reflejar el nuevo nombre
        return view('inventarioviews.pedidosproveedores.index', compact('pedidos'));
    }

    /**
     * Mostrar el formulario para crear un nuevo pedido (si es necesario)
     */
    public function create()
    {
        return view('inventarioviews.pedidosproveedores.create');
    }

    /**
     * Almacenar un nuevo pedido completo (encabezado y detalles).
     * Recibe el JSON complejo desde el frontend.
     */
    public function store(Request $request)
    {
        // Validación del encabezado (la validación del array de detalles es compleja y se deja a la lógica de la vista/JavaScript para simplificar el controlador)
        $request->validate([
            'idProveedor' => 'required|integer|min:1',
            'numeroPedido' => 'required|integer|min:1',
            'fechaPedido' => 'required|date',
            'estadoPedido' => 'required|string',
            'detalles' => 'required|array|min:1', // Debe ser un array no vacío
            'detalles.*.idIngrediente' => 'required|integer',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precioUnitario' => 'required|numeric|min:0.01',
            'detalles.*.subtotal' => 'nullable|numeric', // Es nullable porque el backend lo calcula
        ]);
        
        $data = $request->all();

        $response = $this->service->crearPedidoCompleto($data); // Nuevo método

        if (!$response['success']) {
            return back()->with('error', $response['error'] ?? 'Error desconocido al crear el pedido completo.');
        }

        return redirect()->route('pedidosproveedores.index')->with('success', 'Pedido de proveedor creado correctamente (ID: ' . json_decode($response['data']) . ').');
    }

    /**
     * Mostrar un pedido de proveedor con detalles
     */
    public function show(int $id)
    {
        $pedido = $this->service->obtenerPedidoCompleto($id);

        if (!$pedido) {
            return back()->with('error', 'No se pudo obtener el pedido de proveedor.');
        }

        return view('inventarioviews.pedidosproveedores.show', compact('pedido'));
    }

    /**
     * Editar un pedido de proveedor existente (solo encabezado)
     */
    public function update(Request $request, int $id)
    {
        // Validación del encabezado (solo se valida lo que se actualiza en el backend)
        $request->validate([
            'idProveedor' => 'required|integer|min:1',
            'numeroPedido' => 'required|integer|min:1',
            'fechaPedido' => 'required|date',
            'estadoPedido' => 'required|string',
            // No se valida 'detalles' aquí porque la ruta PUT solo actualiza el encabezado
        ]);
        
        $data = $request->all();

        $response = $this->service->editarPedido($id, $data);

        if (!$response['success']) {
            return back()->with('error', $response['error'] ?? 'Error desconocido al actualizar el pedido.');
        }

        return back()->with('success', 'Pedido de proveedor actualizado correctamente.');
    }

    /**
     * Eliminar un pedido de proveedor por ID
     */
    public function destroy(int $id)
    {
        $response = $this->service->eliminarPedido($id); // Nuevo método

        if (!$response['success']) {
            return back()->with('error', $response['error'] ?? 'Error desconocido al eliminar el pedido.');
        }

        return redirect()->route('pedidosproveedores.index')->with('success', 'Pedido de proveedor eliminado correctamente.');
    }
}