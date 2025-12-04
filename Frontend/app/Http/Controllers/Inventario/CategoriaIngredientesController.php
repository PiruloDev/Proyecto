<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Inventario\CategoriaIngredientesService;
use Illuminate\Support\Facades\Redirect;

class CategoriaIngredientesController extends Controller
{
    protected $categoriaIngredientesService;

    public function __construct(CategoriaIngredientesService $categoriaIngredientesService)
    {
        $this->categoriaIngredientesService = $categoriaIngredientesService;
    }

    /**
     * Muestra el listado de todas las categorías de ingredientes. (INDEX)
     */
    public function index()
    {
        $response = $this->categoriaIngredientesService->obtenerCategoriasIngredientes();

        if (!$response['success']) {
            $errorMessage = $response['error'] ?? 'Error desconocido al obtener categorías.';
            return view('inventarioviews.categorias.index', ['categorias' => []])
                   ->with('error', 'Error al cargar categorías: ' . $errorMessage);
        }
        
        $categorias = $response['data'] ?? [];
        
        return view('inventarioviews.categorias.index', compact('categorias'));
    }

    /**
     * Muestra el formulario para crear una nueva categoría. (CREATE FORM)
     */
    public function create()
    {
        return view('inventarioviews.categoriasIngredientes.create');
    }
    
    /**
     * Almacena una nueva categoría. (STORE)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombreCategoria' => 'required|string|max:255',
        ]);

        $data = $request->only(['nombreCategoria']);

        $response = $this->categoriaIngredientesService->crearCategoriaIngrediente($data);

        if ($response['success']) {
            return Redirect::route('categorias-ingredientes.index')
                           ->with('success', 'Categoría creada con éxito.');
        }

        return Redirect::back()->withInput()->with('error', $response['error']);
    }

    /**
     * Muestra una categoría específica. (SHOW - Opcional)
     */
    public function show(int $id)
    {
        // Si necesitas implementar vista de detalle individual
        // puedes agregar lógica aquí
    }

    /**
     * Actualiza una categoría existente. (UPDATE)
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'nombreCategoria' => 'required|string|max:255',
        ]);
        
        $data = $request->only(['nombreCategoria']);

        $response = $this->categoriaIngredientesService->actualizarCategoriaIngrediente($id, $data);

        if ($response['success']) {
            return Redirect::route('categorias-ingredientes.index')
                           ->with('success', 'Categoría actualizada con éxito.');
        }

        return Redirect::back()->withInput()->with('error', $response['error']);
    }

    /**
     * Elimina una categoría. (DELETE)
     */
    public function destroy(int $id)
    {
        $response = $this->categoriaIngredientesService->eliminarCategoriaIngrediente($id);

        if ($response['success']) {
            return Redirect::route('categorias-ingredientes.index')
                           ->with('success', 'Categoría eliminada con éxito.');
        }
        
        return Redirect::back()->with('error', $response['error']);
    }
}