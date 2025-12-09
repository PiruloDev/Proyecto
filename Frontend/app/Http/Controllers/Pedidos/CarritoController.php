<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CarritoController extends Controller
{
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
        // Obtener el carrito de la sesión, si no existe, devuelve un array vacío
        $carrito = session()->get('carrito', []);

        // Calcular el total general sumando (precio * cantidad) de cada item
        $totalGeneral = array_reduce($carrito, function ($sum, $item) {
            return $sum + ((float)$item['precio'] * $item['cantidad']);
        }, 0);

        return response()->json([
            'carrito' => array_values($carrito), 
            'total' => number_format($totalGeneral, 2, '.', ''), 
            'count' => count($carrito),
            'success' => true
        ]);
    }

    /**
     * Agrega un producto al carrito o incrementa su cantidad.
     * Método: POST /api/carrito/agregar/{id}
     */
    public function agregar(Request $request, $id)
    {
        // 1. Obtener la lista de productos del backend (API de Java/Spring)
        try {
            $response = Http::get("http://localhost:8080/productos");
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión con el servicio de Productos.'
            ], 503);
        }

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo obtener la lista de productos o el servicio devolvió un error. Status: ' . $response->status()
            ], $response->status());
        }

        $productosResponse = $response->json();
        $productos = [];
        
        // 🛑 LÓGICA ROBUSTA PARA MANEJAR RESPUESTAS DE JAVA
        if (is_array($productosResponse) && array_keys($productosResponse) === range(0, count($productosResponse) - 1)) {
            // Es un array simple
            $productos = $productosResponse;
        } elseif (isset($productosResponse['content']) && is_array($productosResponse['content'])) {
            // Es una respuesta paginada de Spring Data JPA
            $productos = $productosResponse['content'];
        } else {
             $productos = $productosResponse;
        }


        // 2. Buscar el producto por ID dentro del array usando comparación flexible
        $productoData = null;
        
        // Búsqueda flexible (==) para que '4' == 4 funcione
        foreach ($productos as $producto) {
            // Clave confirmada: 'Id Producto:'
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

        // 3. Mapear los campos usando las claves JSON confirmadas (con los dos puntos)
        $productoId = $productoData['Id Producto:'];
        $nombreProducto = $productoData['Nombre Producto:'];
        // Asegurarse que el precio se maneje como float
        $precioProducto = (float)$productoData['Precio:']; 

        // 4. Gestionar el carrito en sesión
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

    // ... (Mantener los métodos actualizar, remover y checkout sin cambios) ...
    
    // El resto de los métodos (actualizar, remover, checkout)
    public function actualizar(Request $request) { /* ... */ }
    public function remover($id) { /* ... */ }
    public function checkout()
    {
        $carrito = session()->get('carrito', []);

        if (empty($carrito)) {
            return response()->json([
                'success' => false,
                'message' => 'El carrito está vacío. Agrega productos antes de finalizar el pedido.'
            ], 400);
        }

        // Preparamos el payload que espera PedidoRequest en Java
        $payload = [
            'cliente_id' => auth()->id() ?? 1, 
            'items' => array_values($carrito), 
        ];

        try {
            // Llama al endpoint POST /pedidos en Java
            $response = Http::post('http://localhost:8080/pedidos', $payload);

            if ($response->successful()) {
                session()->forget('carrito');

                return response()->json([
                    'success' => true,
                    'message' => '¡Pedido realizado con éxito! Tu número de pedido es ' . ($response->json('id') ?? 'desconocido')
                ], 200);
            } else {
                $errorMsg = $response->json('message') ?? 'Error desconocido al procesar el pedido.';
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg
                ], $response->status());
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo conectar con el servicio de Pedidos.'
            ], 503);
        }
    }
}