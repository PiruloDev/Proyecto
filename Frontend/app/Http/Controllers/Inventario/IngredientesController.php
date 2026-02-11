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

    // Inyectamos los 3 servicios necesarios
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
    $resProveedores = $this->proveedoresService->obtenerProveedores();
    $resCategorias = $this->categoriasService->obtenerCategoriasIngredientes();

    if (!$resIngredientes['success']) {
        return view('inventarioviews.ingredientes.index', [
            'ingredientes' => [],
            'proveedores' => [],
            'categorias' => []
        ])->with('error', 'Error al cargar ingredientes: ' . ($resIngredientes['error'] ?? 'Error desconocido'));
    }

    // --- NORMALIZACIÓN DE DATOS ---
    // Mapeamos los ingredientes para asegurar que tengan 'idCategoria'
    $ingredientes = collect($resIngredientes['data'] ?? [])->map(function($ing) {
        $ing['idCategoria'] = $ing['idCategoria'] ?? $ing['id_categoria'] ?? $ing['id'] ?? 0;
        $ing['idProveedor'] = $ing['idProveedor'] ?? $ing['id_proveedor'] ?? $ing['id'] ?? 0;
        return $ing;
    })->toArray();

    // Mapeamos las categorías para asegurar que tengan 'idCategoria' y 'nombreCategoria'
    $categorias = collect($resCategorias['data'] ?? [])->map(function($cat) {
        return [
            'idCategoria' => $cat['idCategoria'] ?? $cat['id'] ?? 0,
            'nombreCategoria' => $cat['nombreCategoria'] ?? $cat['nombre'] ?? 'Sin nombre'
        ];
    })->toArray();
    // ------------------------------

    return view('inventarioviews.ingredientes.index', [
        'ingredientes' => $ingredientes,
        'proveedores'  => $resProveedores['data'] ?? [],
        'categorias'   => $categorias
    ]);
}

    public function inventario()
{
    $response = $this->ingredientesService->obtenerIngredientesSimple();

    if (!$response['success']) {
        // Manejo de error específico para la vista de stock
        return view('inventarioviews.ingredientes.inventario', ['ingredientes' => []])
                   ->with('error', 'Error al cargar el stock: ' . $response['error']);
    }
    
    $ingredientes = $response['data'] ?? [];
    
    // El nombre de la vista es la que se usó en la solicitud anterior.
    return view('inventarioviews.ingredientes.inventario', compact('ingredientes')); 
}

    /**
     * Muestra el formulario para crear un nuevo ingrediente.
     */
    public function create()
    {
        return view('ingredientes.index');
    }
    
    /**
     * Almacena un ingrediente nuevo. (CREATE)
     */
    public function store(Request $request)
    {
        $request->validate([
            'idProveedor' => 'required|integer',
            'idCategoria' => 'required|integer',
            'nombreIngrediente' => 'required|string|max:255',
            'referenciaIngrediente' => 'required|string|max:50',
        ]);

        $data = $request->only([
            'idProveedor',
            'idCategoria',
            'nombreIngrediente',
            'referenciaIngrediente',
        ]);

        $response = $this->ingredientesService->agregarIngredientes($data);

        if ($response['success']) {
            return Redirect::route('ingredientes.index')->with('success', 'Ingrediente creado con éxito.');
        }

        // Si falla, regresa con los datos que ya se habían escrito y el mensaje de error
        return Redirect::back()->withInput()->with('error', $response['error']);
    }



    /**
     * Actualiza un ingrediente existente. (UPDATE)
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'idProveedor' => 'required|integer',
            'idCategoria' => 'required|integer',
            'nombreIngrediente' => 'required|string|max:255',
            'referenciaIngrediente' => 'required|string|max:50',
        ]);
        
        $data = $request->only([
            'idProveedor',
            'idCategoria',
            'nombreIngrediente',
            'referenciaIngrediente',
        ]);

        $response = $this->ingredientesService->actualizarIngrediente($id, $data);

        if ($response['success']) {
            return Redirect::route('ingredientes.index')->with('success', 'Ingrediente actualizado con éxito.');
        }

        return Redirect::back()->withInput()->with('error', $response['error']);
    }

    /**
     * Elimina un ingrediente. (DELETE)
     */
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
    // 1. Validación de los datos
    $request->validate([
        // Se espera un número con al menos dos decimales (0.01)
        'cantidadIngresada' => 'required|numeric|min:0.01',
    ], [
        'cantidadIngresada.required' => 'La cantidad a ingresar es obligatoria.',
        'cantidadIngresada.numeric' => 'La cantidad debe ser un número válido.',
        'cantidadIngresada.min' => 'La cantidad debe ser positiva.'
    ]);

    $data = $request->only('cantidadIngresada');
    
    // 2. Llamada al servicio que interactúa con Spring Boot
    $response = $this->ingredientesService->ingresarStock($id, $data);

    if ($response['success']) {
        // Respuesta JSON de éxito para la petición AJAX
        return response()->json([
            'success' => true,
            'message' => 'Stock actualizado con éxito. El inventario se está recargando...'
        ]);
    }

    return response()->json([
        'success' => false,
        'error' => $response['error']
    ], 500); // Retorna un código 500 para manejar el error en JavaScript
}
}