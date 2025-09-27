<?php
class endpointDetalles {
    const API_DETALLE_ADMINISTRADOR = 'http://localhost:8080/detalle/administrador';
    const API_DETALLE_CLIENTE = 'http://localhost:8080/detalle/cliente';
    const API_DETALLE_EMPLEADO = 'http://localhost:8080/detalle/empleado';
}

class endpointCreacion {
    const API_CREAR_ADMINISTRADOR = 'http://localhost:8080/crear/administrador';
    const API_CREAR_CLIENTE = 'http://localhost:8080/crear/cliente';
    const API_CREAR_EMPLEADO = 'http://localhost:8080/crear/empleado';
}
class endopointActualizacion {
    const API_ACTUALIZAR_ADMINISTRADOR = 'http://localhost:8080/actualizar/administrador';
    const API_ACTUALIZAR_CLIENTE = 'http://localhost:8080/actualizar/cliente';
    const API_ACTUALIZAR_EMPLEADO = 'http://localhost:8080/actualizar/empleado';

    public static function administrador(int $id): string {
    return self::API_ACTUALIZAR_ADMINISTRADOR . "/{$id}";
    }

    public static function cliente(int $id): string {
        return self::API_ACTUALIZAR_CLIENTE . "/{$id}";
    }

    public static function empleado(int $id): string {
        return self::API_ACTUALIZAR_EMPLEADO . "/{$id}";
    }
}
class endpointEliminacion {
    const API_ELIMINAR_ADMINISTRADOR = 'http://localhost:8080/eliminar/administrador';
    const API_ELIMINAR_CLIENTE = 'http://localhost:8080/eliminar/cliente';
    const API_ELIMINAR_EMPLEADO = 'http://localhost:8080/eliminar/empleado';

    public static function administrador(int $id): string {
        return self::API_ELIMINAR_ADMINISTRADOR . "/{$id}";
    }

    public static function cliente(int $id): string {
        return self::API_ELIMINAR_CLIENTE . "/{$id}";
    }

    public static function empleado(int $id): string {
        return self::API_ELIMINAR_EMPLEADO . "/{$id}";
    }
}
?>
