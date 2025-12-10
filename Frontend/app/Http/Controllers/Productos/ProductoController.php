<?php

namespace App\Http\Controllers\Productos;

use App\Http\Controllers\Controller;
use App\Services\Productos\ProductoService;

class ProductoController extends Controller
{
    protected $productoService;

    public function __construct(ProductoService $productoService)
    {
        $this->productoService = $productoService;
    }

    public function index()
    {
        // Esto pasa la variable $productos a la vista
        $productos = $this->productoService->obtenerProductos();

        return view('Productos.index', compact('productos'));
    }
    
    // Si necesitas la función 'list' para llamadas AJAX de filtrado/búsqueda posterior,
    // puedes implementarla aquí, pero no es necesaria para la carga inicial.
    public function list()
    {
        return response()->json($this->productoService->obtenerProductos());
    }
    // ... otros métodos (store, update, destroy)
}