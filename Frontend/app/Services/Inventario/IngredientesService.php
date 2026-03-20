<?php

namespace App\Services\Inventario;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IngredientesService
{
    protected $baseUrl;

    public function __construct()
    {
        // Prioriza la URL del .env, usa localhost:8080 como respaldo
        $this->baseUrl = env('API_BASE_URL', 'http://32.193.167.191:8080'); 
    }

    /**
     * Cliente HTTP configurado para JSON
     */
    protected function getApiClient()
    {
        return Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->contentType('application/json');
    }
    
    /**
     * Filtra los datos para que coincidan EXACTAMENTE con el modelo Ingredientes.java
     */
    private function filterIngredienteData(array $data): array
    {
        $allowedKeys = [
            'idProveedor', 
            'idCategoria', 
            'idUnidadMedida', 
            'nombreIngrediente', 
            'referenciaIngrediente'
        ];
        
        // Extraemos solo los campos permitidos
        $filtered = array_intersect_key($data, array_flip($allowedKeys));

        // Aseguramos que los IDs sean tratados como enteros (Long en Java)
        if (isset($filtered['idProveedor'])) $filtered['idProveedor'] = (int)$filtered['idProveedor'];
        if (isset($filtered['idCategoria'])) $filtered['idCategoria'] = (int)$filtered['idCategoria'];
        if (isset($filtered['idUnidadMedida'])) $filtered['idUnidadMedida'] = (int)$filtered['idUnidadMedida'];
        
    return $filtered;
    }

    // --- MÉTODOS DE OBTENCIÓN ---

    public function obtenerIngredientesSimple(): array
    {
        try {
            $response = $this->getApiClient()->get('/ingredientes/cantidad');
            return $response->successful() 
                ? ['success' => true, 'data' => $response->json()]
                : ['success' => false, 'error' => 'Error API: ' . $response->status()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function obtenerIngredientes(): array
    {
        try {
            $response = $this->getApiClient()->get('/ingredientes/lista'); 
            return $response->successful()
                ? ['success' => true, 'data' => $response->json()]
                : ['success' => false, 'error' => 'Error al obtener lista'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // --- MÉTODOS DE CREACIÓN Y EDICIÓN (CRÍTICOS) ---

    public function agregarIngredientes(array $data): array
    {
        try {
            $payload = $this->filterIngredienteData($data); 

            // LOG PARA DEPURACIÓN: Revisa storage/logs/laravel.log
            Log::debug("Enviando a Spring Boot (Crear):", $payload);

            $response = $this->getApiClient()->post('/crearingrediente', $payload);

            if ($response->successful()) {
                return ['success' => true, 'response' => $response->body()];
            }

            Log::error("Spring Boot rechazó la creación:", ['status' => $response->status(), 'body' => $response->body()]);
            return ['success' => false, 'error' => $response->body() ?: 'Error ' . $response->status()];

        } catch (\Exception $e) {
            Log::error("Fallo de conexión en agregarIngredientes: " . $e->getMessage());
            return ['success' => false, 'error' => 'Error de conexión con el servidor backend'];
        }
    }

    public function actualizarIngrediente(int $id, array $data): array
    {
        try {
            $payload = $this->filterIngredienteData($data); 
            
            Log::debug("Enviando a Spring Boot (Actualizar ID $id):", $payload);

            $response = $this->getApiClient()->put("/ingrediente/{$id}", $payload);

            if ($response->successful()) {
                return ['success' => true, 'response' => $response->body()];
            }

            return ['success' => false, 'error' => 'No se pudo actualizar'];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function eliminarIngrediente(int $id): array
    {
        try {
            $response = $this->getApiClient()->delete("/ingrediente/{$id}");
            return $response->successful()
                ? ['success' => true, 'response' => $response->body()]
                : ['success' => false, 'error' => 'Error al eliminar'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // --- GESTIÓN DE STOCK ---

    public function ingresarStock(int $id, array $data): array
    {
        try {
            // Spring espera {"cantidadIngresada": valor}
            $payload = ['cantidadIngresada' => (float)$data['cantidadIngresada']];

            $response = $this->getApiClient()->post("/ingredientes/{$id}/ingreso", $payload);

            return $response->successful()
                ? ['success' => true, 'response' => $response->body()]
                : ['success' => false, 'error' => $response->body()];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Error de conexión stock'];
        }
    }
}