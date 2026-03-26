<?php

namespace App\Services\Reportes;

use App\Services\ApiService;
use Illuminate\Support\Facades\Log;

class ProductosMasVendidosService
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }


    /**
     * Obtener los productos más vendidos desde la API de Spring Boot
     *
     * @param int $limite Cantidad máxima de productos a obtener
     * @return array Lista de productos más vendidos
     */
    public function obtenerProductosMasVendidos(int $limite = 10): array
    {
        try {
            $response = $this->apiService->get("/productos/mas-vendidos?limite={$limite}");

            if ($response['success'] && isset($response['data'])) {
                return $response['data'];
            }

            Log::warning('No se pudieron obtener productos más vendidos', $response);
            return [];
        } catch (\Exception $e) {
            Log::error('Error al obtener productos más vendidos: ' . $e->getMessage());
            return [];
        }
    }
}
