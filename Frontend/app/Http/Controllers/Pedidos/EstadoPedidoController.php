<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pedidos\EstadoPedidoService;

class EstadoPedidoController extends Controller
{
    protected $service;

    public function __construct(EstadoPedidoService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $estados = $this->service->obtenerEstados();
        return view('pedidosviews.estados.index', compact('estados'));
    }

    public function create()
    {
        return view('pedidosviews.estados.create');
    }

    public function store(Request $request)
    {
        $this->service->crearEstado($request->all());
        return redirect()->route('estados.index');
    }

    public function edit($id)
    {
        $estado = $this->service->obtenerEstadoPorId($id);
        return view('pedidosviews.estados.edit', compact('estado'));
    }

    public function update(Request $request, $id)
    {
        $this->service->actualizarEstado($id, $request->all());
        return redirect()->route('estados.index');
    }

    public function destroy($id)
    {
        $this->service->eliminarEstado($id);
        return redirect()->route('estados.index');
    }
}
