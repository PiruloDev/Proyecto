<?php
include 'conexion.php';

// Verificar si se recibió el ID del producto
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_producto = $_GET['id'];
    
    try {
        // Primero, obtener los datos del producto antes de eliminarlo para mostrar confirmación
        $consulta_producto = "SELECT NOMBRE_PRODUCTO FROM Productos WHERE ID_PRODUCTO = :id";
        $stmt_producto = $pdo_conexion->prepare($consulta_producto);
        $stmt_producto->bindParam(':id', $id_producto, PDO::PARAM_INT);
        $stmt_producto->execute();
        $producto = $stmt_producto->fetch(PDO::FETCH_ASSOC);
        
        if ($producto) {
            $nombre_producto = $producto['NOMBRE_PRODUCTO'];
            
            // Verificar si el producto tiene referencias en otras tablas
            $consulta_referencias = "SELECT COUNT(*) as total FROM detalle_pedidos WHERE ID_PRODUCTO = :id";
            $stmt_referencias = $pdo_conexion->prepare($consulta_referencias);
            $stmt_referencias->bindParam(':id', $id_producto, PDO::PARAM_INT);
            $stmt_referencias->execute();
            $referencias = $stmt_referencias->fetch(PDO::FETCH_ASSOC);
            
            if ($referencias['total'] > 0) {
                // El producto tiene referencias, no se puede eliminar
                $mensaje = "No se puede eliminar el producto '{$nombre_producto}' porque está asociado a {$referencias['total']} pedido(s). Para eliminarlo, primero debe eliminar o modificar los pedidos que lo contienen.";
                $tipo_mensaje = "warning";
                $mostrar_opciones = true;
            } else {
                // El producto no tiene referencias, se puede eliminar
                $consulta_eliminar = "DELETE FROM Productos WHERE ID_PRODUCTO = :id";
                $stmt_eliminar = $pdo_conexion->prepare($consulta_eliminar);
                $stmt_eliminar->bindParam(':id', $id_producto, PDO::PARAM_INT);
                
                if ($stmt_eliminar->execute()) {
                    $mensaje = "El producto '{$nombre_producto}' ha sido eliminado exitosamente.";
                    $tipo_mensaje = "success";
                    $mostrar_opciones = false;
                } else {
                    $mensaje = "Error: No se pudo eliminar el producto.";
                    $tipo_mensaje = "error";
                    $mostrar_opciones = false;
                }
            }
        } else {
            $mensaje = "Error: El producto no existe.";
            $tipo_mensaje = "error";
        }
        
    } catch (PDOException $e) {
        // Verificar si es un error de restricción de clave foránea
        if (strpos($e->getMessage(), '1451') !== false) {
            $mensaje = "No se puede eliminar el producto porque está siendo utilizado en pedidos activos. Para eliminarlo, primero debe eliminar o modificar los pedidos que lo contienen.";
            $tipo_mensaje = "warning";
            $mostrar_opciones = true;
        } else {
            $mensaje = "Error en la base de datos: " . $e->getMessage();
            $tipo_mensaje = "error";
            $mostrar_opciones = false;
        }
    }
} else {
    $mensaje = "Error: ID de producto no válido.";
    $tipo_mensaje = "error";
    $mostrar_opciones = false;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Producto - Panadería</title>
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
        
        .warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
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
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #545b62;
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
            color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($tipo_mensaje == 'success'): ?>
            <div class="icon success-icon">✓</div>
            <h1>Eliminación Exitosa</h1>
        <?php else: ?>
            <div class="icon error-icon">✗</div>
            <h1>Error en la Eliminación</h1>
        <?php endif; ?>
        
        <div class="mensaje <?php echo $tipo_mensaje; ?>">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
        
        <div>
            <button id="btnVolver" class="btn btn-primary" onclick="volverALista()" disabled>
                <span id="textoBoton">Aceptar y Volver (5s)</span>
            </button>
            <?php if ($tipo_mensaje == 'success'): ?>
                <button id="btnAgregar" class="btn btn-secondary" onclick="agregarOtro()" disabled>
                    <span id="textoBotonAgregar">Agregar Nuevo Producto (5s)</span>
                </button>
            <?php endif; ?>
        </div>
        
        <div id="countdownWarning" class="countdown-warning">
            ⏱️ Por favor, lee toda la información mostrada arriba antes de continuar. 
            Los botones se activarán en <span id="countdown">5</span> segundos.
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
            
            // Verificar si existe el botón agregar
            const btnAgregar = document.getElementById('btnAgregar');
            const textoBotonAgregar = document.getElementById('textoBotonAgregar');
            
            countdownInterval = setInterval(function() {
                countdownTime--;
                countdownElement.textContent = countdownTime;
                textoBoton.textContent = `Aceptar y Volver (${countdownTime}s)`;
                
                if (btnAgregar && textoBotonAgregar) {
                    textoBotonAgregar.textContent = `Agregar Nuevo Producto (${countdownTime}s)`;
                }
                
                if (countdownTime <= 0) {
                    clearInterval(countdownInterval);
                    btnVolver.disabled = false;
                    textoBoton.textContent = 'Aceptar y Volver';
                    
                    if (btnAgregar && textoBotonAgregar) {
                        btnAgregar.disabled = false;
                        textoBotonAgregar.textContent = 'Agregar Nuevo Producto';
                    }
                    
                    countdownWarning.style.display = 'none';
                }
            }, 1000);
        }
        
        function volverALista() {
            window.location.href = 'productostabla.php';
        }
        
        function agregarOtro() {
            window.location.href = 'productostabla.php#agregar';
        }
        
        // Iniciar countdown cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            startCountdown();
        });
    </script>
</body>
</html>
