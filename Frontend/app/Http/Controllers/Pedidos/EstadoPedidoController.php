<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Pedidos\EstadoPedidoService;
use Exception;

class EstadoPedidoController extends Controller
{
    protected $service;

    public function __construct(EstadoPedidoService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        try {
            $estados = $this->service->obtenerEstados();
            return view('pedidosviews.estados.index', compact('estados'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar estados: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('pedidosviews.estados.create');
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'NOMBRE_ESTADO' => 'required|string|max:255',
        ]);
        
        
        $dataApi = [
            'nombre_ESTADO' => $request->input('NOMBRE_ESTADO')
        ];

        try {
            $this->service->crearEstado($dataApi);
            return redirect()->route('estados.index')
                             ->with('success', 'Estado de pedido creado correctamente.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()
                             ->with('error', 'Error al crear estado: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $estado = $this->service->obtenerEstadoPorId($id);
            return view('pedidosviews.estados.edit', compact('estado'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar estado para edición: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
       
        $request->validate([
            'NOMBRE_ESTADO' => 'required|string|max:255',
        ]);
        
        
        $dataApi = [
            'nombre_ESTADO' => $request->input('NOMBRE_ESTADO')
        ];
        
        try {
            $this->service->actualizarEstado($id, $dataApi);
            return redirect()->route('estados.index')
                             ->with('success', 'Estado de pedido actualizado correctamente.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()
                             ->with('error', 'Error al actualizar estado: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->eliminarEstado($id);
            return redirect()->route('estados.index')
                             ->with('success', 'Estado de pedido eliminado correctamente.');
        } catch (Exception $e) {
            return redirect()->back()
                             ->with('error', 'Error al eliminar estado: ' . $e->getMessage());
        }
    }
}