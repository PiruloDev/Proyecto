<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Inventario\ProduccionService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;

class ProduccionController extends Controller
{
    protected $service;

    public function __construct(ProduccionService $service)
    {
        $this->service = $service;
    }

    /**
     * Muestra el historial de registros de producción. (GET /produccion)
     */
    public function index()
    {
        $response = $this->service->obtenerHistorial();

        if (!$response['success']) {
            $errorMessage = $response['error'] ?? 'Error desconocido al obtener el historial.';
            return view('inventarioviews.produccion.index', ['historial' => []])
                   ->with('error', 'Error al cargar historial de producción: ' . $errorMessage);
        }
        
        $historial = $response['data'] ?? [];
        
        return view('inventarioviews.produccion.index', compact('historial'));
    }

    /**
     * Registra una nueva producción. (POST /produccion/store)
     */
    public function store(Request $request)
    {
        // Validación de los campos obligatorios del DTO ProduccionRequest
        $request->validate([
            'idProducto' => 'required|integer|min:1',
            // Usamos 'numeric' para BigDecimal y permitimos valores fraccionarios
            'cantidadProducida' => 'required|numeric|min:0.01', 
            // Opcional, pero si se envía debe ser un array válido
            'ingredientesDescontados' => 'nullable|array', 
            // Validación de los detalles anidados (si se envían)
            'ingredientesDescontados.*.idIngrediente' => 'required_with:ingredientesDescontados|integer',
            'ingredientesDescontados.*.cantidadUsada' => 'required_with:ingredientesDescontados|numeric|min:0.01',
        ]);
        
        // El payload debe coincidir con el DTO ProduccionRequest de Spring
        $payload = [
            'idProducto' => (int)$request->input('idProducto'),
            'cantidadProducida' => (float)$request->input('cantidadProducida'), // Laravel envía float o string, Spring lo manejará como BigDecimal
            'ingredientesDescontados' => $request->input('ingredientesDescontados') // Array de IngredienteDescontado DTOs
        ];
        
        $response = $this->service->registrarProduccion($payload);

        if ($response['success']) {
            $idProduccion = $response['response']['idProduccion'] ?? 'N/A';
            $mensaje = $response['response']['mensaje'] ?? 'Producción registrada con éxito.';
            
            return Redirect::route('produccion.index')->with('success', $mensaje . ' (ID: ' . $idProduccion . ')');
        }

        // Si falla, el service devuelve el mensaje de error del API de Spring
        return Redirect::back()->withInput()->with('error', 'Fallo al registrar producción: ' . $response['error']);
    }

    /**
     * Elimina un registro de producción y revierte los cambios de inventario. (DELETE /produccion/delete/{id})
     */
    public function destroy(int $id)
    {
        $response = $this->service->eliminarProduccion($id);

        if ($response['success']) {
            return Redirect::route('produccion.index')->with('success', $response['response']);
        }
        
        return Redirect::back()->with('error', 'No se pudo eliminar la producción: ' . $response['error']);
    }
}