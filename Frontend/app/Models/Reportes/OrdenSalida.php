<?php

namespace App\Models\Reportes;

use Illuminate\Database\Eloquent\Model;
use App\Models\Clientes;

class OrdenSalida extends Model
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

    protected $casts = [
        'FECHA_FACTURACION' => 'datetime',
        'TOTAL_FACTURA' => 'decimal:2',
        'ID_CLIENTE' => 'integer',
        'ID_PEDIDO' => 'integer',
    ];

    // RELACIÓN CON CLIENTE 

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'ID_CLIENTE', 'ID_CLIENTE');
    }

    public function scopePorCliente($query, $idCliente)
    {
        return $query->where('ID_CLIENTE', $idCliente);
    }

    public function scopePorRangoFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('FECHA_FACTURACION', [$fechaInicio, $fechaFin]);
    }

    public function scopeMasRecientes($query)
    {
        return $query->orderBy('FECHA_FACTURACION', 'desc');
    }
}
