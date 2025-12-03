<?php

namespace App\Http\Controllers\Inventario;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Services\Inventario\CategoriaService;
use App\Http\Controllers\Controller; 


class CategoriaController extends Controller 
{
    private $categoriaService;

    public function __construct(CategoriaService $categoriaService) 
    {
        // Solo inyectamos el servicio que necesitamos
        $this->categoriaService = $categoriaService;
    }

    // ============================================
    // HELPER PARA REDIRECCIONES Y ESTADO (Duplicado o movido a un Trait si se usa mucho)
    // ============================================
    private function handleRedirect(array $resultado, string $actionName, string $routeName)
    {
        if ($resultado["success"]) {
            $msg = $resultado['response'] ?? ucfirst($actionName) . ' completada correctamente.';
            return Redirect::route($routeName)->with(['status' => $msg, 'status_type' => 'success']);
        } else {
            $errorMsg = $resultado["error"] ?? 'Error desconocido al ' . $actionName . '.';
            return back()->withInput()->with(['status' => "Error al {$actionName}: $errorMsg", 'status_type' => 'danger']);
        }
    }

    // ============================================
    // MÉTODOS DE RECURSO (CRUD)
    // ============================================

    /**
     * Muestra el listado de Categorías. (GET /categorias)
     */
    public function index()
{
    $categorias = $this->categoriaService->obtenerCategorias();
    return view('inventarioviews.categorias.index', compact('categorias'));
}

public function store(Request $request)
{
    $request->validate(['nombreCategoria' => 'required|string|max:100']);
    $resultado = $this->categoriaService->crearCategoria($request->nombreCategoria);
    return $this->handleRedirect($resultado, 'crear categoría', 'categorias.index');
}

public function update(Request $request)
{
    $request->validate([
        'idCategoria' => 'required|integer|min:1',
        'nombreCategoria' => 'required|string|max:100'
    ]);

    $resultado = $this->categoriaService->editarCategoria(
        $request->idCategoria,
        $request->nombreCategoria
    );

    return $this->handleRedirect($resultado, 'actualizar categoría', 'categorias.index');
}

public function destroy(Request $request)
{
    $request->validate(['idCategoria' => 'required|integer|min:1']);

    $resultado = $this->categoriaService->eliminarCategoria($request->idCategoria);

    return $this->handleRedirect($resultado, 'eliminar categoría', 'categorias.index');
}

}