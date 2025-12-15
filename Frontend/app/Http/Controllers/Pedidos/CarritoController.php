<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pedidos\PedidosApiService;
use App\Helpers\ProductImageHelper;
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
        $carrito = session()->get('carrito', []);
        $cartItems = [];
        $subtotal = 0;

        foreach ($carrito as $item) {
            $precio = (float) ($item['precio'] ?? 0);
            $cantidad = (int) ($item['cantidad'] ?? 1);
            $cartItems[] = (object) [
                'id' => $item['id'] ?? 0,
                'producto_nombre' => $item['nombre'] ?? 'Producto',
                'producto_descripcion' => $item['descripcion'] ?? '',
                'producto_imagen' => $item['imagen'] ?? 'jugo.jpg',
                'precio' => $precio,
                'cantidad' => $cantidad,
                'stock' => $item['stock'] ?? 999
            ];
            $subtotal += $precio * $cantidad;
        }

        return view('cart.cart', compact('cartItems', 'subtotal'));
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

    public function agregar(Request $request, $id)
    {
        try {
            $productos = $this->pedidosApiService->obtenerProductos();

        } catch (Exception $e) {
            Log::error('Error de conexión con API de Productos:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión con el servicio de Productos.'
            ], 503);
        }
        $productoData = null;

        foreach ($productos as $producto) {
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
        $productoId = $productoData['Id Producto:'];
        $nombreProducto = $productoData['Nombre Producto:'];
        $descripcionProducto = $productoData['Descripcion:'] ?? '';
        $categoriaProducto = $productoData['Nombre Categoria:'] ?? null;

        $imagenProducto = ProductImageHelper::getImage(
            $nombreProducto,
            $productoId,
            $categoriaProducto
        );

        Log::info('Imagen obtenida para producto:', [
            'nombre' => $nombreProducto,
            'categoria' => $categoriaProducto,
            'imagen' => $imagenProducto
        ]);

        $stockProducto = $productoData['Stock:'] ?? 999;

        $precioString = $productoData['Precio:'];
        $precioProducto = (float) str_replace(',', '.', preg_replace('/[^0-9,.]/', '', $precioString));

        $carrito = session()->get('carrito', []);

        Log::info('Carrito ANTES de agregar (ID: ' . $productoId . '):', ['carrito' => $carrito]);

        if (isset($carrito[$productoId])) {
            // Incrementar cantidad pero ACTUALIZAR todos los datos del producto
            $carrito[$productoId]['cantidad']++;
            $carrito[$productoId]['nombre'] = $nombreProducto;
            $carrito[$productoId]['descripcion'] = $descripcionProducto;
            $carrito[$productoId]['imagen'] = $imagenProducto;
            $carrito[$productoId]['precio'] = $precioProducto;
            $carrito[$productoId]['stock'] = $stockProducto;
        } else {
            $carrito[$productoId] = [
                'id' => $productoId,
                'nombre' => $nombreProducto,
                'descripcion' => $descripcionProducto,
                'imagen' => $imagenProducto,
                'precio' => $precioProducto,
                'cantidad' => 1,
                'stock' => $stockProducto
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

    public function actualizar(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'cantidad' => 'required|integer|min:0',
        ]);

        $id = $request->id;
        $cantidad = $request->cantidad;
        $carrito = session()->get('carrito', []);

        if (!isset($carrito[$id])) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado en el carrito.'], 404);
        }

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

    public function vaciar()
    {
        session()->forget('carrito');
        return response()->json([
            'success' => true,
            'message' => 'Carrito vaciado exitosamente.'
        ]);
    }

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

        $payload = [
            'cliente_id' => session('usuario.id'),
            'empleado_id' => 1,
            'estado_pedido_id' => 1,
            'fecha_entrega' => null,
            'total_producto' => $totalGeneral // Aquí ya está correctamente calculado
        ];

        Log::info('Payload enviado a Java:', $payload);

        $response = $this->pedidosApiService->crearPedido($payload);
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
