<?php

namespace App\Http\Controllers;

use App\Services\ProductoS\ProductoService;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    protected $service;

    public function __construct(ProductoService $productoService)
    {
        $this->service = $productoService;
    }

    public function index()
    {
        $productos = $this->service->obtenerTodos();
        return view('productos.index', compact('productos'));
    }

    public function store(Request $request)
    {
        $this->service->crear($request->all());
        return back()->with('success', 'Producto creado correctamente');
    }

    public function update(Request $request, $id)
    {
        $this->service->actualizar($id, $request->all());
        return back()->with('success', 'Producto actualizado correctamente');
    }

    public function destroy($id)
    {
        $this->service->eliminar($id);
        return back()->with('success', 'Producto eliminado');
    }
}
