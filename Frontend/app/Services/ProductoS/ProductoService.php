<?php

namespace App\Services;

use App\Models\Productos\ProductosAdmin;

class ProductoService
{
    public function obtenerTodos()
    {
        return ProductosAdmin::all();
    }

    public function buscar($id)
    {
        return ProductosAdmin::findOrFail($id);
    }

    public function crear(array $data)
    {
        return ProductosAdmin::create($data);
    }

    public function actualizar($id, array $data)
    {
        $producto = ProductosAdmin::findOrFail($id);
        $producto->update($data);

        return $producto;
    }

    public function eliminar($id)
    {
        $producto = ProductosAdmin::findOrFail($id);
        return $producto->delete();
    }
}
