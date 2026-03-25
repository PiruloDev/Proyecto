<?php

namespace App\Http\Controllers\Productos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    protected $apiUrl;

    public function __construct()
{
    $this->apiUrl = env('API_BASE_URL', 'http://localhost:8080') . '/productos';
}

    public function index()
    {
        try {
            $response = Http::get($this->apiUrl);
            $productos = $response->json();
            
            if ($response->failed() || !is_array($productos)) {
                $productos = []; 
                Log::error('Error al cargar productos desde la API', ['status' => $response->status()]);
            }

            $categorias = [
                1 => 'Tortas Tres Leches',
                2 => 'Tortas Milyway',
                3 => 'Tortas por Encargo',
                4 => 'Pan Grande',
                5 => 'Pan Pequeño',
                6 => 'Postres',
                7 => 'Galletas',
                8 => 'Tamales',
                9 => 'Yogures',
                10 => 'Pasteles Pollo',
            ];

            return view('Productos.index', compact('productos', 'categorias'));
        
        } catch (\Exception $e) {
            Log::error('Excepción en ProductoController@index: ' . $e->getMessage());
            $productos = [];
            $categorias = [];
            return view('Productos.index', compact('productos', 'categorias'))
                ->withErrors('No se pudo conectar con el servicio de productos.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'categoria' => 'required|integer|min:1|max:10',
                'descripcion' => 'nullable|string',
                'precio' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'estado' => 'required|in:activo,inactivo',
                'marca' => 'nullable|string'
            ]);

            $data = [
                'NOMBRE_PRODUCTO' => $validated['nombre'],
                'ID_CATEGORIA_PRODUCTO' => (int)$validated['categoria'],
                'DESCRIPCION_PRODUCTO' => $validated['descripcion'] ?? '',
                'PRECIO_PRODUCTO' => (float)$validated['precio'],
                'PRODUCTO_STOCK_MIN' => (int)$validated['stock'],
                'TIPO_PRODUCTO_MARCA' => $validated['marca'] ?? 'Propio',
                'ACTIVO' => $validated['estado'] === 'activo',
                'ID_ADMIN' => 1,
                'FECHA_VENCIMIENTO_PRODUCTO' => now()->addYear()->format('Y-m-d'),
                'FECHA_INGRESO_PRODUCTO' => now()->format('Y-m-d'),
            ];

            Log::info('CREAR PRODUCTO - Datos a enviar:', $data);

            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrl, $data);

            if ($response->successful()) {
                return redirect()->route('productos.index')
                    ->with('success', 'Producto creado correctamente.');
            } else {
                Log::error('Error API al crear producto', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return back()->with('error', 'Error al crear el producto. Código: ' . $response->status())
                    ->withInput();
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Excepción al crear producto: ' . $e->getMessage());
            return back()->with('error', 'Error inesperado: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            Log::info('=== UPDATE REQUEST ===', [
                'id' => $id,
                'all_data' => $request->all(),
            ]);

            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'categoria' => 'required|integer|min:1|max:10',
                'descripcion' => 'nullable|string',
                'precio' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'estado' => 'required|in:activo,inactivo',
                'marca' => 'nullable|string'
            ]);

            Log::info('=== DATOS VALIDADOS ===', $validated);

            // IMPORTANTE: Mapear exactamente como espera el @JsonProperty del POJO de Spring Boot
            $data = [
                'ID_PRODUCTO' => (int)$id,
                'NOMBRE_PRODUCTO' => $validated['nombre'],
                'ID_CATEGORIA_PRODUCTO' => (int)$validated['categoria'],
                'DESCRIPCION_PRODUCTO' => $validated['descripcion'] ?? '',
                'PRECIO_PRODUCTO' => (float)$validated['precio'],
                'PRODUCTO_STOCK_MIN' => (int)$validated['stock'],
                'TIPO_PRODUCTO_MARCA' => $validated['marca'] ?? 'Propio',
                'ACTIVO' => $validated['estado'] === 'activo',
                'ID_ADMIN' => 1,
                'FECHA_VENCIMIENTO_PRODUCTO' => now()->addYear()->format('Y-m-d'),
                'FECHA_INGRESO_PRODUCTO' => now()->format('Y-m-d'),
            ];

            Log::info('=== DATOS A ENVIAR A API (formato @JsonProperty) ===', $data);

            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->patch($this->apiUrl . '/' . $id, $data);

            Log::info('=== RESPUESTA DE API ===', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                return redirect()->route('productos.index')
                    ->with('success', 'Producto actualizado correctamente.');
            }

            return back()
                ->with('error', 'Error ' . $response->status() . ': ' . $response->body())
                ->withInput();

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Errores de validación:', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Excepción en update:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->with('error', 'Error: ' . $e->getMessage())
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
                'body' => $response->body()
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