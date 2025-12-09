<?php

namespace App\Services\Pedidos;

use App\Models\Pedidos;

class PedidosService
{
 
    public function obtenerPedidosConEstadoYCliente()
{
    return Pedidos::with(['estado', 'cliente'])->get();
}

    public function obtenerPedidos()
    {
        return Pedidos::with(['estado', 'cliente'])->get();
    }

    public function obtenerPedidoPorId($id)
    {
        return Pedidos::with(['estado', 'cliente'])->find($id);
    }

    public function agregarPedido($data)
    {
        return Pedidos::create($data);
    }

   
    public function actualizarPedido($id, $data)
    {
        $pedido = Pedidos::find($id);
        if ($pedido) {
            $pedido->update($data);
        }
        return $pedido;
    }

    
    public function eliminarPedido($id)
    {
        $pedido = Pedidos::find($id);
        if ($pedido) {
            $pedido->delete();
        }
    }
}
