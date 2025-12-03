<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Inventario\IngredientesService;

class IngredientesController extends Controller
{
    protected $ingredientesService;

    public function __construct(IngredientesService $ingredientesService)
    {
        $this->ingredientesService = $ingredientesService;
    }

    // ============================================================
    // LISTAR INGREDIENTES
    // ============================================================

    public function index()
    {
        $ingredientes = $this->ingredientesService->obtenerIngredientes();
        return view('inventarioviews.ingredientes.index', compact('ingredientes'));
    }

    // ============================================================
    // CREAR INGREDIENTE
    // ============================================================

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombreIngrediente' => 'required|string|max:100',
            'cantidadIngrediente' => 'required|numeric',
            'fechaVencimiento' => 'required|date',
            'idProveedor' => 'required|integer',
            'idCategoria' => 'required|integer',
            'referenciaIngrediente' => 'required|string|max:50',
        ]);

        $response = $this->ingredientesService->agregarIngredientes($validated);

        if ($response['success']) {
            return back()->with('success', 'Ingrediente creado correctamente.');
        }

        return back()->with('error', 'Error al crear ingrediente: '.$response['error']);
    }

    // ============================================================
    // ACTUALIZAR INGREDIENTE
    // ============================================================

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
