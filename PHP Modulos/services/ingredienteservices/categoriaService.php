<?php
// Asegúrate de que esta ruta sea correcta para incluir tu archivo de configuración
require_once __DIR__ .  '../../../config/configIngredientes.php'; 

class CategoriaService {
    
    // La propiedad $baseUrl ya no es necesaria, ya que se usa la configuración.

    /**
     * Obtener todas las categorías (GET)
     * Usa: endpointGet::API_GET_CATEGORIAS_INGREDIENTES
     */
    public function obtenerCategorias() {
        // Usamos la constante GET definida en la configuración
        $url = endpointGet::API_GET_CATEGORIAS_INGREDIENTES; 
        
        $response = @file_get_contents($url);

        // Se mantiene la lógica original de manejo de respuesta
        return $response ? json_decode($response, true) : false;
    }

    /**
     * Crear una categoría (POST)
     * Usa: endpointPost::API_CREAR_CATEGORIA_INGREDIENTE
     */
    public function crearCategoria($nombreCategoria) {
        // Usamos la constante POST definida en la configuración
        $url = endpointPost::API_CREAR_CATEGORIA_INGREDIENTE;
        
        $data = ["nombreCategoria" => $nombreCategoria];
        
        $options = [
            "http" => [
                "header"  => "Content-Type: application/json",
                "method"  => "POST",
                "content" => json_encode($data)
            ]
        ];
        $context  = stream_context_create($options);
        
        return @file_get_contents($url, false, $context);
    }

    /**
     * Editar una categoría (PUT)
     * Usa: endpointPut::categoriaIngrediente($id)
     */
    public function editarCategoria($id, $nombreCategoria) {
        // Usamos el método estático PUT para construir la URL con el ID
        $url = endpointPut::categoriaIngrediente($id);
        
        $data = ["nombreCategoria" => $nombreCategoria];
        
        $options = [
            "http" => [
                "header"  => "Content-Type: application/json",
                "method"  => "PUT",
                "content" => json_encode($data)
            ]
        ];
        $context  = stream_context_create($options);
        
        return @file_get_contents($url, false, $context);
    }

    /**
     * Eliminar una categoría (DELETE)
     * Usa: endpointDelete::categoriaIngrediente($id)
     */
    public function eliminarCategoria($id) {
        // Usamos el método estático DELETE para construir la URL con el ID
        $url = endpointDelete::categoriaIngrediente($id);

        $options = [
            "http" => [
                "method"  => "DELETE"
            ]
        ];
        $context  = stream_context_create($options);
        
        return @file_get_contents($url, false, $context);
    }
}
?>