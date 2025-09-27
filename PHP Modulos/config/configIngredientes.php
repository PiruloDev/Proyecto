<?php

class endpointBase {
    protected const API_BASE_URL = 'http://localhost:8080';
}

class endpointGet extends endpointBase {
    // Endpoints para obtener detalles
    const API_GET_INGREDIENTES = self::API_BASE_URL . '/ingredientes';
    const API_GET_INGREDIENTES_LISTA = self::API_BASE_URL . '/ingredientes/lista';
    const API_GET_PROVEEDORES = self::API_BASE_URL . '/proveedores';
    const API_GET_CATEGORIAS = self::API_BASE_URL . '/categorias/ingredientes';

    
}

class endpointPost extends endpointBase {
    // Endpoints para crear recursos
    const API_CREAR_INGREDIENTE = self::API_BASE_URL . '/crearingrediente';
    const API_CREAR_PROVEEDOR = self::API_BASE_URL . '/proveedores';
    const API_CREAR_CATEGORIA = self::API_BASE_URL . '/nuevacategoriaingrediente';

}

class endpointPut extends endpointBase {
    // Endpoints base para actualizar, requieren un ID
    const API_ACTUALIZAR_INGREDIENTE = self::API_BASE_URL . '/ingrediente';
    const API_ACTUALIZAR_PROVEEDOR = self::API_BASE_URL . '/proveedores';
    const API_ACTUALIZAR_CATEGORIA = self::API_BASE_URL . '/categoriaingrediente';

    public static function ingrediente(int $id): string {
        return self::API_ACTUALIZAR_INGREDIENTE . "/{$id}";
    }
    
    public static function proveedor(int $id): string {
        return self::API_ACTUALIZAR_PROVEEDOR . "/{$id}";
    }

    public static function categoria(int $id): string {
        return self::API_ACTUALIZAR_CATEGORIA . "/{$id}";
    }


}

class endpointPatch extends endpointBase {
    const API_ACTUALIZAR_CANTIDAD_INGREDIENTE = self::API_BASE_URL;

    public static function cantidadIngrediente(int $id): string {
        return self::API_ACTUALIZAR_CANTIDAD_INGREDIENTE . "/{$id}/cantidad";
    }

}

class endpointDelete extends endpointBase {
    // Endpoints base para eliminar, requieren un ID
    const API_ELIMINAR_INGREDIENTE = self::API_BASE_URL . '/ingrediente';
    const API_ELIMINAR_PROVEEDOR = self::API_BASE_URL . '/proveedores';
    const API_ELIMINAR_CATEGORIA = self::API_BASE_URL . '/eliminarcategoria';

    public static function ingrediente(int $id): string {
        return self::API_ELIMINAR_INGREDIENTE . "/{$id}";
    }

    public static function proveedor(int $id): string {
        return self::API_ELIMINAR_PROVEEDOR . "/{$id}";
    }

    public static function categoria(int $id): string {
        return self::API_ELIMINAR_CATEGORIA . "/{$id}";
    }
}