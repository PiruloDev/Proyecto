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
        
        // La vista utiliza la variable $ingredientes para el bucle @forelse
        return view('inventarioviews.ingredientes.index', compact('ingredientes'));
    }

    /**
     * Muestra el formulario para crear un nuevo ingrediente.
     */
    public function create()
    {
        // Pasar listas de Proveedores y Categorías si son necesarias para select boxes
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

        // Obtenemos solo los datos validados que necesita el service
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
     * Muestra un ingrediente específico (Opcional, se puede hacer con la misma lista si solo se usa el DTO).
     * Si el backend tiene un endpoint GET /ingrediente/{id}, se implementaría aquí.
     */
    // public function show(int $id) {}

    /**
     * Muestra el formulario para editar un ingrediente. (READ ONE for EDIT)
     */
    // public function edit(int $id) 
    // { 
        // Si el backend tiene un endpoint GET /ingrediente/{id}, se llama aquí 
        // y se pasa el ingrediente a la vista de edición.
    // }

    /**
     * Actualiza un ingrediente existente. (UPDATE)
     */
    public function update(Request $request, int $id)
    {
        // 🎯 VALIDACIÓN: Solo validamos los 4 campos del DTO
        $request->validate([
            'idProveedor' => 'required|integer',
            'idCategoria' => 'required|integer',
            'nombreIngrediente' => 'required|string|max:255',
            'referenciaIngrediente' => 'required|string|max:50',
        ]);
        
        // Obtenemos solo los datos validados que necesita el service
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