<?php

namespace App\Services\Productos;

use App\Models\Productos\ProductosAdmin;

class ProductoService
{
    public function obtenerProductos()
    {
        // Retorna la colección de productos
        return ProductosAdmin::all();
    }
    // ... otros métodos (crear, actualizar, eliminar)
}