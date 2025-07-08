<?php
include 'conexion.php';

// Verificar si se recibió el ID del producto
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_producto = $_GET['id'];
    
    try {
        // Obtener información del producto antes de eliminarlo
        $consulta_producto = "SELECT * FROM Productos WHERE ID_PRODUCTO = :id";
        $stmt_producto = $pdo_conexion->prepare($consulta_producto);
        $stmt_producto->bindParam(':id', $id_producto, PDO::PARAM_INT);
        $stmt_producto->execute();
        $producto = $stmt_producto->fetch(PDO::FETCH_ASSOC);
        
        if ($producto) {
            $nombre_producto = $producto['NOMBRE_PRODUCTO'];
            
            // Verificar si tiene referencias (para informar al usuario)
            $consulta_referencias = "SELECT COUNT(*) as total FROM detalle_pedidos WHERE ID_PRODUCTO = :id";
            $stmt_referencias = $pdo_conexion->prepare($consulta_referencias);
            $stmt_referencias->bindParam(':id', $id_producto, PDO::PARAM_INT);
            $stmt_referencias->execute();
            $referencias = $stmt_referencias->fetch(PDO::FETCH_ASSOC);
            
            // ELIMINACIÓN FORZADA - Ignora las restricciones de clave foránea
            // Primero deshabilitar temporalmente las verificaciones de clave foránea
            $pdo_conexion->exec("SET FOREIGN_KEY_CHECKS = 0");
            
            // Eliminar el producto
            $consulta_eliminar = "DELETE FROM Productos WHERE ID_PRODUCTO = :id";
            $stmt_eliminar = $pdo_conexion->prepare($consulta_eliminar);
            $stmt_eliminar->bindParam(':id', $id_producto, PDO::PARAM_INT);
            
            if ($stmt_eliminar->execute()) {
                // Reactivar las verificaciones de clave foránea
                $pdo_conexion->exec("SET FOREIGN_KEY_CHECKS = 1");
                
                $mensaje = "El producto '{$nombre_producto}' ha sido ELIMINADO PERMANENTEMENTE.";
                $tipo_mensaje = "success";
                $referencias_afectadas = $referencias['total'];
            } else {
                // Reactivar las verificaciones de clave foránea en caso de error
                $pdo_conexion->exec("SET FOREIGN_KEY_CHECKS = 1");
                $mensaje = "Error: No se pudo eliminar el producto.";
                $tipo_mensaje = "error";
                $referencias_afectadas = 0;
            }
        } else {
            $mensaje = "Error: El producto no existe.";
            $tipo_mensaje = "error";
            $referencias_afectadas = 0;
        }
        
    } catch (PDOException $e) {
        // Asegurar que las verificaciones de clave foránea se reactiven en caso de error
        try {
            $pdo_conexion->exec("SET FOREIGN_KEY_CHECKS = 1");
        } catch (Exception $ex) {
            // Ignorar errores al reactivar
        }
        
        $mensaje = "Error en la base de datos: " . $e->getMessage();
        $tipo_mensaje = "error";
        $referencias_afectadas = 0;
    }
} else {
    $mensaje = "Error: ID de producto no válido.";
    $tipo_mensaje = "error";
    $referencias_afectadas = 0;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminación Forzada - Panadería</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 700px;
            margin: 50px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .mensaje {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 16px;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 2px solid #dc3545;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0056b3;
        }
        
        .btn:disabled {
            background-color: #6c757d !important;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .btn:disabled:hover {
            background-color: #6c757d !important;
        }
        
        .countdown-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 2px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
            font-weight: bold;
        }
        
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        
        .icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .success-icon {
            color: #28a745;
        }
        
        .error-icon {
            color: #dc3545;
        }
        
        .warning-icon {
            color: #dc3545;
        }
        
        .info-box {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }
        
        .danger-box {
            background-color: #f8d7da;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
            border: 2px solid #dc3545;
        }
        
        .danger-box h4 {
            color: #721c24;
            margin-top: 0;
        }
        
        .danger-box ul {
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($tipo_mensaje == 'success'): ?>
            <div class="icon warning-icon">⚠️</div>
            <h1>Producto Eliminado Permanentemente</h1>
            
            <div class="mensaje success">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
            
            <?php if ($referencias_afectadas > 0): ?>
                <div class="danger-box">
                    <h4>🚨 ADVERTENCIA CRÍTICA:</h4>
                    <p><strong>Este producto tenía <?php echo $referencias_afectadas; ?> referencia(s) en pedidos.</strong></p>
                    <p>La eliminación forzada puede haber causado:</p>
                    <ul>
                        <li>❌ Pedidos con productos "huérfanos" (referencias rotas)</li>
                        <li>❌ Posibles errores en reportes de ventas</li>
                        <li>❌ Inconsistencias en el historial de transacciones</li>
                        <li>❌ Problemas en cálculos de inventario</li>
                    </ul>
                    <p><strong>Recomendación:</strong> Revise inmediatamente la tabla de pedidos para verificar la integridad de los datos.</p>
                </div>
            <?php else: ?>
                <div class="info-box">
                    <h4>✅ Eliminación Segura:</h4>
                    <p>El producto no tenía referencias en otras tablas, por lo que la eliminación no causó inconsistencias en la base de datos.</p>
                </div>
            <?php endif; ?>
            
            <div class="info-box">
                <h4>Detalles del Producto Eliminado:</h4>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombre_producto); ?></p>
                <p><strong>ID:</strong> <?php echo htmlspecialchars($id_producto); ?></p>
                <p><strong>Fecha de Eliminación:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
                <p><strong>Referencias Afectadas:</strong> <?php echo $referencias_afectadas; ?></p>
            </div>
            
        <?php else: ?>
            <div class="icon error-icon">✗</div>
            <h1>Error en la Eliminación</h1>
            
            <div class="mensaje error">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <div>
            <button id="btnVolver" class="btn btn-primary" onclick="volverALista()" disabled>
                <span id="textoBoton">Aceptar y Volver (5s)</span>
            </button>
        </div>
        
        <div id="countdownWarning" class="countdown-warning">
            ⏱️ Por favor, lee toda la información mostrada arriba antes de continuar. 
            El botón se activará en <span id="countdown">5</span> segundos.
        </div>
        
        <?php if ($tipo_mensaje == 'success' && $referencias_afectadas > 0): ?>
            <div style="margin-top: 30px; padding: 15px; background-color: #fff3cd; border-radius: 8px; border: 1px solid #ffeaa7;">
                <h4 style="color: #856404; margin-top: 0;">🔧 Acciones Recomendadas:</h4>
                <ul style="text-align: left; color: #856404;">
                    <li>Revisar la tabla de pedidos para identificar referencias rotas</li>
                    <li>Contactar al administrador de la base de datos</li>
                    <li>Considerar implementar un proceso de limpieza de datos</li>
                    <li>En el futuro, use la opción "Desactivar" en lugar de eliminar</li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        let countdownTime = 5;
        let countdownInterval;
        
        function startCountdown() {
            const countdownElement = document.getElementById('countdown');
            const btnVolver = document.getElementById('btnVolver');
            const textoBoton = document.getElementById('textoBoton');
            const countdownWarning = document.getElementById('countdownWarning');
            
            countdownInterval = setInterval(function() {
                countdownTime--;
                countdownElement.textContent = countdownTime;
                textoBoton.textContent = `Aceptar y Volver (${countdownTime}s)`;
                
                if (countdownTime <= 0) {
                    clearInterval(countdownInterval);
                    btnVolver.disabled = false;
                    btnVolver.classList.remove('btn-disabled');
                    textoBoton.textContent = 'Aceptar y Volver';
                    countdownWarning.style.display = 'none';
                }
            }, 1000);
        }
        
        function volverALista() {
            window.location.href = 'productostabla.php';
        }
        
        // Iniciar countdown cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            startCountdown();
        });
    </script>
</body>
</html>
