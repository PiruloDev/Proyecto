<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pedidos\PedidosApiService;

class DashboardController extends Controller
{
    protected $pedidosApiService;

    public function __construct(PedidosApiService $pedidosApiService)
    {
        $this->pedidosApiService = $pedidosApiService;
    }

  public function index()
{
    $clienteId = session('usuario.id');
    if (!$clienteId) return redirect()->route('login');

    try {
        $pedidosRaw = $this->pedidosApiService->obtenerPedidosPorCliente($clienteId);
        $detallesRaw = $this->pedidosApiService->obtenerTodosLosDetalles();
        $productosRaw = $this->pedidosApiService->obtenerProductos();

        // 1. Mapear productos (Súper flexible con las llaves de Java)
        $mapaProductos = collect($productosRaw)->mapWithKeys(function ($item) {
            // Buscamos el ID en todas estas posibles llaves que vimos en tus logs
            $id = $item['Id Producto:'] ?? $item['idProducto'] ?? $item['ID_PRODUCTO'] ?? null;
            
            // Buscamos el Nombre
            $nombre = $item['Nombre Producto:'] ?? $item['nombreProducto'] ?? $item['NOMBRE_PRODUCTO'] ?? 'Producto Desconocido';
            
            // Buscamos el Precio
            $precio = $item['Precio:'] ?? $item['precio'] ?? $item['PRECIO_PRODUCTO'] ?? 0;
            
            return [$id => ['nombre' => $nombre, 'precio' => $precio]];
        });

        // 2. Agrupar detalles por ID de Pedido
        $detallesPorPedido = collect($detallesRaw)->groupBy(function ($d) {
            return $d['idPedido'] ?? $d['ID_PEDIDO'] ?? 0;
        });

        // 3. Unir todo
        $pedidos = collect($pedidosRaw)->map(function($pedido) use ($detallesPorPedido, $mapaProductos) {
            // ID del pedido actual
            $pedidoId = $pedido['id_PEDIDO'] ?? $pedido['idPedido'] ?? $pedido['ID_PEDIDO'] ?? 0;
            
            // Obtener sus detalles
            $detallesRelacionados = $detallesPorPedido->get($pedidoId) ?? collect([]);

            // Mapear cada detalle para que tenga nombre del producto
            $pedido['productos'] = $detallesRelacionados->map(function($d) use ($mapaProductos) {
                $prodId = $d['idProducto'] ?? $d['ID_PRODUCTO'] ?? null;
                $infoProd = $mapaProductos->get($prodId);

                return [
                    'nombre'   => $infoProd['nombre'] ?? 'Producto ID: ' . $prodId,
                    'cantidad' => $d['cantidadProducto'] ?? $d['CANTIDAD_PRODUCTO'] ?? 0,
                    'precio'   => $d['precioUnitario'] ?? $d['PRECIO_UNITARIO'] ?? ($infoProd['precio'] ?? 0),
                    'subtotal' => $d['subtotal'] ?? $d['SUBTOTAL'] ?? 0,
                ];
            })->values()->toArray();

            return $pedido;
        })->sortByDesc(function($p) {
            return $p['id_PEDIDO'] ?? $p['idPedido'] ?? $p['ID_PEDIDO'] ?? 0;
        })->values();

        return view('dashboards.client', compact('pedidos'));

    } catch (\Exception $e) {
        \Log::error("Error en Dashboard: " . $e->getMessage());
        return view('dashboards.client', ['pedidos' => collect([])]);
    }
}
}