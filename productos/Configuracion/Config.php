<?php
class endpointDetalles {
    const API_DETALLE_ADMIN = 'http://localhost:8080/detalle/administrador';
    const API_DETALLE_USUARIO = 'http://localhost:8080/detalle/usuario';
}

class endpointCreacion {
    const API_CREAR_ADMIN = 'http://localhost:8080/crear/administrador';
    const API_CREAR_USUARIO = 'http://localhost:8080/crear/usuario';
}

class endpointActualizacion {
    const API_ACTUALIZAR_ADMIN = 'http://localhost:8080/actualizar/administrador';
    const API_ACTUALIZAR_USUARIO = 'http://localhost:8080/actualizar/usuario';

    public static function admin($id): string {
        return self::API_ACTUALIZAR_ADMIN . "/$id";
    }
    public static function usuario($id): string {
        return self::API_ACTUALIZAR_USUARIO . "/$id";
    }
}

class endpointEliminacion {
    const API_ELIMINAR_ADMIN = 'http://localhost:8080/eliminar/administrador';
    const API_ELIMINAR_USUARIO = 'http://localhost:8080/eliminar/usuario';

    public static function admin($id): string {
        return self::API_ELIMINAR_ADMIN . "/$id";
    }
    public static function usuario($id): string {
        return self::API_ELIMINAR_USUARIO . "/$id";
    }
}
