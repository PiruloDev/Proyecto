<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoPedido extends Model
{
    protected $table = 'Estado_Pedidos'; 

    protected $primaryKey = 'ID_ESTADO_PEDIDO';

    public $timestamps = false;

    protected $fillable = [
        'NOMBRE_ESTADO'
    ];
}
