<?php

namespace App\Http\Controllers\Usuarios;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EmpleadoController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index()
    {
        // Obtener lista de empleados directamente desde el endpoint correcto
        $response = $this->apiService->get('/detalle/empleado');

        $empleados = [];
        if ($response['success'] && isset($response['data'])) {
            foreach ($response['data'] as $empleadoData) {
                // Normalizar los datos del empleado
                // La API devuelve claves con formato "Nombre:" con dos puntos
                $empleado = [
                    'id' => $empleadoData['Id:'] ?? $empleadoData['id'] ?? $empleadoData['ID'] ?? null,
                    'nombre' => $empleadoData['Nombre:'] ?? $empleadoData['nombre'] ?? 'N/A',
                    'email' => $empleadoData['Correo Electronico:'] ?? $empleadoData['email'] ?? 'N/A',
                    'telefono' => $empleadoData['Telefono:'] ?? $empleadoData['telefono'] ?? '',
                    'rol' => 'Empleado',
                    'estado' => $empleadoData['Estado del Empleado'] ?? 'ACTIVO',
                ];

                // Solo agregar empleados con ID válido
                if ($empleado['id'] !== null) {
                    $empleados[] = $empleado;
                }
            }
        }

        return view('empleados.index', [
            'empleados' => $empleados,
            'error' => !$response['success'] ? $response['message'] : null
        ]);
    }

    /**
     * Mostrar formulario para crear empleado
     */
    public function create()
    {
        return view('empleados.create');
    }

    /**
     * Almacenar nuevo empleado
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contrasena' => 'required|string|min:6',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'contrasena.required' => 'La contraseña es obligatoria',
            'contrasena.min' => 'La contraseña debe tener al menos 6 caracteres',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'nombre' => $request->nombre,
            'email' => $request->email,
            'contrasena' => $request->contrasena,
        ];

        $response = $this->apiService->post('/auth/registro/empleado', $data);

        if ($response['success']) {
            return redirect()->route('empleados.index')
                ->with('success', 'Empleado creado exitosamente');
        }

        return redirect()->back()
            ->with('error', $response['message'])
            ->withInput();
    }

    /**
     * Mostrar detalle de un empleado (redirige a editar)
     */
    public function show($id)
    {
        // Redirigir directamente a la vista de edición
        return redirect()->route('empleados.edit', $id);
    }

    /**
     * Mostrar formulario para editar empleado
     */
    public function edit($id)
    {
        // Obtener todos los empleados y buscar el que coincida con el ID
        $response = $this->apiService->get('/detalle/empleado');

        if (!$response['success'] || !isset($response['data'])) {
            return redirect()->route('empleados.index')
                ->with('error', 'Error al obtener datos de empleados');
        }

        // Buscar el empleado por ID
        $empleadoEncontrado = null;
        foreach ($response['data'] as $empleadoData) {
            $empleadoId = $empleadoData['Id:'] ?? $empleadoData['id'] ?? $empleadoData['ID'] ?? null;

            if ($empleadoId == $id) {
                $empleadoEncontrado = [
                    'id' => $empleadoId,
                    'nombre' => $empleadoData['Nombre:'] ?? $empleadoData['nombre'] ?? '',
                    'email' => $empleadoData['Correo Electronico:'] ?? $empleadoData['email'] ?? '',
                    'telefono' => $empleadoData['Telefono:'] ?? $empleadoData['telefono'] ?? '',
                ];
                break;
            }
        }

        if (!$empleadoEncontrado) {
            return redirect()->route('empleados.index')
                ->with('error', 'Empleado no encontrado (ID: ' . $id . ')');
        }

        return view('empleados.edit', ['empleado' => $empleadoEncontrado]);
    }

    public function update(Request $request, $id)
    {
        Log::info('Actualizando empleado', [
            'id' => $id,
            'id_type' => gettype($id),
            'data' => $request->except('contrasena')
        ]);

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contrasena' => 'nullable|string|min:6',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'contrasena.min' => 'La contraseña debe tener al menos 6 caracteres',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'nombre' => $request->nombre,
            'email' => $request->email,
        ];

        if ($request->filled('contrasena')) {
            $data['contrasena'] = $request->contrasena;
        }

        $response = $this->apiService->patch('/actualizar/empleado/' . $id, $data);

        Log::info('Respuesta de actualización', [
            'id' => $id,
            'success' => $response['success'],
            'status' => $response['status'] ?? 'N/A'
        ]);

        if ($response['success']) {
            return redirect()->route('empleados.index')
                ->with('success', 'Empleado actualizado exitosamente');
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Error al actualizar empleado')
            ->withInput();
    }

    public function destroy($id)
    {
        Log::info('Eliminando empleado', [
            'id' => $id,
            'id_type' => gettype($id)
        ]);

        $response = $this->apiService->delete('/eliminar/empleado/' . $id);

        Log::info('Respuesta de eliminación', [
            'id' => $id,
            'success' => $response['success'],
            'status' => $response['status'] ?? 'N/A'
        ]);

        if ($response['success']) {
            return redirect()->route('empleados.index')
                ->with('success', 'Empleado eliminado exitosamente');
        }

        return redirect()->route('empleados.index')
            ->with('error', $response['message'] ?? 'Error al eliminar empleado');
    }
}
