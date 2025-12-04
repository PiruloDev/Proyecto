<?php

namespace App\Http\Controllers\Pedidos;

use Illuminate\Http\Request;
use App\Services\Pedidos\PedidosService; // <--- ¡CORRECTO! Apunta al nuevo namespace

class PedidosController  
{
    protected $service;

    public function __construct(PedidosService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $pedidos = $this->service->obtenerPedidos();

        
        return view('pedidosviews.PedidosClientes.index', compact('pedidos')); 
    }

    public function create()
    {
        
        return view('pedidosviews.PedidosClientes.create'); 
    }

    public function store(Request $request)
    {
        
        $data = $request->validate([
            'ID_CLIENTE' => 'required|integer',
            'ID_EMPLEADO' => 'required|integer',
            'ID_ESTADO_PEDIDO' => 'required|integer',
            'TOTAL_PRODUCTO' => 'required|numeric',
        ]);

        
        $data['FECHA_INGRESO'] = now();

        $this->service->agregarPedido($data);

        return redirect()->route('pedidos.index')
                            ->with('success', 'Pedido creado correctamente');
    }

    public function edit($id)
    {
        $pedido = $this->service->obtenerPedidoPorId($id);

        return view('pedidosviews.PedidosClientes.edit', compact('pedido'));
    }

    public function update(Request $request, $id)
    {
        $pedido_original = $this->service->obtenerPedidoPorId($id);

        if (!$pedido_original) {
            return redirect()->back()->with('error', 'Pedido no encontrado');
        }

        $data = $request->validate([
            'ID_CLIENTE' => 'required|integer',
            'ID_EMPLEADO' => 'required|integer',
            'ID_ESTADO_PEDIDO' => 'required|integer',
            'TOTAL_PRODUCTO' => 'required|numeric',
        ]);

        $data['FECHA_INGRESO'] = $pedido_original['FECHA_INGRESO'];
        $data['FECHA_ENTREGA'] = $pedido_original['FECHA_ENTREGA'];

        $this->service->actualizarPedido($id, $data);

        return redirect()->route('pedidos.index')
                            ->with('success', 'Pedido con ID ' . $id . ' actualizado correctamente');
    }

    public function destroy($id)
    {
        $this->service->eliminarPedido($id);

        return redirect()->route('pedidos.index')
                          ->with('success', 'Pedido eliminado');
    }
}