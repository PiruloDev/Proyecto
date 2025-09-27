<?php
class endpointDetalles {
    const API_DETALLE_PRODUCTO = 'http://localhost:8080/detalle/producto';
}

class endpointCreacion {
    const API_CREAR_PRODUCTO = 'http://localhost:8080/crear/producto';
}

class endpointActualizacion {
    const API_ACTUALIZAR_PRODUCTO = 'http://localhost:8080/actualizar/producto';

    public static function producto($id): string {
        return self::API_ACTUALIZAR_PRODUCTO . "/$id";
    }
}

class endpointEliminacion {
    const API_ELIMINAR_PRODUCTO = 'http://localhost:8080/eliminar/producto';

    public static function producto($id): string {
        return self::API_ELIMINAR_PRODUCTO . "/$id";
    }
}
