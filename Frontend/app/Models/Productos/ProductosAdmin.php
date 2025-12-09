<?php

namespace App\Models\Productos;

use Illuminate\Database\Eloquent\Model;

class ProductosAdmin extends Model
{
    protected $table = 'productos'; 

    protected $fillable = [
        'nombre',
        'categoria', 
        'descripcion',
        'precio',
        'stock',
        'estado',
        'imagen'
    ];
     public $timestamps = true;
}