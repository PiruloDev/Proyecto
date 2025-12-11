<?php

namespace App\Models\Productos;

use Illuminate\Database\Eloquent\Model;

class ProductosAdmin extends Model
{
    protected $table = 'productos';

    protected $primaryKey = 'ID_PRODUCTO';

    public $timestamps = false;

    protected $fillable = [
        'NOMBRE_PRODUCTO',
        'ID_CATEGORIA_PRODUCTO',
        'DESCRIPCION_PRODUCTO',
        'PRECIO_PRODUCTO',
        'PRODUCTO_STOCK_MIN',
        'ACTIVO'
    ];

    protected $casts = [
        'PRECIO_PRODUCTO' => 'decimal:2',
        'PRODUCTO_STOCK_MIN' => 'integer',
        'ACTIVO' => 'boolean'
    ];
}
