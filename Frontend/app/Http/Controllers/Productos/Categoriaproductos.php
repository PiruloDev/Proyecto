<?php

namespace App\Http\Controllers\Productos;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class Categoriaproductos extends Controller
{
    private $apiBaseUrl = 'http://localhost:8080';

    public function index()
    {
        try {
            $response = Http::get($this->apiBaseUrl . '/categorias');
            
            if ($response->successful()) {
                $categoriasData = $response->json();
                
                $responseProductos = Http::get($this->apiBaseUrl . '/productos');
                $productosData = $responseProductos->successful() ? $responseProductos->json() : [];
                
                $categorias = collect($categoriasData)->map(function ($categoria) use ($productosData) {
                    $productosCategoria = collect($productosData)->filter(function($prod) use ($categoria) {
                        return isset($prod['Id Categoria Producto:']) && 
                               $prod['Id Categoria Producto:'] == $categoria['idCategoriaProducto'];
                    });
                    
                    return (object) [
                        'ID_CATEGORIA_PRODUCTO' => $categoria['idCategoriaProducto'],
                        'NOMBRE_CATEGORIAPRODUCTO' => $categoria['nombreCategoriaProducto'],
                        'productosActivos' => $productosCategoria
                    ];
                });
                
                return view('home.home', compact('categorias'));
            }
            
            $categorias = collect([]);
            return view('home.home', compact('categorias'));
            
        } catch (\Exception $e) {
            \Log::error('Error API: ' . $e->getMessage());
            $categorias = collect([]);
            return view('home.home', compact('categorias'));
        }
    }
}