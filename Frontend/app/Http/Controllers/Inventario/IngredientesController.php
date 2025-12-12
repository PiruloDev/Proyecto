<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Inventario\IngredientesService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log; 

class IngredientesController extends Controller
{
    protected $ingredientesService;


    public function __construct(IngredientesService $ingredientesService /*, ProveedorService $proveedorService, CategoriaService $categoriaService */)
    {
        $this->ingredientesService = $ingredientesService;

    }

 
    public function index()
    {
        $response = $this->ingredientesService->obtenerIngredientes();

        if (!$response['success']) {
            $errorMessage = $response['error'] ?? 'Error desconocido al obtener ingredientes.';
            // Retorna a la vista con un mensaje de error
            return view('inventarioviews.ingredientes.index', ['ingredientes' => []])
                   ->with('error', 'Error al cargar ingredientes: ' . $errorMessage);
        }
        
        $ingredientes = $response['data'] ?? [];
        
        return view('inventarioviews.ingredientes.index', compact('ingredientes'));
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
        return view('inventarioviews.ingredientes.create');
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
}