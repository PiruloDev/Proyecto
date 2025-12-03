<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Inventario\ProveedoresService;

class ProveedoresController extends Controller
{
    protected $proveedoresService;

    public function __construct(ProveedoresService $proveedoresService)
    {
        $this->proveedoresService = $proveedoresService;
    }

    // ============================================================
    // LISTAR PROVEEDORES
    // ============================================================

    public function index()
    {
        $response = $this->proveedoresService->obtenerProveedores();

        if (!$response['success']) {
            return back()->with('error', $response['error']);
        }

        $proveedores = $response['data'];

        return view('inventarioviews.proveedores.index', compact('proveedores'));
    }

    // ============================================================
    // CREAR PROVEEDOR
    // ============================================================

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombreProv' => 'required|string|max:100',
            'telefonoProv' => 'nullable|string|max:20',
            'emailProv' => 'nullable|email|max:100',
            'activoProv' => 'required|boolean',
            'direccionProv' => 'nullable|string|max:255',
        ]);

        $response = $this->proveedoresService->agregarProveedor($validated);

        if ($response['success']) {
            return back()->with('success', 'Proveedor creado correctamente.');
        }

        return back()->with('error', 'Error al crear proveedor: '.$response['error']);
    }

    // ============================================================
    // ACTUALIZAR PROVEEDOR
    // ============================================================

    public function update($id, Request $request)
    {
        $validated = $request->validate([
            'nombreProv' => 'required|string|max:100',
            'telefonoProv' => 'nullable|string|max:20',
            'emailProv' => 'nullable|email|max:100',
            'activoProv' => 'required|boolean',
            'direccionProv' => 'nullable|string|max:255',
        ]);

        $response = $this->proveedoresService->actualizarProveedor($id, $validated);

        if ($response['success']) {
            return back()->with('success', 'Proveedor actualizado correctamente.');
        }

        return back()->with('error', 'Error al actualizar: '.$response['error']);
    }

    // ============================================================
    // ELIMINAR PROVEEDOR
    // ============================================================

    public function destroy($id)
    {
        $response = $this->proveedoresService->eliminarProveedor($id);

        if ($response['success']) {
            return back()->with('success', 'Proveedor eliminado correctamente.');
        }

        return back()->with('error', 'Error al eliminar: '.$response['error']);
    }
}
