<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\CategoriaProducto;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Obtener categorías con productos activos
        $categorias = CategoriaProducto::with(['productosActivos' => function($query) {
            $query->where('STOCK_ACTUAL', '>', 0)
                  ->orderBy('NOMBRE_PRODUCTO', 'asc');
        }])
        ->whereHas('productosActivos', function($query) {
            $query->where('STOCK_ACTUAL', '>', 0);
        })
        ->orderBy('NOMBRE_CATEGORIAPRODUCTO', 'asc')
        ->get();

        // Devolver la vista con las categorías
        return view('home.home', compact('categorias'));
    }
}