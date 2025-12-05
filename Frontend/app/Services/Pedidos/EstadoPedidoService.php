<?php

namespace App\Services\Pedidos;

use App\Models\EstadoPedido;

class EstadoPedidoService
{
    public function obtenerEstados()
    {
        return EstadoPedido::all();
    }

    public function obtenerEstadoPorId($id)
    {
        return EstadoPedido::find($id);
    }

    public function crearEstado(array $data)
    {
        return EstadoPedido::create($data);
    }

    public function actualizarEstado($id, array $data)
    {
        $estado = EstadoPedido::find($id);

        if (!$estado) {
            return false;
        }

        return $estado->update($data);
    }

    public function eliminarEstado($id)
    {
        $estado = EstadoPedido::find($id);

        if (!$estado) {
            return false;
        }

        return $estado->delete();
    }
}
