<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Inventario\IngredientesService;
use App\Services\Inventario\ProveedoresService;
use App\Services\Inventario\CategoriaIngredientesService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log; 

class IngredientesController extends Controller
{
    protected $ingredientesService;
    protected $proveedoresService;
    protected $categoriasService;

    public function __construct(
        IngredientesService $ingredientesService, 
        ProveedoresService $proveedoresService, 
        CategoriaIngredientesService $categoriasService
    ) {
        $this->ingredientesService = $ingredientesService;
        $this->proveedoresService = $proveedoresService;
        $this->categoriasService = $categoriasService;
    }

    public function index()
    {
        // 1. Obtener datos de los servicios
        $resIngredientes = $this->ingredientesService->obtenerIngredientes();
        $resCategorias = $this->categoriasService->obtenerCategoriasIngredientes();
        $resProveedores = $this->proveedoresService->obtenerProveedores();

        // 2. Normalización de Ingredientes
        $ingredientes = collect($resIngredientes['data'] ?? [])->map(function($ing) {
            return [
                'idIngrediente' => $ing['idIngrediente'] ?? 0,
                'nombreIngrediente' => $ing['nombreIngrediente'] ?? 'Sin nombre',
                'referenciaIngrediente' => $ing['referenciaIngrediente'] ?? 'N/A',
                'idCategoria' => $ing['idCategoria'] ?? 0,
                'idProveedor' => $ing['idProveedor'] ?? 0,
            ];
        })->toArray();

        // 3. Normalización de Categorías
        $categorias = collect($resCategorias['data'] ?? [])->map(function($cat) {
            return [
                'idCategoria' => $cat['idCategoria'] ?? $cat['id'] ?? 0,
                'nombreCategoria' => $cat['nombreCategoria'] ?? $cat['nombre'] ?? 'Sin nombre'
            ];
        })->toArray();

        // 4. Normalización de Proveedores (Ajustado a tu Backend Java: nombreProv)
        $proveedores = collect($resProveedores['data'] ?? [])->map(function($prov) {
            return [
                'idProveedor' => $prov['idProveedor'] ?? $prov['id'] ?? 0,
                'nombreProv' => $prov['nombreProv'] ?? $prov['nombre'] ?? 'Sin nombre'
            ];
        })->toArray();

        // 5. Retornar vista con los datos normalizados
        return view('inventarioviews.ingredientes.index', compact('ingredientes', 'categorias', 'proveedores'));
    }

    public function inventario()
    {
        $response = $this->ingredientesService->obtenerIngredientesSimple();

        if (!$response['success']) {
            return view('inventarioviews.ingredientes.inventario', ['ingredientes' => []])
                       ->with('error', 'Error al cargar el stock: ' . $response['error']);
        }
        
        $ingredientes = $response['data'] ?? [];
        return view('inventarioviews.ingredientes.inventario', compact('ingredientes')); 
    }

    public function create()
    {
        return view('ingredientes.index');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'idProveedor' => 'required|integer',
            'idCategoria' => 'required|integer',
            'nombreIngrediente' => 'required|string|max:255',
            'referenciaIngrediente' => 'required|string|max:50',
        ]);

        $data = $request->only(['idProveedor', 'idCategoria', 'nombreIngrediente', 'referenciaIngrediente']);
        $response = $this->ingredientesService->agregarIngredientes($data);

        if ($response['success']) {
            return Redirect::route('ingredientes.index')->with('success', 'Ingrediente creado con éxito.');
        }

        return Redirect::back()->withInput()->with('error', $response['error']);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'idProveedor' => 'required|integer',
            'idCategoria' => 'required|integer',
            'nombreIngrediente' => 'required|string|max:255',
            'referenciaIngrediente' => 'required|string|max:50',
        ]);
        
        $data = $request->only(['idProveedor', 'idCategoria', 'nombreIngrediente', 'referenciaIngrediente']);
        $response = $this->ingredientesService->actualizarIngrediente($id, $data);

        if ($response['success']) {
            return Redirect::route('ingredientes.index')->with('success', 'Ingrediente actualizado con éxito.');
        }

        return Redirect::back()->withInput()->with('error', $response['error']);
    }

    public function destroy(int $id)
    {
        $response = $this->ingredientesService->eliminarIngrediente($id);

        if ($response['success']) {
            return Redirect::route('ingredientes.index')->with('success', 'Ingrediente eliminado con éxito.');
        }
        
        return Redirect::back()->with('error', $response['error']);
    }

    public function ingresarStock(Request $request, int $id)
    {
        $request->validate([
            'cantidadIngresada' => 'required|numeric|min:0.01',
        ]);

        $data = $request->only('cantidadIngresada');
        $response = $this->ingredientesService->ingresarStock($id, $data);

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Stock actualizado con éxito.'
            ]);
        }

        return response()->json(['success' => false, 'error' => $response['error']], 500);
    }
}