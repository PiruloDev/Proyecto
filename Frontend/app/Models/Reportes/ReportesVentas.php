<?php

namespace App\Models\Reportes;

use Illuminate\Database\Eloquent\Model;

class ReportesVentas extends Model
{
    protected $table = 'ordenes_salida';
    protected $primaryKey = 'ID_FACTURA';
    public $timestamps = false;

    protected $fillable = [
        'ID_CLIENTE',
        'ID_PEDIDO',
        'FECHA_FACTURACION',
        'TOTAL_FACTURA'
    ];
}
