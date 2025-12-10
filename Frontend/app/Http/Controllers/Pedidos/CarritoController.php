<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pedidos\PedidosApiService; // Importamos el servicio para comunicarnos con la API de Java
use Exception;

class CarritoController extends Controller
{
    protected $pedidosApiService;

    /**
     * Inyección de dependencias.
     * El controlador usará el servicio para todas las interacciones con la API.
     */
    public function __construct(PedidosApiService $pedidosApiService)
    {
        $this->pedidosApiService = $pedidosApiService;
    }

    /**
     * Devuelve la vista principal del carrito.
     */
    public function index()
    {
        return view('pedidosviews.Carrito.index');
    }

    /**
     * Obtiene el contenido completo del carrito desde la sesión y lo devuelve como JSON.
     * Método: GET /api/carrito
     */
    public function obtenerCarrito()
    {
        $carrito = session()->get('carrito', []);
        
        // Calcula el total general
        $totalGeneral = array_reduce($carrito, function ($sum, $item) {
            return $sum + ((float)$item['precio'] * $item['cantidad']);
        }, 0);

        return response()->json([
            // array_values() es importante para convertir el array asociativo en un array indexado para JSON
            'carrito' => array_values($carrito), 
            'total' => number_format($totalGeneral, 2, '.', ''), 
            'count' => count($carrito),
            'success' => true
        ]);
    }

    /**
     * Agrega un producto al carrito o incrementa su cantidad.
     * Llama a la API de Java para obtener los datos del producto (precio, nombre).
     * Método: POST /api/carrito/agregar/{id}
     */
    public function agregar(Request $request, $id)
    {
        try {
            // 1. Obtener la lista completa de productos desde la API de Java
            $productos = $this->pedidosApiService->obtenerProductos();

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión con el servicio de Productos.'
            ], 503);
        }

        // 2. Buscar el producto por ID dentro del array devuelto por la API.
        $productoData = null;
        
        // La búsqueda debe ser flexible (==) si los IDs vienen como strings o enteros
        foreach ($productos as $producto) {
            // Usamos las claves de la respuesta JSON de la API: 'Id Producto:' y 'Precio:'
            if (isset($producto['Id Producto:']) && $producto['Id Producto:'] == $id) {
                $productoData = $producto;
                break;
            }
        }

        if (!$productoData) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado en el servicio externo (ID: ' . $id . ').' 
            ], 404);
        }

        // 3. Mapear los campos
        $productoId = $productoData['Id Producto:'];
        $nombreProducto = $productoData['Nombre Producto:'];
        $precioProducto = (float)$productoData['Precio:']; 

        // 4. Gestionar el carrito en la sesión de Laravel
        $carrito = session()->get('carrito', []);

        if (isset($carrito[$productoId])) {
            $carrito[$productoId]['cantidad']++;
        } else {
            $carrito[$productoId] = [
                'id' => $productoId,
                'nombre' => $nombreProducto,
                'precio' => $precioProducto,
                'cantidad' => 1,
            ];
        }

        session()->put('carrito', $carrito);

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito.',
            'count' => count($carrito),
            'item_id' => $productoId
        ]);
    }

    /**
     * Actualiza la cantidad de un producto en el carrito.
     * Método: POST /api/carrito/actualizar
     */
    public function actualizar(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'cantidad' => 'required|integer|min:1',
        ]);

        $id = $request->id;
        $cantidad = $request->cantidad;
        $carrito = session()->get('carrito', []);

        if (isset($carrito[$id])) {
            $carrito[$id]['cantidad'] = $cantidad;
            session()->put('carrito', $carrito);
            return response()->json(['success' => true, 'message' => 'Cantidad actualizada.']);
        }

        return response()->json(['success' => false, 'message' => 'Producto no encontrado en el carrito.'], 404);
    }

    /**
     * Remueve un producto del carrito.
     * Método: DELETE /api/carrito/remover/{id}
     */
    public function remover($id)
    {
        $carrito = session()->get('carrito', []);

        if (isset($carrito[$id])) {
            unset($carrito[$id]);
            session()->put('carrito', $carrito);
            return response()->json(['success' => true, 'message' => 'Producto eliminado del carrito.']);
        }

        return response()->json(['success' => false, 'message' => 'Producto no encontrado en el carrito.'], 404);
    }

    /**
     * Proceso de checkout (FINALIZAR PEDIDO).
     * Llama al endpoint POST /pedidos de la API de Java para crear el pedido.
     * Método: POST /api/carrito/checkout
     */
    public function checkout()
    {
        $carrito = session()->get('carrito', []);

        if (empty($carrito)) {
            return response()->json(['success' => false, 'message' => 'El carrito está vacío.'], 400);
        }

        try {
            // 1. Preparamos el payload que espera el PedidoRequest en Java
            $payload = [
                'cliente_id' => auth()->id() ?? 1, // Usar el ID del cliente logeado (o 1 por defecto si no hay auth)
                'items' => array_values($carrito), // Convertir el carrito en un array indexado de items
            ];

            // 2. LLAMADA A LA API DE JAVA A TRAVÉS DEL SERVICIO
            $response = $this->pedidosApiService->crearPedidoCheckout($payload);

            // 3. Limpiar el carrito solo si el pedido fue exitoso
            session()->forget('carrito');

            return response()->json([
                'success' => true,
                'message' => $response['message'] ?? '¡Pedido realizado con éxito!',
                'id' => $response['id'] ?? null // ID del pedido creado en la API
            ], 200);

        } catch (Exception $e) {
            // Capturamos cualquier error lanzado por el servicio (ej. error 500, stock insuficiente)
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}