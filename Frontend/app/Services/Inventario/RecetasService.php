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
        $this->baseUrl = env('API_BASE_URL', 'http://32.193.167.191:8080'); 
    }

    protected function getApiClient()
    {
        return Http::baseUrl($this->baseUrl)->withHeaders([
             'Accept' => 'application/json',
             'Content-Type' => 'application/json',
        ]);
    }
    
    public function obtenerTodasLasRecetas(): array
    {
        try {
            $response = $this->getApiClient()->get("{$this->path}/optimizadas"); 

            if ($response->successful()) {
                $recetasDetalles = $response->json();
                $recetasAgrupadas = [];
                
                foreach ($recetasDetalles as $detalle) {
                    $idProducto = $detalle['idProducto'];
                    if (!isset($recetasAgrupadas[$idProducto])) {
                        $recetasAgrupadas[$idProducto] = [
                            'idProducto' => $idProducto,
                            'nombreProducto' => $detalle['nombreProducto'] ?? 'Producto ' . $idProducto, 
                            'detalles' => []
                        ];
                    }
                    $recetasAgrupadas[$idProducto]['detalles'][] = $detalle;
                }

                return [
                    'success' => true, 
                    'data' => array_values($recetasAgrupadas)
                ];
            }

            return ['success' => false, 'error' => 'Error al obtener recetas del servidor'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function obtenerRecetaPorIdProducto(int $idProducto): array
    {
        try {
            $response = $this->getApiClient()->get("{$this->path}/optimizadas/producto/{$idProducto}");

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return ['success' => false, 'error' => 'Receta no encontrada'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function crearReceta(array $data): array
    {
        try {
            $payload = [
                'idProducto' => (int) $data['idProducto'],
                'ingredientes' => array_map(function($detalle) {
                    return [
                        'idIngrediente'    => (int) $detalle['idIngrediente'],
                        'cantidadRequerida' => (float) $detalle['cantidadRequerida'],
                        'idUnidad'         => (int) $detalle['idUnidad'],
                    ];
                }, $data['detalles'] ?? [])
            ];

            Log::info('Payload enviado a Spring Boot:', $payload);

            $response = $this->getApiClient()->post($this->path, $payload);
            return [
                'success' => $response->successful(),
                'mensaje' => $response->json()['mensaje'] ?? 'Operación realizada'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function actualizarReceta(int $idProducto, array $data): array
{
    try {
        $payload = [
            'idProducto' => $idProducto,
            'ingredientes' => array_map(function($detalle) {
                return [
                    'idIngrediente'     => (int) $detalle['idIngrediente'],
                    'cantidadRequerida' => (float) $detalle['cantidadRequerida'],
                    'idUnidad'          => (int) $detalle['idUnidad'],
                ];
            }, $data['detalles'] ?? [])
        ];

        $response = $this->getApiClient()->put("{$this->path}/producto/{$idProducto}", $payload);

        return [
            'success' => $response->successful(),
            'mensaje' => $response->json()['mensaje'] ?? 'Receta actualizada'
        ];
    } catch (\Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

    public function eliminarReceta(int $idProducto): array
    {
        try {
            $response = $this->getApiClient()->delete("{$this->path}/{$idProducto}");
            return ['success' => $response->successful()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}