<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedidos extends Model
{
    protected $table = 'pedidos'; 

    protected $primaryKey = 'ID_PEDIDO';

    public $timestamps = false; 

    protected $fillable = [
        'ID_CLIENTE',
        'ID_EMPLEADO',
        'ID_ESTADO_PEDIDO',
        'FECHA_INGRESO',
        'FECHA_ENTREGA',
        'TOTAL_PRODUCTO'
    ];

    protected $casts = [
        'FECHA_INGRESO' => 'datetime',
        'FECHA_ENTREGA' => 'datetime',
        'TOTAL_PRODUCTO' => 'decimal:2',
    ];
}
