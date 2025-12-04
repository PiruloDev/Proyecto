<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Inventario\PedidosProveedoresService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; // Útil para formatear fechas

class PedidosProveedoresController extends Controller
{
    protected $service;

    public function __construct(PedidosProveedoresService $service)
    {
        $this->service = $service;
    }

    /**
     * Muestra el listado de todos los pedidos de proveedores.
     */
    public function index()
    {
        $response = $this->service->obtenerPedidos();

        if (!$response['success']) {
            $errorMessage = $response['error'] ?? 'Error desconocido al obtener pedidos.';
            return view('inventarioviews.pedidoproveedores.index', ['pedidos' => []])
                   ->with('error', 'Error al cargar pedidos: ' . $errorMessage);
        }
        
        $pedidos = $response['data'] ?? [];
        
        // Se asume que la vista se llamará 'inventarioviews.pedidoproveedores.index'
        return view('inventarioviews.pedidoproveedores.index', compact('pedidos'));
    }

    /**
     * Procesa la creación de un nuevo pedido, incluyendo sus detalles. (STORE)
     * NOTA: Este método es complejo porque debe manejar el JSON del encabezado + los detalles.
     * En una implementación real con Blade, los detalles se recogen con JavaScript/inputs dinámicos.
     */
    public function store(Request $request)
    {
        // 1. VALIDACIÓN DEL ENCABEZADO
        $request->validate([
            'idProveedor' => 'required|integer|exists:proveedores,ID_PROVEEDOR', // Asume que tienes una tabla Proveedores
            'numeroPedido' => 'required|integer|unique:pedidos_proveedores,NUMERO_PEDIDO', // Asume unicidad
            'estadoPedido' => 'required|string|max:50',
            // El campo fechaPedido se llenará automáticamente con la fecha actual del servidor.
            
            // 2. VALIDACIÓN DE DETALLES (se asume un formato de array)
            'detalles' => 'required|array|min:1',
            'detalles.*.idIngrediente' => 'required|integer',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precioUnitario' => 'required|numeric|min:0',
        ]);
        
        // 3. CONSTRUIR EL PAYLOAD PARA SPRING
        // Spring espera el formato del modelo PedidosProveedores.java
        $payload = [
            'idProveedor' => (int)$request->input('idProveedor'),
            'numeroPedido' => (int)$request->input('numeroPedido'),
            // La fecha de pedido debe ser enviada como java.util.Date (timestamp o string ISO)
            // Se usa la fecha actual de Laravel y se convierte a un formato que Spring pueda parsear,
            // o simplemente se pasa el timestamp. Usaremos la fecha actual del servidor.
            'fechaPedido' => Carbon::now()->getTimestampMs(), // Envía el timestamp en milisegundos (más seguro)
            'estadoPedido' => $request->input('estadoPedido'),
            'detalles' => $request->input('detalles') // Ya es un array de detalles
        ];

        $response = $this->service->crearPedidoCompleto($payload);

        if ($response['success']) {
            return Redirect::route('pedidoproveedores.index')->with('success', $response['response']);
        }

        return Redirect::back()->withInput()->with('error', $response['error']);
    }

    /**
     * Muestra los detalles de un pedido (útil para ver los ingredientes comprados).
     */
    public function show(int $id)
    {
        $response = $this->service->obtenerPedidoConDetalles($id);
        
        if (!$response['success']) {
            return Redirect::route('pedidoproveedores.index')->with('error', 'No se pudo cargar el detalle del pedido: ' . $response['error']);
        }
        
        $pedido = $response['data'];
        
        // Se asume que la vista se llamará 'inventarioviews.pedidoproveedores.show'
        return view('inventarioviews.pedidoproveedores.show', compact('pedido'));
    }

    /**
     * Actualiza el encabezado de un pedido. (UPDATE)
     */
    public function update(Request $request, int $id)
    {
        // Solo actualizamos el encabezado
        $request->validate([
            'idProveedor' => 'required|integer',
            'numeroPedido' => 'required|integer',
            'estadoPedido' => 'required|string|max:50',
        ]);
        
        $payload = $request->only(['idProveedor', 'numeroPedido', 'estadoPedido']);
        // La API de Spring requiere también la fecha, la usaremos del formulario (o la actual)
        $payload['fechaPedido'] = Carbon::now()->getTimestampMs();

        $response = $this->service->actualizarPedido($id, $payload);

        if ($response['success']) {
            return Redirect::route('pedidoproveedores.index')->with('success', $response['response']);
        }

        return Redirect::back()->withInput()->with('error', $response['error']);
    }

    /**
     * Elimina un pedido. (DELETE)
     */
    public function destroy(int $id)
    {
        $response = $this->service->eliminarPedido($id);

        if ($response['success']) {
            return Redirect::route('pedidoproveedores.index')->with('success', $response['response']);
        }
        
        return Redirect::back()->with('error', $response['error']);
    }
}