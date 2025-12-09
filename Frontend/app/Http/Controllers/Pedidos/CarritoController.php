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
        $carrito = session()->get('carrito', []);

        $totalGeneral = array_reduce($carrito, function ($sum, $item) {
            return $sum + ((float)$item['precio'] * $item['cantidad']);
        }, 0);

        return response()->json([
            'carrito' => $carrito,
            'total' => number_format($totalGeneral, 2),
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
        // TU API NO TIENE /productos/{id}, así que traemos TODOS y filtramos
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
                'message' => 'No se pudo obtener la lista de productos.'
            ], 404);
        }

        $productos = $response->json();

        // Buscar el producto por ID dentro del array usando el campo "Id Producto"
        $productoData = collect($productos)->firstWhere('Id Producto', (int)$id);

        if (!$productoData) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado en el servicio externo.'
            ], 404);
        }

        // Mapear los campos tal como llegan en la API
        $productoId = $productoData['Id Producto'];
        $nombreProducto = $productoData['Nombre Producto'];
        $precioProducto = (float)$productoData['Precio'];

        // Carrito en sesión
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
     * Método: PATCH /api/carrito/actualizar
     */
    public function actualizar(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'cantidad' => 'required|integer|min:0',
        ]);

        $id = $request->input('id');
        $cantidad = $request->input('cantidad');

        $carrito = session()->get('carrito', []);

        if (isset($carrito[$id])) {
            if ($cantidad > 0) {
                $carrito[$id]['cantidad'] = $cantidad;
                session()->put('carrito', $carrito);

                return response()->json([
                    'success' => true,
                    'message' => 'Cantidad actualizada.',
                    'item_id' => $id
                ]);
            } else {
                // Si la cantidad es 0, removemos el producto
                unset($carrito[$id]);
                session()->put('carrito', $carrito);

                return response()->json([
                    'success' => true,
                    'message' => 'Producto eliminado.',
                    'item_id' => $id,
                    'removed' => true
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Producto no encontrado en el carrito.'
        ], 404);
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

            return response()->json([
                'success' => true,
                'message' => 'Producto removido del carrito.'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Producto no encontrado en el carrito.'
        ], 404);
    }

    /**
     * Proceso de checkout. Llama a la API de IntelliJ.
     * Método: POST /api/carrito/checkout
     */
    public function checkout()
    {
        $carrito = session()->get('carrito', []);

        if (empty($carrito)) {
            return response()->json([
                'success' => false,
                'message' => 'El carrito está vacío.'
            ], 400);
        }

        // Preparamos el payload que espera PedidoRequest en Java
        $payload = [
            'cliente_id' => auth()->id() ?? 1,
            'items' => array_values($carrito),
        ];

        try {

            $response = Http::post('http://localhost:8080/pedidos', $payload);

            if ($response->successful()) {
                session()->forget('carrito');

                return response()->json([
                    'success' => true,
                    'message' => '¡Pedido realizado con éxito!'
                ], 200);
            } else {
                $errorMsg = $response->json('message') ?? 'Error desconocido.';
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
