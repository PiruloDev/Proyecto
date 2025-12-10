<?php

namespace App\Models\Productos;

use Illuminate\Database\Eloquent\Model;

class ProductosAdmin extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'categoria_id',
        'estado'
    ];
}
