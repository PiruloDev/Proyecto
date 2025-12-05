<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Inventario\RecetasService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;

class RecetasController extends Controller
{
    protected $service;

    public function __construct(RecetasService $service)
    {
        $this->service = $service;
    }

    /**
     * Muestra el listado de todas las recetas agrupadas por producto.
     */
    public function index()
    {
        $response = $this->service->obtenerTodasLasRecetas();

        if (!$response['success']) {
            $errorMessage = $response['error'] ?? 'Error desconocido al obtener las recetas.';
            return view('inventarioviews.recetas.index', ['recetas' => []])
                   ->with('error', 'Error al cargar recetas: ' . $errorMessage);
        }
        
        // $recetas ya viene agrupado por idProducto desde el Service
        $recetas = $response['data'] ?? [];
        
        return view('inventarioviews.recetas.index', compact('recetas'));
    }

    /**
     * Muestra el detalle de una receta específica por ID de Producto.
     */
    public function show(int $idProducto)
    {
        $response = $this->service->obtenerRecetaPorIdProducto($idProducto);

        if (!$response['success']) {
            return Redirect::route('recetas.index')->with('error', $response['error']);
        }
        
        // El API devuelve una lista de detalles (RecetaProducto)
        $detalles = $response['data'];
        
        return view('inventarioviews.recetas.show', compact('detalles', 'idProducto'));
    }

    /**
     * Crea una nueva receta. (POST /recetas/store)
     */
    public function store(Request $request)
    {
        // Validación del encabezado y de los detalles del DTO RecetaRequest
        $request->validate([
            'idProducto' => 'required|integer|min:1|unique:recetas,ID_PRODUCTO', // Asume una tabla 'recetas' en la DB local para validar unicidad
            'ingredientes' => 'required|array|min:1',
            'ingredientes.*.idIngrediente' => 'required|integer',
            'ingredientes.*.cantidadNecesaria' => 'required|numeric|min:0.001',
            'ingredientes.*.idUnidad' => 'required|integer',
        ]);
        
        // El payload debe coincidir con el DTO RecetaRequest de Spring
        $payload = [
            'idProducto' => (int)$request->input('idProducto'),
            // Los detalles ya vienen en el formato correcto
            'ingredientes' => $request->input('ingredientes') 
        ];

        $response = $this->service->crearReceta($payload);

        if ($response['success']) {
            return Redirect::route('recetas.index')->with('success', $response['mensaje']);
        }

        return Redirect::back()->withInput()->with('error', 'Fallo al crear receta: ' . $response['error']);
    }

    /**
     * Actualiza una receta existente. (PUT /recetas/update/{idProducto})
     */
    public function update(Request $request, int $idProducto)
    {
        // La actualización de Spring borra y vuelve a insertar los detalles,
        // por lo que se debe validar la lista completa de ingredientes.
        $request->validate([
            'ingredientes' => 'required|array|min:1',
            'ingredientes.*.idIngrediente' => 'required|integer',
            'ingredientes.*.cantidadNecesaria' => 'required|numeric|min:0.001',
            'ingredientes.*.idUnidad' => 'required|integer',
        ]);
        
        // El payload solo necesita la lista de ingredientes para la actualización
        $payload = [
            'ingredientes' => $request->input('ingredientes')
        ];

        $response = $this->service->actualizarReceta($idProducto, $payload);

        if ($response['success']) {
            return Redirect::route('recetas.index')->with('success', $response['mensaje']);
        }

        return Redirect::back()->withInput()->with('error', 'Fallo al actualizar receta: ' . $response['error']);
    }

    /**
     * Elimina una receta. (DELETE /recetas/delete/{idProducto})
     */
    public function destroy(int $idProducto)
    {
        $response = $this->service->eliminarReceta($idProducto);

        if ($response['success']) {
            return Redirect::route('recetas.index')->with('success', $response['mensaje']);
        }
        
        return Redirect::back()->with('error', 'No se pudo eliminar la receta: ' . $response['error']);
    }
}