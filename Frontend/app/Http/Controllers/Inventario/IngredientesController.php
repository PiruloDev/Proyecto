<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Inventario\IngredientesService;
use Illuminate\Support\Facades\Redirect; // Importar Redirect

class IngredientesController extends Controller
{
    protected $ingredientesService;

    public function __construct(IngredientesService $ingredientesService)
    {
        $this->ingredientesService = $ingredientesService;
    }

    // ===========================================================
    // LISTAR INGREDIENTES (CORREGIDO)
    // ===========================================================

    public function index()
    {
        // 1. Llama al servicio, que devuelve el array ['success' => bool, 'data' => array|null, 'error' => string|null]
        $response = $this->ingredientesService->obtenerIngredientes();

        // 2. Verifica si la operación fue exitosa
        if (!$response['success']) {
            // Manejar el error: redirigir y mostrar un mensaje de alerta.
            $errorMessage = $response['error'] ?? 'Error desconocido al obtener ingredientes.';
            // Retorna una redirección a la página anterior con el error.
            return Redirect::back()->with(['error' => $errorMessage]); 
        }

        // 3. Si es exitosa, extrae *solamente* el array de ingredientes (la clave 'data')
        $ingredientes = $response['data'] ?? [];
        
        // Se asegura de que sea un array para evitar cualquier error de tipo si la API devolvió nulo o un objeto simple
        if (!is_array($ingredientes)) {
            $ingredientes = [];
        }
        
        // Pasa SOLAMENTE la lista de ingredientes a la vista
        return view('inventarioviews.ingredientes.index', compact('ingredientes'));
    }

    // ===========================================================
    // CREAR INGREDIENTE
    // ===========================================================

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombreIngrediente' => 'required|string|max:100',
            'cantidadIngrediente' => 'required|numeric',
            'fechaVencimiento' => 'required|date',
            'idProveedor' => 'required|integer',
            'idCategoria' => 'required|integer',
            'referenciaIngrediente' => 'required|string|max:50',
            // Asegúrate de incluir fechaEntregaIngrediente si es requerido
        ]);

        $response = $this->ingredientesService->agregarIngredientes($validated);

        if ($response['success']) {
            return back()->with('success', 'Ingrediente agregado correctamente.');
        }

        return back()->with('error', 'Error al crear ingrediente: '.$response['error']);
    }

    // ===========================================================
    // ACTUALIZAR INGREDIENTE
    // ===========================================================

    public function update($id, Request $request)
    {
        $validated = $request->validate([
            'nombreIngrediente' => 'required|string|max:100',
            'cantidadIngrediente' => 'required|numeric',
            'fechaVencimiento' => 'required|date',
            'idProveedor' => 'required|integer',
            'idCategoria' => 'required|integer',
            'referenciaIngrediente' => 'required|string|max:50',
        ]);

        $response = $this->ingredientesService->actualizarIngrediente($id, $validated);

        if ($response['success']) {
            return back()->with('success', 'Ingrediente actualizado correctamente.');
        }

        return back()->with('error', 'Error al actualizar: '.$response['error']);
    }

    // ============================================================
    // ACTUALIZAR SOLO CANTIDAD
    // ============================================================

    public function updateCantidad($id, Request $request)
    {
        $validated = $request->validate([
            'cantidadIngrediente' => 'required|numeric',
        ]);

        $response = $this->ingredientesService->actualizarCantidadIngrediente(
            $id,
            ['cantidadIngrediente' => $validated['cantidadIngrediente']]
        );

        if ($response['success']) {
            return back()->with('success', 'Cantidad actualizada correctamente.');
        }

        return back()->with('error', 'Error al actualizar cantidad: '.$response['error']);
    }

    // ============================================================
    // ELIMINAR INGREDIENTE
    // ============================================================

    public function destroy($id)
    {
        $response = $this->ingredientesService->eliminarIngrediente($id);

        if ($response['success']) {
            return back()->with('success', 'Ingrediente eliminado correctamente.');
        }

        return back()->with('error', 'Error al eliminar: '.$response['error']);
    }
}