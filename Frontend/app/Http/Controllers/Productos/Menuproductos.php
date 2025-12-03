<?php

namespace App\Http\Controllers\Productos;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class Menuproductos extends Controller
{
    private $apiBaseUrl = 'http://localhost:8080';

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
                        ->map(function ($prod) {
                            return (object) [
                                'ID_PRODUCTO' => $prod['Id Producto:'] ?? 0,
                                'NOMBRE_PRODUCTO' => $prod['Nombre Producto:'] ?? '',
                                'DESCRIPCION_PRODUCTO' => 'Producto fresco y delicioso',
                                'PRECIO_PRODUCTO' => $prod['Precio:'] ?? 0,
                                'STOCK_ACTUAL' => $prod['Stock Minímo:'] ?? 0,
                                'imagen' => 'pan-rtzqhi1ok4k1bxlo.jpg'
                            ];
                        });
                    
                    return (object) [
                        'ID_CATEGORIA_PRODUCTO' => $categoria['idCategoriaProducto'],
                        'NOMBRE_CATEGORIAPRODUCTO' => $categoria['nombreCategoriaProducto'],
                        'productosActivos' => $productosCategoria
                    ];
                });
                
                $productos = collect($productosData)->map(function ($prod) {
                    return (object) [
                        'ID_PRODUCTO' => $prod['Id Producto:'] ?? 0,
                        'NOMBRE_PRODUCTO' => $prod['Nombre Producto:'] ?? '',
                        'PRECIO_PRODUCTO' => $prod['Precio:'] ?? 0,
                        'STOCK_ACTUAL' => $prod['Stock Minímo:'] ?? 0,
                        'imagen' => 'pan-rtzqhi1ok4k1bxlo.jpg'
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