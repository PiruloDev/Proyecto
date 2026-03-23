<?php


namespace App\Services\Productos;

use App\Models\Productos\ProductosAdmin;

class ProductoService
{
    public function __construct()
    {
        $this->baseUrl = env('API_BASE_URL', 'http://32.193.167.191:8080'); 
    }

    public function crearProducto($request)
    {
        return ProductosAdmin::create([
            'NOMBRE_PRODUCTO' => $request->nombre,
            'ID_CATEGORIA_PRODUCTO' => $request->categoria,
            'DESCRIPCION_PRODUCTO' => $request->descripcion ?? 'Sin descripción',
            'PRECIO_PRODUCTO' => $request->precio,
            'PRODUCTO_STOCK_MIN' => $request->stock,
            'ACTIVO' => $request->estado === 'activo' ? 1 : 0,
        ]);
    }

    public function actualizarProducto($request, $id)
    {
        $producto = ProductosAdmin::find($id);

        if (!$producto) return null;

        return $producto->update([
            'NOMBRE_PRODUCTO' => $request->nombre,
            'ID_CATEGORIA_PRODUCTO' => $request->categoria,
            'DESCRIPCION_PRODUCTO' => $request->descripcion ?? $producto->DESCRIPCION_PRODUCTO,
            'PRECIO_PRODUCTO' => $request->precio,
            'PRODUCTO_STOCK_MIN' => $request->stock,
            'ACTIVO' => $request->estado === 'activo' ? 1 : 0,
        ]);
    }

    public function eliminarProducto($id)
    {
        return ProductosAdmin::find($id)?->delete();
    }
}
