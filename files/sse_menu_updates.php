<?php
/**
 * Server-Sent Events (SSE) para actualizaciones en tiempo real del menú
 * Detecta cambios en productos y notifica a los clientes
 */

// Configurar headers para SSE
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Cache-Control');

// Evitar timeout de PHP
set_time_limit(0);
ini_set('max_execution_time', 0);

require_once 'conexion.php';

// Obtener último timestamp verificado
$last_check = floatval($_GET['last_check'] ?? 0);

// Si no hay timestamp, usar el actual menos 1 hora
if ($last_check == 0) {
    $last_check = time() - 3600;
}

/**
 * Función para enviar evento SSE
 */
function sendSSEEvent($type, $data = null, $id = null) {
    if ($id !== null) {
        echo "id: $id\n";
    }
    echo "event: $type\n";
    if ($data !== null) {
        echo "data: " . json_encode($data) . "\n";
    }
    echo "\n";
    ob_flush();
    flush();
}

/**
 * Función para verificar cambios en productos
 */
function verificarCambiosProductos($conexion, $last_check) {
    // Verificar cambios en productos activos
    $query = "
        SELECT 
            COUNT(*) as total_cambios,
            MAX(UNIX_TIMESTAMP(FECHA_ULTIMA_MODIFICACION)) as ultimo_cambio
        FROM Productos 
        WHERE ACTIVO = 1 
        AND UNIX_TIMESTAMP(FECHA_ULTIMA_MODIFICACION) > ?
    ";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("d", $last_check);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    return $result;
}

/**
 * Función para obtener detalles de productos modificados
 */
function obtenerProductosModificados($conexion, $last_check, $limit = 10) {
    $query = "
        SELECT 
            p.ID_PRODUCTO,
            p.NOMBRE_PRODUCTO,
            p.PRECIO_PRODUCTO,
            p.PRODUCTO_STOCK_MIN,
            p.ACTIVO,
            c.NOMBRE_CATEGORIA_PRODUCTO as categoria,
            UNIX_TIMESTAMP(p.FECHA_ULTIMA_MODIFICACION) as timestamp_modificacion
        FROM Productos p
        LEFT JOIN Categoria_Productos c ON p.ID_CATEGORIA_PRODUCTO = c.ID_CATEGORIA_PRODUCTO
        WHERE UNIX_TIMESTAMP(p.FECHA_ULTIMA_MODIFICACION) > ?
        ORDER BY p.FECHA_ULTIMA_MODIFICACION DESC
        LIMIT ?
    ";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("di", $last_check, $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Función para verificar cambios en logs recientes
 */
function verificarLogsRecientes($conexion, $last_check) {
    $query = "
        SELECT 
            pl.*,
            p.NOMBRE_PRODUCTO
        FROM productos_logs pl
        JOIN Productos p ON pl.producto_id = p.ID_PRODUCTO
        WHERE UNIX_TIMESTAMP(pl.fecha_cambio) > ?
        AND p.ACTIVO = 1
        ORDER BY pl.fecha_cambio DESC
        LIMIT 20
    ";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("d", $last_check);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

try {
    // Enviar evento inicial de conexión
    sendSSEEvent('connected', [
        'message' => 'Conectado al sistema de actualizaciones',
        'timestamp' => time()
    ]);
    
    $iteration = 0;
    $max_iterations = 300; // 5 minutos con intervalos de 1 segundo
    
    while ($iteration < $max_iterations) {
        // Verificar cambios en productos
        $cambios = verificarCambiosProductos($conexion, $last_check);
        
        if ($cambios['total_cambios'] > 0) {
            // Hay cambios, obtener detalles
            $productos_modificados = obtenerProductosModificados($conexion, $last_check);
            $logs_recientes = verificarLogsRecientes($conexion, $last_check);
            
            // Enviar evento de actualización del menú
            sendSSEEvent('menu_update', [
                'type' => 'productos_actualizados',
                'total_cambios' => $cambios['total_cambios'],
                'ultimo_cambio' => $cambios['ultimo_cambio'],
                'productos' => $productos_modificados,
                'logs' => $logs_recientes,
                'timestamp' => time()
            ]);
            
            // Actualizar last_check al timestamp más reciente
            $last_check = $cambios['ultimo_cambio'];
        }
        
        // Verificar si la conexión sigue activa
        if (connection_aborted()) {
            break;
        }
        
        // Enviar heartbeat cada 30 segundos
        if ($iteration % 30 == 0 && $iteration > 0) {
            sendSSEEvent('heartbeat', [
                'timestamp' => time(),
                'iteration' => $iteration
            ]);
        }
        
        // Esperar 1 segundo antes de la siguiente verificación
        sleep(1);
        $iteration++;
    }
    
    // Enviar evento de desconexión
    sendSSEEvent('disconnected', [
        'message' => 'Sesión SSE finalizada',
        'timestamp' => time()
    ]);
    
} catch (Exception $e) {
    // Enviar evento de error
    sendSSEEvent('error', [
        'message' => 'Error en el servidor: ' . $e->getMessage(),
        'timestamp' => time()
    ]);
}

// Cerrar la conexión
if (isset($conexion)) {
    $conexion->close();
}
?>
