<?php

namespace App\Services\Reportes;

use App\Services\ApiService;
use Illuminate\Support\Facades\Log;

class UsuariosRegistradosService
{
    protected $apiService;

    public function __construct()
    {
        $this->baseUrl = env('API_BASE_URL', 'http://32.193.167.191:8080'); 
    }

    /**
     * Obtener los usuarios registrados desde la API de Spring Boot
     *
     * @return array 
     */
    public function obtenerUsuariosRegistrados(): array
    {
        try {
            $response = $this->apiService->get('/reporte/usuarios');

            if ($response['success'] && isset($response['data'])) {
                return $response['data'];
            }

            Log::warning('No se pudieron obtener los usuarios registrados', $response);
            return [];
        } catch (\Exception $e) {
            Log::error('Error al obtener usuarios registrados: ' . $e->getMessage());
            return [];
        }
    }
}