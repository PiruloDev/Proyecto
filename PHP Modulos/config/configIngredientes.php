<?php

/**
 * Clase base para la URL del API.
 * Define la URL base de la aplicación.
 */
class endpointBase {
    /**
     * @var string La URL base de la API.
     */
    protected const API_BASE_URL = 'http://localhost:8080';
}

// --------------------------------------------------------------------------------------------------

/**
 * Endpoints para operaciones de obtención de datos (GET).
 */
class endpointGet extends endpointBase {
    // Ingredientes
    const API_GET_INGREDIENTES_LISTA = self::API_BASE_URL . '/ingredientes/lista';
    // Proveedores
    const API_GET_PROVEEDORES = self::API_BASE_URL . '/proveedores';
    // Categorías
    const API_GET_CATEGORIAS_INGREDIENTES = self::API_BASE_URL . '/categorias/ingredientes';
    // Detalles de Pedidos
    const API_GET_DETALLES_PEDIDOS = self::API_BASE_URL . '/detalles-pedidos';
}

// --------------------------------------------------------------------------------------------------

/**
 * Endpoints para operaciones de creación de recursos (POST).
 */
class endpointPost extends endpointBase {
    // Ingredientes
    const API_CREAR_INGREDIENTE = self::API_BASE_URL . '/crearingrediente';
    // Proveedores
    const API_CREAR_PROVEEDOR = self::API_BASE_URL . '/proveedores';
    // Categorías
    const API_CREAR_CATEGORIA_INGREDIENTE = self::API_BASE_URL . '/nuevacategoriaingrediente';
    // Detalles de Pedidos
    const API_CREAR_DETALLE_PEDIDO = self::API_BASE_URL . '/detalles-pedidos';
}

// --------------------------------------------------------------------------------------------------

/**
 * Endpoints para operaciones de actualización completa (PUT).
 */
class endpointPut extends endpointBase {
    // Bases de ruta para actualizar (requieren ID)
    private const BASE_INGREDIENTE_PUT = self::API_BASE_URL . '/ingrediente';
    private const BASE_PROVEEDOR_PUT = self::API_BASE_URL . '/proveedores';
    private const BASE_CATEGORIA_PUT = self::API_BASE_URL . '/categoriaingrediente';
    private const BASE_DETALLE_PEDIDO_PUT = self::API_BASE_URL . '/detalles-pedidos';

    /**
     * Obtiene el endpoint para actualizar un ingrediente.
     */
    public static function ingrediente(int $id): string {
        return self::BASE_INGREDIENTE_PUT . "/{$id}";
    }
    
    /**
     * Obtiene el endpoint para actualizar un proveedor.
     */
    public static function proveedor(int $id): string {
        return self::BASE_PROVEEDOR_PUT . "/{$id}";
    }

    /**
     * Obtiene el endpoint para actualizar una categoría de ingrediente.
     */
    public static function categoriaIngrediente(int $id): string {
        return self::BASE_CATEGORIA_PUT . "/{$id}";
    }
    
    /**
     * Obtiene el endpoint para actualizar un detalle de pedido.
     */
    public static function detallePedido(int $id): string {
        return self::BASE_DETALLE_PEDIDO_PUT . "/{$id}";
    }
}

// --------------------------------------------------------------------------------------------------

/**
 * Endpoints para operaciones de actualización parcial (PATCH).
 */
class endpointPatch extends endpointBase {
    // Base de ruta para actualizar la cantidad del ingrediente (requiere ID)
    private const BASE_INGREDIENTE_PATCH = self::API_BASE_URL;

    /**
     * Obtiene el endpoint para actualizar solo la cantidad de un ingrediente.
     */
    public static function cantidadIngrediente(int $id): string {
        return self::BASE_INGREDIENTE_PATCH . "/{$id}/cantidad";
    }
}

// --------------------------------------------------------------------------------------------------

/**
 * Endpoints para operaciones de eliminación (DELETE).
 */
class endpointDelete extends endpointBase {
    // Bases de ruta para eliminar (requieren ID)
    private const BASE_INGREDIENTE_DELETE = self::API_BASE_URL . '/ingrediente';
    private const BASE_PROVEEDOR_DELETE = self::API_BASE_URL . '/proveedores';
    private const BASE_CATEGORIA_DELETE = self::API_BASE_URL . '/eliminarcategoria';
    private const BASE_DETALLE_PEDIDO_DELETE = self::API_BASE_URL . '/detalles-pedidos';


    /**
     * Obtiene el endpoint para eliminar un ingrediente.
     */
    public static function ingrediente(int $id): string {
        return self::BASE_INGREDIENTE_DELETE . "/{$id}";
    }

    /**
     * Obtiene el endpoint para eliminar un proveedor.
     */
    public static function proveedor(int $id): string {
        return self::BASE_PROVEEDOR_DELETE . "/{$id}";
    }

    /**
     * Obtiene el endpoint para eliminar una categoría de ingrediente.
     */
    public static function categoriaIngrediente(int $id): string {
        return self::BASE_CATEGORIA_DELETE . "/{$id}";
    }

    /**
     * Obtiene el endpoint para eliminar un detalle de pedido.
     */
    public static function detallePedido(int $id): string {
        return self::BASE_DETALLE_PEDIDO_DELETE . "/{$id}";
    }
}