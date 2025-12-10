<?php
namespace App\Models;

use App\Services\Pedidos\PedidosApiService;
use Illuminate\Database\Eloquent\Model;

class OrdenSalida extends Model
{
    protected $table = 'ordenes_salida';

    protected $primaryKey = 'id_factura'; 

    public $timestamps = false; 

    protected $fillable = [
        'idCliente',
        'idPedido',
        'fechaFacturacion',
        'totalFactura'
    ];

    protected $casts = [
        'fechaFacturacion' => 'datetime',
        'totalFactura' => 'decimal:2',
        'idCliente' => 'integer',
        'idPedido' => 'integer',
    ];

    
    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'idCliente', 'ID_CLIENTE');
    }

    
    public function pedido()
    {
        return $this->belongsTo(PedidosApiService::class, 'idPedido', 'ID_PEDIDO');
    }

    
    public function scopePorCliente($query, $idCliente)
    {
        return $query->where('idCliente', $idCliente);
    }

    
    public function scopePorRangoFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fechaFacturacion', [$fechaInicio, $fechaFin]);
    }

    
    public function scopeMasRecientes($query)
    {
        return $query->orderBy('fechaFacturacion', 'desc');
    }
}