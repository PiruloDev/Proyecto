<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Inventario\ProveedoresService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;

class ProveedoresController extends Controller
{
    protected $proveedoresService;

    public function __construct(ProveedoresService $proveedoresService)
    {
        $this->proveedoresService = $proveedoresService;
    }

    /**
     * Muestra el listado de todos los proveedores. (INDEX)
     */
    public function index()
    {
        $response = $this->proveedoresService->obtenerProveedores();

        if (!$response['success']) {
            $errorMessage = $response['error'] ?? 'Error desconocido al obtener proveedores.';
            return view('inventarioviews.proveedores.index', ['proveedores' => []])
                   ->with('error', 'Error al cargar proveedores: ' . $errorMessage);
        }
        
        $proveedores = $response['data'] ?? [];
        
        return view('inventarioviews.proveedores.index', compact('proveedores'));
    }

    /**
     * Muestra el formulario para crear un nuevo proveedor. (CREATE FORM)
     */
    public function create()
    {
        return view('inventarioviews.proveedores.create');
    }
    
    /**
     * Almacena un nuevo proveedor. (STORE)
     */
    public function store(Request $request)
    {
        // Validación - direccionProv es nullable
        $request->validate([
            'nombreProv' => 'required|string|max:255',
            'telefonoProv' => 'required|string|max:20',
            'activoProv' => 'required',
            'emailProv' => 'required|email|max:255',
            'direccionProv' => 'nullable|string|max:500',
        ]);

        $data = $request->only([
            'nombreProv',
            'telefonoProv',
            'activoProv',
            'emailProv',
            'direccionProv'
        ]);

        // Log para debugging
        Log::info('Datos recibidos en store', $data);

        $response = $this->proveedoresService->crearProveedor($data);

        if ($response['success']) {
            return Redirect::route('proveedores.index')
                           ->with('success', 'Proveedor creado con éxito.');
        }

        Log::error('Error en store de proveedor', ['error' => $response['error']]);
        
        return Redirect::back()
                       ->withInput()
                       ->with('error', $response['error']);
    }

    /**
     * Muestra un proveedor específico. (SHOW - Opcional)
     */
    public function show(int $id)
    {
        // Si necesitas implementar vista de detalle individual
    }

    /**
     * Actualiza un proveedor existente. (UPDATE)
     */
    public function update(Request $request, int $id)
    {
        // Validación - direccionProv es nullable
        $request->validate([
            'nombreProv' => 'required|string|max:255',
            'telefonoProv' => 'required|string|max:20',
            'activoProv' => 'required',
            'emailProv' => 'required|email|max:255',
            'direccionProv' => 'nullable|string|max:500',
        ]);
        
        $data = $request->only([
            'nombreProv',
            'telefonoProv',
            'activoProv',
            'emailProv',
            'direccionProv'
        ]);

        // Log para debugging
        Log::info('Datos recibidos en update', ['id' => $id, 'data' => $data]);

        $response = $this->proveedoresService->actualizarProveedor($id, $data);

        if ($response['success']) {
            return Redirect::route('proveedores.index')
                           ->with('success', 'Proveedor actualizado con éxito.');
        }

        Log::error('Error en update de proveedor', ['id' => $id, 'error' => $response['error']]);

        return Redirect::back()
                       ->withInput()
                       ->with('error', $response['error']);
    }

    /**
     * Elimina un proveedor. (DELETE)
     */
    public function destroy(int $id)
    {
        Log::info('Intentando eliminar proveedor desde controller', ['id' => $id]);
        
        $response = $this->proveedoresService->eliminarProveedor($id);

        if ($response['success']) {
            return Redirect::route('proveedores.index')
                           ->with('success', 'Proveedor eliminado con éxito.');
        }

        Log::error('Error en destroy de proveedor', ['id' => $id, 'error' => $response['error']]);
        
        return Redirect::back()
                       ->with('error', $response['error']);
    }
}