<?php

namespace App\Services\Inventario;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecetasService
{
    protected $baseUrl;
    protected $path = '/inventario/recetas';

    public function __construct()
    {
        $this->baseUrl = env('API_BASE_URL', 'http://localhost:8080'); 
    }

    protected function getApiClient()
    {
        return Http::baseUrl($this->baseUrl)->withHeaders([
             'Accept' => 'application/json',
             'Content-Type' => 'application/json',
        ]);
    }
    
    // =========================================================================
    // GET /inventario/recetas - Obtener Todas Las Recetas
    // =========================================================================
    
    public function obtenerTodasLasRecetas(): array
    {
        try {
            // El API devuelve una lista plana de RecetaProducto, que agrupa los detalles.
            $response = $this->getApiClient()->get($this->path); 

            if ($response->successful()) {
                // Agrupamos por ID_PRODUCTO para mejor manejo en la vista
                $recetasDetalles = $response->json();
                $recetasAgrupadas = [];
                
                foreach ($recetasDetalles as $detalle) {
                    $idProducto = $detalle['idProducto'];
                    if (!isset($recetasAgrupadas[$idProducto])) {
                        $recetasAgrupadas[$idProducto] = [
                            'idProducto' => $idProducto,
                            // Se asume que el nombre del producto se podría obtener de otro servicio
                            'nombreProducto' => 'Producto ID ' . $idProducto, 
                            'detalles' => []
                        ];
                    }
                    $recetasAgrupadas[$idProducto]['detalles'][] = $detalle;
                }

                return [
                    'success' => true, 
                    'data' => array_values($recetasAgrupadas) // Devolvemos el array de recetas agrupadas
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Error desconocido al obtener recetas (' . $response->status() . ')'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'No se pudo conectar con el servidor de la API: ' . $e->getMessage()
            ];
        }
    }

    // =========================================================================
    // GET /inventario/recetas/producto/{idProducto} - Obtener Receta Por Producto
    // =========================================================================

    public function obtenerRecetaPorIdProducto(int $idProducto): array
    {
        try {
            $response = $this->getApiClient()->get("{$this->path}/producto/{$idProducto}");

            if ($response->successful()) {
                // Devuelve una lista de detalles (RecetaProducto)
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            if ($response->status() == 404) {
                return ['success' => false, 'error' => 'Receta no encontrada para el producto ID ' . $idProducto];
            }
            
            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Error al obtener la receta (' . $response->status() . ')'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Fallo de conexión al buscar receta: ' . $e->getMessage()
            ];
        }
    }
    
    // =========================================================================
    // POST /inventario/recetas - Crear Receta
    // =========================================================================

    public function crearReceta(array $data): array
    {
        try {
            // Spring espera el formato de RecetaRequest (idProducto y lista de IngredientesReceta)
            $response = $this->getApiClient()->post($this->path, $data);

            if ($response->successful() && $response->status() === 201) {
                return [
                    'success' => true,
                    'mensaje' => $response->json()['mensaje'] ?? 'Receta creada con éxito.'
                ];
            }
            
            $errorBody = $response->json();
            $errorMessage = $errorBody['error'] ?? 'Error al crear la receta (' . $response->status() . ')';

            return [
                'success' => false,
                'error' => $errorMessage
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Fallo de conexión al intentar crear la receta: ' . $e->getMessage()
            ];
        }
    }

    // =========================================================================
    // PUT /inventario/recetas/producto/{idProducto} - Actualizar Receta
    // =========================================================================

    public function actualizarReceta(int $idProducto, array $data): array
    {
        try {
            // Spring espera el formato de RecetaRequest (lista de IngredientesReceta) en el body
            $response = $this->getApiClient()->put("{$this->path}/producto/{$idProducto}", $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'mensaje' => $response->json()['mensaje'] ?? 'Receta actualizada con éxito.'
                ];
            }
            
            $errorBody = $response->json();
            $errorMessage = $errorBody['error'] ?? 'Error al actualizar la receta (' . $response->status() . ')';

            return [
                'success' => false,
                'error' => $errorMessage
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Fallo de conexión al intentar actualizar la receta: ' . $e->getMessage()
            ];
        }
    }

    // =========================================================================
    // DELETE /inventario/recetas/{idProducto} - Eliminar Receta
    // =========================================================================

    public function eliminarReceta(int $idProducto): array
    {
        try {
            $response = $this->getApiClient()->delete("{$this->path}/{$idProducto}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'mensaje' => $response->json()['mensaje'] ?? 'Receta eliminada con éxito.'
                ];
            }
            
            $errorBody = $response->json();
            $errorMessage = $errorBody['error'] ?? 'Error al eliminar la receta (' . $response->status() . ')';

            return [
                'success' => false,
                'error' => $errorMessage
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Fallo de conexión al intentar eliminar la receta: ' . $e->getMessage()
            ];
        }
    }
}