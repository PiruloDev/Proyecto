<?php

namespace App\Http\Controllers\Inventario;

use Illuminate\Http\Request;
use App\Services\Inventario\InventarioService; 
use Illuminate\Support\Facades\Validator;

class InventarioController extends Controller 
{
    protected $inventarioService;

    // Inyección de Dependencias
    public function __construct(InventarioService $inventarioService)
    {
        $this->inventarioService = $inventarioService;
    }

    public function index()
    {
        $ingredientes = $this->inventarioService->obtenerIngredientes();
        
        return view('inventarioviews.ingredientes.index', compact('ingredientes')); 
    }


    public function store(Request $request)
    {
        $request->validate([
            'idProveedor' => 'required|integer|min:1',
            'idCategoria' => 'required|integer|min:1',
            'nombreIngrediente' => 'required|string|max:255',
            'cantidadIngrediente' => 'required|integer|min:0',
            'fechaVencimiento' => 'nullable|date_format:Y-m-d', 
            'referenciaIngrediente' => 'required|string|max:255',
            'fechaEntregaIngrediente' => 'nullable|date_format:Y-m-d',
        ]);
        
        $resultado = $this->inventarioService->agregarIngredientes($request->all());
        
        if ($resultado["success"]) {
            return back()->with(['status' => 'Ingrediente agregado correctamente.', 'status_type' => 'success']);
        } else {
            $errorMsg = $resultado["error"] ?? 'Error desconocido al agregar ingrediente.';
            return back()->withInput()->with(['status' => "Error al agregar: $errorMsg", 'status_type' => 'danger']);
        }
    }

    /**
     * Procesa la solicitud para actualizar un ingrediente completo. (POST /ingredientes/update)
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|min:1',
            'idProveedor' => 'sometimes|integer|min:1',
            'idCategoria' => 'sometimes|integer|min:1',
            'nombreIngrediente' => 'sometimes|string|max:255',
            'cantidadIngrediente' => 'sometimes|integer|min:0',
            'fechaVencimiento' => 'nullable|date_format:Y-m-d',
            'referenciaIngrediente' => 'sometimes|string|max:255',
            'fechaEntregaIngrediente' => 'nullable|date_format:Y-m-d',
        ]);
        
        $resultado = $this->inventarioService->actualizarIngrediente($request->input('id'), $request->all());
        
        if ($resultado["success"]) {
            return back()->with(['status' => 'Ingrediente actualizado correctamente.', 'status_type' => 'success']);
        } else {
            $errorMsg = $resultado["error"] ?? 'Error desconocido al actualizar ingrediente.';
            return back()->withInput()->with(['status' => "Error al actualizar: $errorMsg", 'status_type' => 'danger']);
        }
    }

    /**
     * Procesa la solicitud para actualizar la cantidad. (POST /ingredientes/cantidad)
     */
    public function updateCantidad(Request $request)
{
    $request->validate([
        'id' => 'required|integer|min:1',
        'cantidadIngrediente' => 'required|integer|min:0',
    ]);
    
    $data = [
        'cantidadIngrediente' => (int) $request->input('cantidadIngrediente')
    ];
    
    $resultado = $this->inventarioService->actualizarCantidadIngrediente($request->input('id'), $data);
    
    if ($resultado["success"]) {
        return back()->with(['status' => 'Cantidad actualizada correctamente.', 'status_type' => 'success']);
    } else {
        $errorMsg = $resultado["error"] ?? 'Error al actualizar cantidad.';
        return back()->withInput()->with(['status' => "Error al actualizar: $errorMsg", 'status_type' => 'danger']);
    }
}

    /**
     * Procesa la solicitud para eliminar un ingrediente. (POST /ingredientes/delete)
     */
    public function destroy(Request $request) 
    {
        $request->validate(['id' => 'required|integer|min:1']);
        
        $id = $request->input('id');
        $resultado = $this->inventarioService->eliminarIngrediente($id);
        
        if ($resultado["success"]) {
            return back()->with(['status' => 'Ingrediente eliminado correctamente.', 'status_type' => 'success']);
        } else {
            $errorMsg = $resultado["error"] ?? 'Error desconocido al eliminar ingrediente.';
            return back()->withInput()->with(['status' => "Error al eliminar: $errorMsg", 'status_type' => 'danger']);
        }
    }

    public function indexCategorias()
{
    // Lógica futura para obtener categorías: $categorias = $this->inventarioService->obtenerCategorias();
    $categorias = []; // Placeholder temporal
    
    return view('inventarioviews.categorias.index', compact('categorias')); 
}

/**
 * Muestra el listado de Proveedores.
 * Corresponde a la ruta GET /proveedores
 */
public function indexProveedores()
{
    // Lógica futura para obtener proveedores: $proveedores = $this->inventarioService->obtenerProveedores();
    $proveedores = []; // Placeholder temporal
    
    // La vista es 'inventarioviews.proveedores.index'
    return view('inventarioviews.proveedores.index', compact('proveedores'));
}

/**
 * Muestra el listado de Detalle de Pedidos.
 * Corresponde a la ruta GET /detalle-pedidos
 */
public function indexDetallePedidos()
{
    // Lógica futura para obtener detalles de pedidos: $detalles = $this->inventarioService->obtenerDetallePedidos();
    $detalles = []; // Placeholder temporal
    
    // La vista es 'inventarioviews.detallepedidos.index'
    return view('inventarioviews.detallepedidos.index', compact('detalles'));
}

}