<?php

namespace App\Http\Controllers\Productos;

use App\Http\Controllers\Controller;
use App\Models\Productos\ProductosAdmin;  
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = ProductosAdmin::all()->toArray(); 
        return view('productos.index', compact('productos'));
    }

    public function store(Request $request)
    {
        $producto = ProductosAdmin::create([
            'nombre' => $request->nombre,
            'categoria' => $request->categoria,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'stock' => $request->stock,
            'estado' => $request->estado,
            'imagen' => $request->imagen
        ]);

        return response()->json(['success' => true, 'producto' => $producto]);
    }

    public function update(Request $request, $id)
    {
        $producto = ProductosAdmin::findOrFail($id);
        $producto->update($request->all());

        return response()->json(['success' => true, 'producto' => $producto]);
    }

    public function destroy($id)
    {
        ProductosAdmin::destroy($id);
        return response()->json(['success' => true]);
    }
}