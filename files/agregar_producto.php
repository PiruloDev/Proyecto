<?php
include 'conexion.php';

// Verificar si se enviaron los datos del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Obtener y validar los datos del formulario
    $nombre_producto = trim($_POST['nombre_producto']);
    $precio_producto = $_POST['precio_producto'];
    $stock_min = $_POST['stock_min'];
    $tipo_producto_marca = trim($_POST['tipo_producto_marca']);
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    
    // Validaciones básicas
    $errores = [];
    
    if (empty($nombre_producto)) {
        $errores[] = "El nombre del producto es obligatorio.";
    }
    
    if (!is_numeric($precio_producto) || $precio_producto <= 0) {
        $errores[] = "El precio debe ser un número mayor a 0.";
    }
    
    if (!is_numeric($stock_min) || $stock_min < 0) {
        $errores[] = "El stock mínimo debe ser un número mayor o igual a 0.";
    }
    
    if (empty($tipo_producto_marca)) {
        $errores[] = "La marca/tipo del producto es obligatoria.";
    }
    
    if (empty($fecha_vencimiento)) {
        $errores[] = "La fecha de vencimiento es obligatoria.";
    } else {
        // Verificar que la fecha de vencimiento sea posterior a hoy
        $fecha_actual = date('Y-m-d');
        if ($fecha_vencimiento <= $fecha_actual) {
            $errores[] = "La fecha de vencimiento debe ser posterior a la fecha actual.";
        }
    }
    
    // Si no hay errores, proceder con la inserción
    if (empty($errores)) {
        try {
            // Preparar la consulta de inserción
            // Nota: ID_ADMIN e ID_CATEGORIA_PRODUCTO se pueden dejar NULL por ahora
            // FECHA_INGRESO_PRODUCTO se establece como la fecha actual
            $consulta = "INSERT INTO Productos (
                NOMBRE_PRODUCTO, 
                PRODUCTO_STOCK_MIN, 
                PRECIO_PRODUCTO, 
                FECHA_VENCIMIENTO_PRODUCTO, 
                FECHA_INGRESO_PRODUCTO, 
                TIPO_PRODUCTO_MARCA,
                ACTIVO
            ) VALUES (
                :nombre_producto, 
                :stock_min, 
                :precio_producto, 
                :fecha_vencimiento, 
                :fecha_ingreso, 
                :tipo_producto_marca,
                1
            )";
            
            $stmt = $pdo_conexion->prepare($consulta);
            
            // Vincular parámetros
            $stmt->bindParam(':nombre_producto', $nombre_producto, PDO::PARAM_STR);
            $stmt->bindParam(':stock_min', $stock_min, PDO::PARAM_INT);
            $stmt->bindParam(':precio_producto', $precio_producto, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_vencimiento', $fecha_vencimiento, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_ingreso', date('Y-m-d'), PDO::PARAM_STR);
            $stmt->bindParam(':tipo_producto_marca', $tipo_producto_marca, PDO::PARAM_STR);
            
            // Ejecutar la consulta
            if ($stmt->execute()) {
                $id_producto_creado = $pdo_conexion->lastInsertId();
                $mensaje = "¡Producto agregado exitosamente! ID del producto: {$id_producto_creado}";
                $tipo_mensaje = "success";
            } else {
                $mensaje = "Error: No se pudo agregar el producto.";
                $tipo_mensaje = "error";
            }
            
        } catch (PDOException $e) {
            $mensaje = "Error en la base de datos: " . $e->getMessage();
            $tipo_mensaje = "error";
        }
    } else {
        // Hay errores de validación
        $mensaje = "Errores encontrados:<br>• " . implode("<br>• ", $errores);
        $tipo_mensaje = "error";
    }
} else {
    // Si se accede directamente sin POST, redirigir
    header("Location: productostabla.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto - Panadería</title>
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
            text-align: left;
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
        
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #218838;
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
        
        .producto-info {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }
        
        .producto-info h4 {
            margin-top: 0;
            color: #495057;
        }
        
        .producto-info p {
            margin: 5px 0;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($tipo_mensaje == 'success'): ?>
            <div class="icon success-icon">✓</div>
            <h1>¡Producto Agregado!</h1>
            
            <div class="mensaje success">
                <?php echo $mensaje; ?>
            </div>
            
            <div class="producto-info">
                <h4>Detalles del Producto Agregado:</h4>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombre_producto); ?></p>
                <p><strong>Precio:</strong> $<?php echo number_format($precio_producto, 2); ?></p>
                <p><strong>Stock Mínimo:</strong> <?php echo htmlspecialchars($stock_min); ?></p>
                <p><strong>Marca/Tipo:</strong> <?php echo htmlspecialchars($tipo_producto_marca); ?></p>
                <p><strong>Fecha de Vencimiento:</strong> <?php echo date('d/m/Y', strtotime($fecha_vencimiento)); ?></p>
                <p><strong>Fecha de Ingreso:</strong> <?php echo date('d/m/Y'); ?></p>
            </div>
            
        <?php else: ?>
            <div class="icon error-icon">✗</div>
            <h1>Error al Agregar Producto</h1>
            
            <div class="mensaje error">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <div>
            <button id="btnVolver" class="btn btn-primary" onclick="volverALista()" disabled>
                <span id="textoBoton">Ver Lista de Productos (5s)</span>
            </button>
            
            <?php if ($tipo_mensaje == 'success'): ?>
                <button id="btnAgregar" class="btn btn-success" onclick="agregarOtro()" disabled>
                    <span id="textoBotonAgregar">Agregar Otro Producto (5s)</span>
                </button>
            <?php else: ?>
                <button id="btnVolver2" class="btn btn-secondary" onclick="volverAtras()" disabled>
                    <span id="textoBotonVolver">Volver al Formulario (5s)</span>
                </button>
            <?php endif; ?>
        </div>
        
        <div id="countdownWarning" class="countdown-warning">
            ⏱️ Por favor, lea la información mostrada arriba antes de continuar. 
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
            
            // Verificar si existen los otros botones
            const btnAgregar = document.getElementById('btnAgregar');
            const textoBotonAgregar = document.getElementById('textoBotonAgregar');
            const btnVolver2 = document.getElementById('btnVolver2');
            const textoBotonVolver = document.getElementById('textoBotonVolver');
            
            countdownInterval = setInterval(function() {
                countdownTime--;
                countdownElement.textContent = countdownTime;
                textoBoton.textContent = `Ver Lista de Productos (${countdownTime}s)`;
                
                if (btnAgregar && textoBotonAgregar) {
                    textoBotonAgregar.textContent = `Agregar Otro Producto (${countdownTime}s)`;
                }
                
                if (btnVolver2 && textoBotonVolver) {
                    textoBotonVolver.textContent = `Volver al Formulario (${countdownTime}s)`;
                }
                
                if (countdownTime <= 0) {
                    clearInterval(countdownInterval);
                    btnVolver.disabled = false;
                    textoBoton.textContent = 'Ver Lista de Productos';
                    
                    if (btnAgregar && textoBotonAgregar) {
                        btnAgregar.disabled = false;
                        textoBotonAgregar.textContent = 'Agregar Otro Producto';
                    }
                    
                    if (btnVolver2 && textoBotonVolver) {
                        btnVolver2.disabled = false;
                        textoBotonVolver.textContent = 'Volver al Formulario';
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
        
        function volverAtras() {
            history.back();
        }
        
        // Iniciar countdown cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            startCountdown();
        });
    </script>
</body>
</html>
