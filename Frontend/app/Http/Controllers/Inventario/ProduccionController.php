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

    public function index()
    {
        $response = $this->service->obtenerHistorial();
        $historial = $response['success'] ? ($response['data'] ?? []) : [];

        $recetasResponse = $this->service->obtenerRecetas();
        $recetas = $recetasResponse['success'] ? ($recetasResponse['data'] ?? []) : [];

        $productosConReceta = collect($recetas)
            ->unique('idProducto')
            ->values()
            ->toArray();

        if (!$response['success']) {
            return view('inventarioviews.produccion.index',
                        compact('historial', 'productosConReceta', 'recetas'))
                   ->with('error', $response['error'] ?? 'Error al cargar historial');
        }

        return view('inventarioviews.produccion.index',
                    compact('historial', 'productosConReceta', 'recetas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idProducto'       => 'required|integer|min:1',
            'cantidadProducida'=> 'required|numeric|min:0.01',
        ]);

        $payload = [
            'idProducto'       => (int)$request->input('idProducto'),
            'cantidadProducida'=> (float)$request->input('cantidadProducida'),
        ];

        $response = $this->service->registrarProduccion($payload);

        if ($response['success']) {
            $idProduccion = $response['response']['idProduccion'] ?? 'N/A';
            $mensaje      = $response['response']['mensaje'] ?? 'Producción registrada con éxito.';
            return Redirect::route('produccion.index')
                           ->with('success', $mensaje . ' (ID: ' . $idProduccion . ')');
        }

        return Redirect::back()->withInput()
                       ->with('error', 'Fallo al registrar: ' . $response['error']);
    }

    public function destroy(int $id)
    {
        $response = $this->service->eliminarProduccion($id);

        if ($response['success']) {
            return Redirect::route('produccion.index')
                           ->with('success', $response['response']);
        }

        return Redirect::back()
                       ->with('error', 'No se pudo eliminar: ' . $response['error']);
    }
}