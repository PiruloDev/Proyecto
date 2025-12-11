<?php

namespace App\Http\Controllers\Productos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    protected $apiUrl = 'http://localhost:8080/productos';

    public function index()
    {
        $response = Http::get($this->apiUrl);
        $productos = $response->successful() ? $response->json() : [];

        return view('productos.index', compact('productos'));
    }

    public function store(Request $request)
    {
        try {
            Log::info('Creando producto', $request->all());
            
            $response = Http::timeout(10)->post($this->apiUrl, [
                'nombreProducto' => $request->nombre,
                'idCategoriaProducto' => (int)$request->categoria,
                'descripcionProducto' => $request->descripcion ?? '',
                'precio' => (float)$request->precio,
                'stockMinimo' => (int)$request->stock,
                'marcaProducto' => $request->marca ?? 'Propio',
                'activo' => $request->estado === 'activo',
                'imagenProducto' => $request->imagen ?? 'https://via.placeholder.com/60',
                'fechaVencimiento' => now()->addYear()->format('Y-m-d'),
                'fechaIngreso' => now()->format('Y-m-d'),
                'idAdmin' => 1
            ]);

            Log::info('Respuesta API CREATE', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                return redirect()->route('productos.index')
                    ->with('success', 'Producto creado correctamente');
            }

            return redirect()->back()
                ->with('error', 'Error al crear: ' . json_encode($response->json()))
                ->withInput();
                
        } catch (\Exception $e) {
            Log::error('Error crear producto: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error de conexión: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            Log::info('Actualizando producto ID: ' . $id, $request->all());
            
            $data = [
                'id' => (int)$id,
                'nombreProducto' => $request->nombre,
                'idCategoriaProducto' => (int)$request->categoria,
                'descripcionProducto' => $request->descripcion ?? '',
                'precio' => (float)$request->precio,
                'stockMinimo' => (int)$request->stock,
                'marcaProducto' => $request->marca ?? 'Propio',
                'activo' => $request->estado === 'activo',
                'imagenProducto' => $request->imagen ?? 'https://via.placeholder.com/60'
            ];
            
            Log::info('Datos enviados a API', $data);
            
            $response = Http::timeout(10)->put($this->apiUrl . '/' . $id, $data);

            Log::info('Respuesta API UPDATE', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                return redirect()->route('productos.index')
                    ->with('success', 'Producto actualizado correctamente');
            }

            $errorBody = $response->json();
            return redirect()->back()
                ->with('error', 'Error al actualizar: ' . ($errorBody['error'] ?? $response->body()))
                ->withInput();
                
        } catch (\Exception $e) {
            Log::error('Error actualizar producto: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error de conexión: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            Log::info('Eliminando producto ID: ' . $id);
            
            $response = Http::timeout(10)->delete($this->apiUrl . '/' . $id);

            Log::info('Respuesta API DELETE', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                return redirect()->route('productos.index')
                    ->with('success', 'Producto eliminado correctamente');
            }

            return redirect()->back()
                ->with('error', 'Error al eliminar: ' . $response->body());
                
        } catch (\Exception $e) {
            Log::error('Error eliminar producto: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error de conexión: ' . $e->getMessage());
        }
    }
}