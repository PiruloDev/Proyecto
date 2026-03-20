<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
    $resIngredientes = $this->ingredientesService->obtenerIngredientes();
    $ingredientes = $resIngredientes['success'] ? $resIngredientes['data'] : [];

    // Proveedores y categorías (los que ya tienes)
    try {
        $proveedores = Http::get('http://32.193.167.191:8080/proveedores')->json() ?? [];
        $categorias  = Http::get('http://32.193.167.191:8080/categorias/ingredientes')->json() ?? [];
        $unidades    = Http::get('http://32.193.167.191:8080/unidades-medida')->json() ?? []; // ← nuevo
    } catch (\Exception $e) {
        $proveedores = $categorias = $unidades = [];
    }

    return view('inventarioviews.ingredientes.index', compact(
        'ingredientes', 'proveedores', 'categorias', 'unidades'
    ));
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
            'idUnidadMedida' => 'required|integer', 
            'nombreIngrediente' => 'required|string|max:255',
            'referenciaIngrediente' => 'required|string|max:50',
        ]);

        $data = $request->only(['idProveedor', 'idCategoria', 'idUnidadMedida','nombreIngrediente', 'referenciaIngrediente']);
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
            'idUnidadMedida' => 'required|integer',
            'nombreIngrediente' => 'required|string|max:255',
            'referenciaIngrediente' => 'required|string|max:50',
        ]);
        
        $data = $request->only(['idProveedor', 'idCategoria', 'idUnidadMedida', 'nombreIngrediente', 'referenciaIngrediente']);
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