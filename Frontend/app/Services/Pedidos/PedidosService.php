<?php

namespace App\Services\Pedidos;

use App\Models\Pedidos;

class PedidosService
{
    /**
     * Obtener todos los pedidos con su estado y cliente.
     */
    public function obtenerPedidosConEstadoYCliente()
{
    return Pedidos::with(['estado', 'cliente'])->get();
}

    /**
     * Obtener todos los pedidos (usado por el index original).
     */
    public function obtenerPedidos()
    {
        return Pedidos::with(['estado', 'cliente'])->get();
    }

    /**
     * Obtener un pedido por ID.
     */
    public function obtenerPedidoPorId($id)
    {
        return Pedidos::with(['estado', 'cliente'])->find($id);
    }

    /**
     * Crear un pedido
     */
    public function agregarPedido($data)
    {
        return Pedidos::create($data);
    }

    /**
     * Actualizar un pedido
     */
    public function actualizarPedido($id, $data)
    {
        $pedido = Pedidos::find($id);
        if ($pedido) {
            $pedido->update($data);
        }
        return $pedido;
    }

    /**
     * Eliminar un pedido
     */
    public function eliminarPedido($id)
    {
        $pedido = Pedidos::find($id);
        if ($pedido) {
            $pedido->delete();
        }
    }
}
