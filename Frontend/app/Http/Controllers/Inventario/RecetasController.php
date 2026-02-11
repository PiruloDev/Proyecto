<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Inventario\RecetasService;
use App\Services\Productos\ProductoService; 
use App\Services\Inventario\IngredientesService; // Asumiendo este nombre
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;

class RecetasController extends Controller
{
    protected $recetasService;
    protected $productoService;
    protected $ingredienteService;

    /**
     * Inyección de servicios necesarios.
     */
    public function __construct(
        RecetasService $recetasService,
        ProductoService $productoService,
        IngredientesService $ingredienteService
    ) {
        $this->recetasService = $recetasService;
        $this->productoService = $productoService;
        $this->ingredienteService = $ingredienteService;
    }

    /**
     * Listado de recetas y catálogos para creación.
     */
    public function index()
    {
        // 1. Obtener las recetas del backend (Spring)
        $response = $this->recetasService->obtenerTodasLasRecetas();
        $recetas = $response['data'] ?? [];

        // 2. Obtener Productos del Service local (para el select del modal)
        $productos = $this->productoService->obtenerProductos();

        // 3. Obtener Ingredientes del Service (para el detalle dinámico)
        $resIng = $this->ingredienteService->obtenerIngredientes();
        $ingredientesCatalogo = $resIng['data'] ?? [];

        if (!$response['success']) {
            Log::error('Error al cargar recetas: ' . ($response['error'] ?? 'Sin detalle'));
            return view('inventarioviews.recetas.index', compact('recetas', 'productos', 'ingredientesCatalogo'))
                   ->with('error', 'Error al sincronizar con el servidor de producción.');
        }
        
        return view('inventarioviews.recetas.index', compact('recetas', 'productos', 'ingredientesCatalogo'));
    }

    public function show(int $idProducto)
    {
        $response = $this->recetasService->obtenerRecetaPorIdProducto($idProducto);

        if (!$response['success']) {
            return Redirect::route('recetas.index')->with('error', $response['error']);
        }
        
        $detalles = $response['data'];
        return view('inventarioviews.recetas.show', compact('detalles', 'idProducto'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idProducto' => 'required|integer|min:1', 
            'ingredientes' => 'required|array|min:1',
            'ingredientes.*.idIngrediente' => 'required|integer',
            'ingredientes.*.cantidadNecesaria' => 'required|numeric|min:0.001',
            'ingredientes.*.idUnidad' => 'required|integer',
        ]);
        
        $payload = [
            'idProducto' => (int)$request->input('idProducto'),
            'ingredientes' => $request->input('ingredientes') 
        ];

        $response = $this->recetasService->crearReceta($payload);

        if ($response['success']) {
            return Redirect::route('recetas.index')->with('success', $response['mensaje']);
        }

        return Redirect::back()->withInput()->with('error', 'Fallo al crear receta: ' . $response['error']);
    }

    public function update(Request $request, int $idProducto)
    {
        $request->validate([
            'ingredientes' => 'required|array|min:1',
            'ingredientes.*.idIngrediente' => 'required|integer',
            'ingredientes.*.cantidadNecesaria' => 'required|numeric|min:0.001',
            'ingredientes.*.idUnidad' => 'required|integer',
        ]);
        
        $payload = ['ingredientes' => $request->input('ingredientes')];
        $response = $this->recetasService->actualizarReceta($idProducto, $payload);

        if ($response['success']) {
            return Redirect::route('recetas.index')->with('success', $response['mensaje']);
        }

        return Redirect::back()->withInput()->with('error', 'Fallo al actualizar receta: ' . $response['error']);
    }

    public function destroy(int $idProducto)
    {
        $response = $this->recetasService->eliminarReceta($idProducto);

        if ($response['success']) {
            return Redirect::route('recetas.index')->with('success', $response['mensaje']);
        }
        
        return Redirect::back()->with('error', 'No se pudo eliminar: ' . $response['error']);
    }
}