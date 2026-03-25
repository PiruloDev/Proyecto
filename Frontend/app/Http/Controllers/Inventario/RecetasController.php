<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Services\Inventario\RecetasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecetasController extends Controller
{
    protected $recetasService;

    public function __construct(RecetasService $recetasService)
    {
        $this->recetasService = $recetasService;
    }

    // ─── Método privado centralizado para lista-modal ───────────────
    private function obtenerIngredientesModal(): array
    {
        try {
            $response = Http::timeout(5)->get('http://32.193.167.191:8080/recetas/lista-modal');
            return $response->successful() ? ($response->json() ?? []) : [];
        } catch (\Exception $e) {
            Log::error('RecetasController - lista-modal: ' . $e->getMessage());
            return [];
        }
    }

    public function index()
    {
        // Recetas
        try {
            $resRecetas = $this->recetasService->obtenerTodasLasRecetas();
            $recetas    = $resRecetas['success'] ? $resRecetas['data'] : [];
            $errorRecetas = $resRecetas['success'] ? null : ($resRecetas['error'] ?? 'Error desconocido');
        } catch (\Exception $e) {
            $recetas      = [];
            $errorRecetas = 'Error al obtener recetas';
            Log::error('RecetasController@index recetas: ' . $e->getMessage());
        }

        // Productos — la API devuelve JSON array, no objetos Eloquent
        try {
            $responseProd = Http::timeout(5)->get('http://32.193.167.191:8080/productos');
            $productos    = $responseProd->successful() ? ($responseProd->json() ?? []) : [];
        } catch (\Exception $e) {
            $productos = [];
            Log::error('RecetasController@index productos: ' . $e->getMessage());
        }

        // Ingredientes para modal (centralizado)
        $ingredientesParaModal = $this->obtenerIngredientesModal();

        return view('inventarioviews.recetas.index', [
            'recetas'      => $recetas,
            'productos'    => $productos,
            'ingredientes' => $ingredientesParaModal,
            'error'        => $errorRecetas ?? null,
        ]);
    }

    public function show($idProducto)
    {
        if (!is_numeric($idProducto)) {
            return redirect()->route('recetas.index')->with('error', 'ID de producto inválido');
        }

        try {
            $res = $this->recetasService->obtenerRecetaPorIdProducto((int) $idProducto);
        } catch (\Exception $e) {
            Log::error('RecetasController@show: ' . $e->getMessage());
            return redirect()->route('recetas.index')->with('error', 'Error al obtener la receta');
        }

        // Si la receta no existe, redirigir con mensaje claro
        if (!$res['success']) {
            return redirect()->route('recetas.index')->with('error', $res['error'] ?? 'Receta no encontrada');
        }

        // Protección extra: si data viene vacío (Spring retornó 404 vacío)
        if (empty($res['data'])) {
            return redirect()->route('recetas.index')->with('error', 'No se encontraron ingredientes para esta receta');
        }

        $ingredientes = $this->obtenerIngredientesModal();

        return view('inventarioviews.recetas.show', [
            'detalles'     => $res['data'],
            'idProducto'   => (int) $idProducto,
            'ingredientes' => $ingredientes,
        ]);
    }

    public function store(Request $request)
    {
        // Validación básica antes de llamar al service
        if (empty($request->input('idProducto'))) {
            return back()->with('error', 'Debe seleccionar un producto');
        }

        if (empty($request->input('detalles'))) {
            return back()->with('error', 'Debe agregar al menos un ingrediente');
        }

        try {
            $res = $this->recetasService->crearReceta($request->all());
        } catch (\Exception $e) {
            Log::error('RecetasController@store: ' . $e->getMessage());
            return back()->with('error', 'Error inesperado al crear la receta');
        }

        if ($res['success']) {
            return redirect()->route('recetas.index')->with('success', 'Receta creada correctamente');
        }

        return back()->with('error', $res['error'] ?? 'Error al crear receta');
    }

    public function update(Request $request, $idProducto)
    {
        if (!is_numeric($idProducto)) {
            return back()->with('error', 'ID de producto inválido');
        }

        if (empty($request->input('detalles'))) {
            return back()->with('error', 'Debe agregar al menos un ingrediente');
        }

        try {
            $res = $this->recetasService->actualizarReceta((int) $idProducto, $request->all());
        } catch (\Exception $e) {
            Log::error('RecetasController@update: ' . $e->getMessage());
            return back()->with('error', 'Error inesperado al actualizar la receta');
        }

        if ($res['success']) {
            return redirect()
                ->route('recetas.show', (int) $idProducto)
                ->with('success', 'Receta actualizada correctamente');
        }

        return back()->with('error', $res['error'] ?? 'Error al actualizar la receta');
    }

    public function destroy($idProducto)
    {
        if (!is_numeric($idProducto)) {
            return back()->with('error', 'ID de producto inválido');
        }

        try {
            $res = $this->recetasService->eliminarReceta((int) $idProducto);
        } catch (\Exception $e) {
            Log::error('RecetasController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Error inesperado al eliminar la receta');
        }

        if ($res['success']) {
            return redirect()->route('recetas.index')->with('success', 'Receta eliminada correctamente');
        }

        return back()->with('error', 'No se pudo eliminar la receta');
    }
}