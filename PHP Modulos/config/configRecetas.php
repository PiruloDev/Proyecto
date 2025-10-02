<?php
// config/configRecetas.php

// --------------------------------------------------------------------------------------------------
// NUEVOS ENDPOINTS PARA RECETAS
// --------------------------------------------------------------------------------------------------

class endpointGetRecetas {
    
    private const BASE_RECETAS_GET = 'http://localhost:8080/inventario/recetas';

    /**
     * Obtiene todas las recetas.
     */
    const API_GET_RECETAS_LISTA = self::BASE_RECETAS_GET;

    /**
     * Obtiene el endpoint para una receta específica por ID de Producto.
     */
    public static function recetaPorProducto(int $id): string {
        return self::BASE_RECETAS_GET . "/{$id}";
    }
}

// --------------------------------------------------------------------------------------------------

class endpointPostRecetas {
    
    // Recetas
    const API_CREAR_RECETA = 'http://localhost:8080/inventario/recetas';
}

// --------------------------------------------------------------------------------------------------

class endpointPutRecetas {

    private const BASE_RECETAS_PUT = 'http://localhost:8080/inventario/recetas';

    /**
     * Obtiene el endpoint para actualizar (reemplazar) una receta.
     */
    public static function receta(int $id): string {
        return self::BASE_RECETAS_PUT . "/{$id}";
    }
}

// --------------------------------------------------------------------------------------------------

class endpointDeleteRecetas {
    
    private const BASE_RECETAS_DELETE = 'http://localhost:8080/inventario/recetas';

    /**
     * Obtiene el endpoint para eliminar una receta.
     */
    public static function receta(int $id): string {
        return self::BASE_RECETAS_DELETE . "/{$id}";
    }
}