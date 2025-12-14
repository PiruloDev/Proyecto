<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pedidos\PedidosApiService; 
use Illuminate\Support\Facades\Log;
use Exception;

class CarritoController extends Controller
{
    protected $pedidosApiService;

    /**
     * Inyección de dependencias.
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
        
        Log::info('Carrito leído en obtenerCarrito:', ['contenido_leido' => $carrito]); 

        // Calcula el total general
        $totalGeneral = array_reduce($carrito, function ($sum, $item) {
            return $sum + ((float)$item['precio'] * $item['cantidad']);
        }, 0);
        
        // Mapeo para que el JSON sea fácil de consumir por el JS
        $itemsFormateados = array_map(function ($item) {
            $precio = (float) $item['precio'];
            $cantidad = (int) $item['cantidad'];
            $subtotal = $precio * $cantidad;
            return [
                'id' => $item['id'],
                'producto' => $item['nombre'],
                'precio' => number_format($precio, 0, ',', '.'), 
                'cantidad' => $cantidad,
                'subtotal' => number_format($subtotal, 0, ',', '.'), 
            ];
        }, $carrito);


        return response()->json([
            'items' => array_values($itemsFormateados), 
            'total' => number_format($totalGeneral, 0, ',', '.'), 
            'count' => count($carrito),
            'success' => true
        ]);
    }

    /**
     * Agrega un producto al carrito o incrementa su cantidad.
     * (Método 'agregar' omitido por brevedad, no necesita cambios)
     */
    public function agregar(Request $request, $id)
    {
        // ... (Tu código existente del método agregar) ...
        try {
            // 1. Obtener la lista completa de productos desde la API de Java
            $productos = $this->pedidosApiService->obtenerProductos();

        } catch (Exception $e) {
            Log::error('Error de conexión con API de Productos:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión con el servicio de Productos.'
            ], 503);
        }

        // 2. Buscar el producto por ID dentro del array devuelto por la API.
        $productoData = null;
        
        foreach ($productos as $producto) {
            // Usamos la clave de Java 'Id Producto:'
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

        // 3. Mapear los campos y limpiar el formato de precio
        $productoId = $productoData['Id Producto:'];
        $nombreProducto = $productoData['Nombre Producto:'];
        
        $precioString = $productoData['Precio:'];
        $precioProducto = (float) str_replace(',', '.', preg_replace('/[^0-9,.]/', '', $precioString));

        // 4. Gestionar el carrito en la sesión de Laravel
        $carrito = session()->get('carrito', []);

        Log::info('Carrito ANTES de agregar (ID: ' . $productoId . '):', ['carrito' => $carrito]); 

        if (isset($carrito[$productoId])) {
            $carrito[$productoId]['cantidad']++;
        } else {
            $carrito[$productoId] = [
                'id' => $productoId,
                'nombre' => $nombreProducto,
                'precio' => $precioProducto, // Precio guardado como float/numeric limpio
                'cantidad' => 1,
            ];
        }

        session()->put('carrito', $carrito);

        Log::info('Carrito DESPUÉS de agregar (ID: ' . $productoId . '):', ['carrito_actual' => session()->get('carrito')]);

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito.',
            'count' => count($carrito),
            'item_id' => $productoId
        ]);
    }


    /**
     * Actualiza la cantidad de un producto en el carrito o lo remueve si cantidad es 0.
     * Método: PATCH /api/carrito/actualizar
     */
    public function actualizar(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            // 🔥 CAMBIO: Permitimos 0 para la eliminación 🔥
            'cantidad' => 'required|integer|min:0', 
        ]);

        $id = $request->id;
        $cantidad = $request->cantidad;
        $carrito = session()->get('carrito', []);

        if (!isset($carrito[$id])) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado en el carrito.'], 404);
        }

        // 🔥 NUEVA LÓGICA: Si la cantidad es 0, eliminamos el producto. 🔥
        if ($cantidad === 0) { 
            unset($carrito[$id]);
            session()->put('carrito', $carrito);
            return response()->json(['success' => true, 'message' => 'Producto eliminado del carrito.']);
        }
        
        // Lógica de actualización normal (cantidad > 0)
        $carrito[$id]['cantidad'] = $cantidad;
        session()->put('carrito', $carrito);
        return response()->json(['success' => true, 'message' => 'Cantidad actualizada.']);
    }

    /**
     * Remueve un producto del carrito. (Este método ya no se usa, pero se mantiene para la ruta DELETE)
     */
    public function remover($id)
    {
        // Se recomienda llamar a la función actualizar(id, 0) para consolidar la lógica.
        // Pero si se usa la ruta DELETE, este código es correcto:
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
     * (Método 'checkout' omitido por brevedad, no necesita cambios)
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

    try {
        // Calcula el total general (basado en productos en el carrito)
        $totalGeneral = array_reduce($carrito, function ($sum, $item) {
            return $sum + ($item['precio'] * $item['cantidad']);
        }, 0);

        // Ahora sí, arma el payload correctamente
        $payload = [
            'cliente_id' => session('usuario.id'),
            'empleado_id' => 1,
            'estado_pedido_id' => 1,
            'fecha_entrega' => null,
            'total_producto' => $totalGeneral // Aquí ya está correctamente calculado
        ];

        Log::info('Payload enviado a Java:', $payload);

        // Llamada a la API de Java para crear el pedido
        $response = $this->pedidosApiService->crearPedido($payload);

        // Vaciar carrito solo si el pedido fue exitoso
        session()->forget('carrito');

        return response()->json([
            'success' => true,
            'message' => 'Pedido realizado con éxito',
            'pedido' => $response
        ]);

    } catch (Exception $e) {
        Log::error('Error al finalizar pedido:', ['error' => $e->getMessage()]);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
}