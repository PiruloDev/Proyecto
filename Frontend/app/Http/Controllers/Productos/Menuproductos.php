<?php

namespace App\Http\Controllers\Productos;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Helpers\ProductImageHelper;

class Menuproductos extends Controller
{
   protected $baseUrl;

    public function __construct()
    {
        // Obtiene la URL base de la API desde el archivo .env
        $this->baseUrl = env('API_BASE_URL', 'http://32.193.167.191:8080'); 
    }
    public function index()
    {
        try {
            $responseCategorias = Http::get($this->apiBaseUrl . '/categorias');
            $responseProductos = Http::get($this->apiBaseUrl . '/productos');

            $categorias = collect([]);
            $productos = collect([]);

            if ($responseCategorias->successful() && $responseProductos->successful()) {
                $categoriasData = $responseCategorias->json();
                $productosData = $responseProductos->json();

                $categorias = collect($categoriasData)->map(function ($categoria) use ($productosData) {
                    $productosCategoria = collect($productosData)
                        ->filter(function ($prod) use ($categoria) {
                            return isset($prod['Id Categoria Producto:']) &&
                                   $prod['Id Categoria Producto:'] == $categoria['idCategoriaProducto'];
                        })
                        ->map(function ($prod) use ($categoria) {
                            $nombreProducto = $prod['Nombre Producto:'] ?? '';
                            $imagenApi = $prod['imagen'] ?? $prod['Imagen:'] ?? $prod['imagen_producto'] ?? $prod['Imagen Producto:'] ?? null;

                            return (object) [
                                'ID_PRODUCTO' => $prod['Id Producto:'] ?? 0,
                                'NOMBRE_PRODUCTO' => $nombreProducto,
                                'DESCRIPCION_PRODUCTO' => $prod['Descripcion Producto:'] ?? 'Producto fresco y delicioso',
                                'PRECIO_PRODUCTO' => $prod['Precio:'] ?? 0,
                                'STOCK_ACTUAL' => $prod['Stock Minímo:'] ?? 0,
                                // Si la API no devuelve imagen, usar el helper para obtenerla
                                'imagen' => $imagenApi ?? ProductImageHelper::getImage($nombreProducto, $prod['Id Producto:'] ?? null, $categoria['nombreCategoriaProducto'] ?? null)
                            ];
                        });

                    return (object) [
                        'ID_CATEGORIA_PRODUCTO' => $categoria['idCategoriaProducto'],
                        'NOMBRE_CATEGORIAPRODUCTO' => $categoria['nombreCategoriaProducto'],
                        'productosActivos' => $productosCategoria
                    ];
                });

                $productos = collect($productosData)->map(function ($prod) {
                    $nombreProducto = $prod['Nombre Producto:'] ?? '';
                    $imagenApi = $prod['imagen'] ?? $prod['Imagen:'] ?? $prod['imagen_producto'] ?? $prod['Imagen Producto:'] ?? null;

                    return (object) [
                        'ID_PRODUCTO' => $prod['Id Producto:'] ?? 0,
                        'NOMBRE_PRODUCTO' => $nombreProducto,
                        'DESCRIPCION_PRODUCTO' => $prod['Descripcion Producto:'] ?? 'Producto fresco y delicioso',
                        'PRECIO_PRODUCTO' => $prod['Precio:'] ?? 0,
                        'STOCK_ACTUAL' => $prod['Stock Minímo:'] ?? 0,
                        // Si la API no devuelve imagen, usar el helper para obtenerla
                        'imagen' => $imagenApi ?? ProductImageHelper::getImage($nombreProducto, $prod['Id Producto:'] ?? null)
                    ];
                });
            }

            return view('menu.menu', compact('categorias', 'productos'));

        } catch (\Exception $e) {
            \Log::error('Error API: ' . $e->getMessage());
            return view('menu.menu', [
                'categorias' => collect([]),
                'productos' => collect([])
            ]);
        }
    }
}
