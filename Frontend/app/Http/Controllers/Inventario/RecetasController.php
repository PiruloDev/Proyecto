<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Services\Inventario\RecetasService;
use App\Services\Productos\ProductoService; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecetasController extends Controller
{
    protected $recetasService;
    protected $productosService;

    public function __construct(RecetasService $recetasService, ProductoService $productosService)
    {
        $this->recetasService = $recetasService;
        $this->productosService = $productosService;
    }

    public function index()
{
    // ✅ Recetas — si falla, carga vacío en lugar de redirigir
    $resRecetas = $this->recetasService->obtenerTodasLasRecetas();
    $recetas = $resRecetas['success'] ? $resRecetas['data'] : [];

    // ✅ Productos — ya tiene try/catch
    try {
        $productos = $this->productosService->obtenerProductos();
    } catch (\Exception $e) {
        $productos = [];
        Log::error('Error al obtener productos en RecetasController: ' . $e->getMessage());
    }

    // ✅ Ingredientes para modal — ya tiene try/catch
    try {
        $responseIng = Http::get('http://32.193.167.191:8080/recetas/lista-modal');
        $ingredientesParaModal = $responseIng->successful() ? $responseIng->json() : [];
    } catch (\Exception $e) {
        $ingredientesParaModal = [];
    }

    // ✅ Siempre carga la vista — muestra error como alerta si hubo fallo
    return view('inventarioviews.recetas.index', [
        'recetas'      => $recetas,
        'productos'    => $productos,
        'ingredientes' => $ingredientesParaModal,
        'error'        => $resRecetas['success'] ? null : $resRecetas['error'],
    ]);
}

    public function show($idProducto)
    {
        $res = $this->recetasService->obtenerRecetaPorIdProducto($idProducto);

        // ✅ Cargar ingredientes para el select del modal de edición
        try {
            $responseIng = Http::get('http://32.193.167.191:8080/recetas/lista-modal');
            $ingredientes = $responseIng->successful() ? $responseIng->json() : [];
        } catch (\Exception $e) {
            $ingredientes = [];
        }

        if ($res['success']) {
            return view('inventarioviews.recetas.show', [
                'detalles'     => $res['data'],
                'idProducto'   => $idProducto,
                'ingredientes' => $ingredientes, // ✅ ahora disponible en la vista
            ]);
        }

        return redirect()->route('recetas.index')->with('error', $res['error']);
    }

    // ✅ Método update que faltaba completamente
    public function update(Request $request, $idProducto)
    {
        $res = $this->recetasService->actualizarReceta($idProducto, $request->all());

        if ($res['success']) {
            return redirect()
                ->route('recetas.show', $idProducto)
                ->with('success', 'Receta actualizada correctamente');
        }

        return back()->with('error', $res['error'] ?? 'Error al actualizar la receta');
    }

    public function store(Request $request)
    {
        $res = $this->recetasService->crearReceta($request->all());
        if ($res['success']) {
            return redirect()->route('recetas.index')->with('success', 'Receta creada correctamente');
        }
        return back()->with('error', $res['error'] ?? 'Error al crear receta');
    }

    public function destroy($idProducto)
    {
        $res = $this->recetasService->eliminarReceta($idProducto);
        if ($res['success']) {
            return redirect()->route('recetas.index')->with('success', 'Receta eliminada');
        }
        return back()->with('error', 'No se pudo eliminar la receta');
    }
}