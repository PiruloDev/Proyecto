<?php

namespace App\Services\Pedidos;

use App\Models\Pedidos;

class PedidosService
{
    public function obtenerPedidos()
    {
        return Pedidos::all();
    }

    public function obtenerPedidoPorId($id)
    {
        return Pedidos::find($id);
    }

    public function agregarPedido(array $data)
    {
        return Pedidos::create($data);
    }

    public function actualizarPedido($id, array $data)
    {
        $pedido = Pedidos::find($id);

        if (!$pedido) {
            return false;
        }

        return $pedido->update($data);
    }

    public function eliminarPedido($id)
    {
        $pedido = Pedidos::find($id);

        if (!$pedido) {
            return false;
        }

        return $pedido->delete();
    }
}
