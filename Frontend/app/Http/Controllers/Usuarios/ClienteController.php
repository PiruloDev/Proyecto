<?php

namespace App\Http\Controllers\Usuarios;

use App\Http\Controllers\Controller;
use App\Services\ApiService;

class ClienteController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index()
    {
        $response = $this->apiService->get('/reporte/usuarios');

        $clientes = [];
        if ($response['success'] && isset($response['data'])) {
            // Filtrar solo clientes y extraer solo nombre, email y teléfono
            foreach ($response['data'] as $user) {
                if (isset($user['rol']) && strtolower($user['rol']) === 'cliente') {
                    $clientes[] = [
                        'nombre' => $user['nombre'] ?? $user['Nombre:'] ?? 'N/A',
                        'email' => $user['email'] ?? $user['Correo Electronico:'] ?? 'N/A',
                        'telefono' => $user['telefono'] ?? $user['Telefono:'] ?? 'N/A',
                    ];
                }
            }
        }

        return view('clientes.index', [
            'clientes' => $clientes,
            'error' => !$response['success'] ? $response['message'] : null
        ]);
    }
}
