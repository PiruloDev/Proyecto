<?php


class endpointGet {
    // Ingredientes
    const API_GET_INGREDIENTES_LISTA = 'http://localhost:8080/ingredientes/lista';
    // Proveedores
    const API_GET_PROVEEDORES = 'http://localhost:8080/proveedores';
    // Categorías
    const API_GET_CATEGORIAS_INGREDIENTES = 'http://localhost:8080/categorias/ingredientes';
    // Detalles de Pedidos
    const API_GET_DETALLES_PEDIDOS = 'http://localhost:8080/detalles-pedidos';
}

// --------------------------------------------------------------------------------------------------


class endpointPost {
    // Ingredientes
    const API_CREAR_INGREDIENTE = 'http://localhost:8080/crearingrediente';
    // Proveedores
    const API_CREAR_PROVEEDOR = 'http://localhost:8080/proveedores';
    // Categorías
    const API_CREAR_CATEGORIA_INGREDIENTE = 'http://localhost:8080/nuevacategoriaingrediente';
    // Detalles de Pedidos
    const API_CREAR_DETALLE_PEDIDO = 'http://localhost:8080/detalles-pedidos';
}

// --------------------------------------------------------------------------------------------------


class endpointPut {
    // Bases de ruta definidas directamente
    private const BASE_INGREDIENTE_PUT = 'http://localhost:8080/ingrediente';
    private const BASE_PROVEEDOR_PUT = 'http://localhost:8080/proveedores';
    private const BASE_CATEGORIA_PUT = 'http://localhost:8080/categoriaingrediente';
    private const BASE_DETALLE_PEDIDO_PUT = 'http://localhost:8080/detalles-pedidos';

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

/**
 * Endpoints para operaciones de actualización parcial (PATCH).
 */
class endpointPatch {
    // Base de ruta para actualizar la cantidad del ingrediente (requiere ID)
    private const BASE_INGREDIENTE_PATCH = 'http://localhost:8080';

    /**
     * Obtiene el endpoint para actualizar solo la cantidad de un ingrediente.
     */
    public static function cantidadIngrediente(int $id): string {
        return self::BASE_INGREDIENTE_PATCH . "/{$id}/cantidad";
    }
}


class endpointDelete {
    // Bases de ruta definidas directamente
    private const BASE_INGREDIENTE_DELETE = 'http://localhost:8080/ingrediente';
    private const BASE_PROVEEDOR_DELETE = 'http://localhost:8080/proveedores';
    private const BASE_CATEGORIA_DELETE = 'http://localhost:8080/eliminarcategoria';
    private const BASE_DETALLE_PEDIDO_DELETE = 'http://localhost:8080/detalles-pedidos';


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