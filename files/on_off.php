<?php
include 'conexion.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('Método no permitido. Use POST.'); window.history.back();</script>";
    exit();
}

// Verificar si se recibió el ID del producto y el estado actual
if (isset($_POST['id']) && !empty($_POST['id']) && isset($_POST['estado'])) {
    $id_producto = $_POST['id'];
    $estado_actual = $_POST['estado'];
    
    // Determinar el nuevo estado (invertir el actual)
    $nuevo_estado = ($estado_actual == 1) ? 0 : 1;
    $accion_texto = ($nuevo_estado == 1) ? "activado" : "desactivado";
    
    try {
        // Obtener información del producto antes del cambio
        $consulta_producto = "SELECT NOMBRE_PRODUCTO, ACTIVO FROM Productos WHERE ID_PRODUCTO = :id";
        $stmt_producto = $pdo_conexion->prepare($consulta_producto);
        $stmt_producto->bindParam(':id', $id_producto, PDO::PARAM_INT);
        $stmt_producto->execute();
        $producto = $stmt_producto->fetch(PDO::FETCH_ASSOC);
        
        if ($producto) {
            $nombre_producto = $producto['NOMBRE_PRODUCTO'];
            
            // Actualizar el estado del producto
            $consulta_update = "UPDATE Productos SET ACTIVO = :nuevo_estado WHERE ID_PRODUCTO = :id";
            $stmt_update = $pdo_conexion->prepare($consulta_update);
            $stmt_update->bindParam(':nuevo_estado', $nuevo_estado, PDO::PARAM_INT);
            $stmt_update->bindParam(':id', $id_producto, PDO::PARAM_INT);
            
            if ($stmt_update->execute()) {
                $mensaje = "El producto '{$nombre_producto}' ha sido {$accion_texto} exitosamente.";
                $tipo_mensaje = "success";
            } else {
                $mensaje = "Error: No se pudo cambiar el estado del producto.";
                $tipo_mensaje = "error";
            }
        } else {
            $mensaje = "Error: El producto no existe.";
            $tipo_mensaje = "error";
        }
        
    } catch (PDOException $e) {
        $mensaje = "Error en la base de datos: " . $e->getMessage();
        $tipo_mensaje = "error";
    }
} else {
    $mensaje = "Error: Parámetros no válidos.";
    $tipo_mensaje = "error";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Estado - Panadería</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 600px;
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
        
        .info-box {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }
        
        .info-box h4 {
            margin-top: 0;
            color: #495057;
        }
        
        .estado-activo {
            color: #28a745;
            font-weight: bold;
        }
        
        .estado-inactivo {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($tipo_mensaje == 'success'): ?>
            <div class="icon success-icon">✓</div>
            <h1>Estado Cambiado</h1>
        <?php else: ?>
            <div class="icon error-icon">✗</div>
            <h1>Error al Cambiar Estado</h1>
        <?php endif; ?>
        
        <div class="mensaje <?php echo $tipo_mensaje; ?>">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
        
        <?php if ($tipo_mensaje == 'success'): ?>
            <div class="info-box">
                <h4>Información del Cambio:</h4>
                <p><strong>Producto:</strong> <?php echo htmlspecialchars($nombre_producto); ?></p>
                <p><strong>Nuevo Estado:</strong> 
                    <?php if ($nuevo_estado == 1): ?>
                        <span class="estado-activo">✓ Activo</span>
                    <?php else: ?>
                        <span class="estado-inactivo">✗ Inactivo</span>
                    <?php endif; ?>
                </p>
                <p><strong>Fecha del Cambio:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
            </div>
            
            <div style="background-color: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid #ffeaa7;">
                <h4 style="color: #856404; margin-top: 0;">💡 Información Importante:</h4>
                <ul style="text-align: left; color: #856404;">
                    <?php if ($nuevo_estado == 0): ?>
                        <li>El producto está ahora <strong>INACTIVO</strong> y no aparecerá en búsquedas normales</li>
                        <li>Los pedidos existentes que contienen este producto NO se ven afectados</li>
                        <li>Puedes reactivarlo en cualquier momento</li>
                    <?php else: ?>
                        <li>El producto está ahora <strong>ACTIVO</strong> y disponible para nuevos pedidos</li>
                        <li>Aparecerá en todas las búsquedas y listados normales</li>
                        <li>Los clientes pueden volver a pedirlo</li>
                    <?php endif; ?>
                </ul>
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
