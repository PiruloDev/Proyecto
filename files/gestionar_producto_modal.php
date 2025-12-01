<?php
include 'conexion.php';

// Verificar si se recibió el ID del producto
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_producto = $_GET['id'];
    
    try {
        // Obtener información del producto
        $consulta_producto = "SELECT 
            ID_PRODUCTO, 
            NOMBRE_PRODUCTO, 
            PRECIO_PRODUCTO, 
            PRODUCTO_STOCK_MIN, 
            TIPO_PRODUCTO_MARCA, 
            FECHA_VENCIMIENTO_PRODUCTO,
            COALESCE(ACTIVO, 1) as ACTIVO
            FROM Productos 
            WHERE ID_PRODUCTO = :id";
        $stmt_producto = $pdo_conexion->prepare($consulta_producto);
        $stmt_producto->bindParam(':id', $id_producto, PDO::PARAM_INT);
        $stmt_producto->execute();
        $producto = $stmt_producto->fetch(PDO::FETCH_ASSOC);
        
        if (!$producto) {
            header("Location: productostabla.php");
            exit();
        }
        
        // Verificar referencias en detalle_pedidos
        $consulta_referencias = "SELECT COUNT(*) as total FROM detalle_pedidos WHERE ID_PRODUCTO = :id";
        $stmt_referencias = $pdo_conexion->prepare($consulta_referencias);
        $stmt_referencias->bindParam(':id', $id_producto, PDO::PARAM_INT);
        $stmt_referencias->execute();
        $referencias = $stmt_referencias->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        header("Location: productostabla.php");
        exit();
    }
} else {
    header("Location: productostabla.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Producto - Panadería</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="styleproductostabla.css">
    <style>
        .producto-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .producto-header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 300;
        }
        
        .producto-header .producto-id {
            font-size: 1.2em;
            opacity: 0.9;
            margin-top: 10px;
        }
        
        .producto-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .detail-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }
        
        .detail-card h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.3em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #555;
        }
        
        .detail-value {
            color: #333;
            font-weight: 400;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-activo {
            background: #d4edda;
            color: #155724;
        }
        
        .status-inactivo {
            background: #f8d7da;
            color: #721c24;
        }
        
        .price-value {
            font-size: 1.2em;
            font-weight: 700;
            color: #28a745;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .action-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.2s ease;
        }
        
        .action-card:hover {
            transform: translateY(-2px);
        }
        
        .action-card h4 {
            margin-bottom: 15px;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .action-card p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .btn-action-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-action-card.warning {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        }
        
        .btn-action-card.danger {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }
        
        .btn-action-card.success {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
        }
        
        .warning-zone {
            background: #fff3cd;
            border: 2px solid #ffeaa7;
            border-radius: 12px;
            padding: 25px;
            margin-top: 30px;
            text-align: center;
        }
        
        .warning-zone h4 {
            color: #856404;
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        
        .warning-zone p {
            color: #856404;
            margin-bottom: 20px;
        }
        
        .references-info {
            background: #f8d7da;
            border: 2px solid #f5c6cb;
            border-radius: 12px;
            padding: 25px;
            margin: 20px 0;
            text-align: center;
        }
        
        .references-info h4 {
            color: #721c24;
            margin-bottom: 15px;
        }
        
        .references-info p {
            color: #721c24;
            margin-bottom: 0;
        }
        
        .back-actions {
            text-align: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #eee;
        }
        
        .btn-back {
            background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            margin: 0 10px;
        }
        
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(149, 165, 166, 0.4);
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        }
        
        .btn-edit:hover {
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="producto-header">
            <h1><?php echo htmlspecialchars($producto['NOMBRE_PRODUCTO']); ?></h1>
            <div class="producto-id">ID: <?php echo htmlspecialchars($producto['ID_PRODUCTO']); ?></div>
        </div>
        
        <div class="producto-details">
            <div class="detail-card">
                <h3>
                    <span class="material-symbols-outlined">info</span>
                    Información General
                </h3>
                <div class="detail-row">
                    <span class="detail-label">Precio:</span>
                    <span class="detail-value price-value">$<?php echo number_format($producto['PRECIO_PRODUCTO'], 2); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Stock:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($producto['PRODUCTO_STOCK_MIN']); ?> unidades</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Categoría:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($producto['TIPO_PRODUCTO_MARCA']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Estado:</span>
                    <span class="detail-value">
                        <?php if ($producto['ACTIVO'] == 1): ?>
                            <span class="status-badge status-activo">
                                <span class="material-symbols-outlined" style="font-size: 16px;">check_circle</span>
                                Activo
                            </span>
                        <?php else: ?>
                            <span class="status-badge status-inactivo">
                                <span class="material-symbols-outlined" style="font-size: 16px;">cancel</span>
                                Inactivo
                            </span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            
            <div class="detail-card">
                <h3>
                    <span class="material-symbols-outlined">schedule</span>
                    Fechas y Vencimiento
                </h3>
                <div class="detail-row">
                    <span class="detail-label">Fecha de Vencimiento:</span>
                    <span class="detail-value"><?php echo date('d/m/Y', strtotime($producto['FECHA_VENCIMIENTO_PRODUCTO'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Referencias en Pedidos:</span>
                    <span class="detail-value">
                        <?php if ($referencias['total'] > 0): ?>
                            <span style="color: #e74c3c; font-weight: 600;">
                                <?php echo $referencias['total']; ?> pedido(s)
                            </span>
                        <?php else: ?>
                            <span style="color: #27ae60; font-weight: 600;">
                                Sin referencias
                            </span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
        
        <?php if ($referencias['total'] > 0): ?>
            <div class="references-info">
                <h4>
                    <span class="material-symbols-outlined">warning</span>
                    Producto con Referencias
                </h4>
                <p>Este producto está siendo utilizado en <strong><?php echo $referencias['total']; ?></strong> pedido(s). No se puede eliminar directamente.</p>
            </div>
        <?php endif; ?>
        
        <div class="actions-grid">
            <?php if ($referencias['total'] == 0): ?>
                <div class="action-card">
                    <h4>
                        <span class="material-symbols-outlined">delete</span>
                        Eliminar Producto
                    </h4>
                    <p>Elimina permanentemente el producto. Esta acción no se puede deshacer.</p>
                    <button class="btn-action-card danger" onclick="eliminarProducto(<?php echo $producto['ID_PRODUCTO']; ?>, '<?php echo addslashes($producto['NOMBRE_PRODUCTO']); ?>')">
                        <span class="material-symbols-outlined">delete</span>
                        Eliminar
                    </button>
                </div>
            <?php endif; ?>
            
            <div class="action-card">
                <h4>
                    <span class="material-symbols-outlined">visibility</span>
                    Ver Historial
                </h4>
                <p>Consulta el historial de pedidos y movimientos de este producto.</p>
                <a href="historial_producto.php?id=<?php echo $producto['ID_PRODUCTO']; ?>" class="btn-action-card">
                    <span class="material-symbols-outlined">visibility</span>
                    Ver Historial
                </a>
            </div>
        </div>
        
        <div class="back-actions">
            <a href="productostabla.php" class="btn-back">
                <span class="material-symbols-outlined">arrow_back</span>
                Volver a la Lista
            </a>
        </div>
    </div>
    
    <!-- Modal para mostrar resultados -->
    <div id="modal-resultado" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-titulo">Resultado</h2>
                <button class="modal-close" onclick="cerrarModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="modal-icono" class="modal-icon">✓</div>
                <div id="modal-mensaje" class="modal-message"></div>
                <div id="modal-acciones" class="modal-actions">
                    <button class="modal-btn primary" onclick="cerrarModal()">Aceptar</button>
                </div>
                <div id="modal-countdown" class="countdown-info" style="display: none;">
                    ⏱️ Esta ventana se cerrará automáticamente en <span id="countdown-segundos">5</span> segundos.
                </div>
            </div>
        </div>
    </div>

    <script>
        // Variables globales para el modal
        let modalCountdown;
        let modalCountdownTime = 5;

        // Función para mostrar modal
        function mostrarModal(tipo, titulo, mensaje, acciones = null, autoClose = true) {
            const modal = document.getElementById('modal-resultado');
            const modalTitulo = document.getElementById('modal-titulo');
            const modalIcono = document.getElementById('modal-icono');
            const modalMensaje = document.getElementById('modal-mensaje');
            const modalAcciones = document.getElementById('modal-acciones');
            const modalCountdownDiv = document.getElementById('modal-countdown');
            
            // Configurar contenido
            modalTitulo.textContent = titulo;
            modalMensaje.innerHTML = mensaje;
            
            // Configurar icono según tipo
            modalIcono.className = `modal-icon ${tipo}`;
            switch(tipo) {
                case 'success':
                    modalIcono.innerHTML = '<span class="material-symbols-outlined">check_circle</span>';
                    break;
                case 'error':
                    modalIcono.innerHTML = '<span class="material-symbols-outlined">error</span>';
                    break;
                case 'warning':
                    modalIcono.innerHTML = '<span class="material-symbols-outlined">warning</span>';
                    break;
            }
            
            // Configurar acciones
            if (acciones) {
                modalAcciones.innerHTML = acciones;
            } else {
                modalAcciones.innerHTML = '<button class="modal-btn primary" onclick="cerrarModal()">Aceptar</button>';
            }
            
            // Mostrar modal
            modal.style.display = 'block';
            
            // Configurar auto-close
            if (autoClose) {
                modalCountdownDiv.style.display = 'block';
                iniciarCountdownModal();
            } else {
                modalCountdownDiv.style.display = 'none';
            }
        }

        // Función para cerrar modal
        function cerrarModal() {
            const modal = document.getElementById('modal-resultado');
            modal.style.display = 'none';
            
            if (modalCountdown) {
                clearInterval(modalCountdown);
            }
            
            // Recargar la página para actualizar los datos
            location.reload();
        }

        // Función para iniciar countdown del modal
        function iniciarCountdownModal() {
            const countdownElement = document.getElementById('countdown-segundos');
            modalCountdownTime = 5;
            
            modalCountdown = setInterval(function() {
                modalCountdownTime--;
                countdownElement.textContent = modalCountdownTime;
                
                if (modalCountdownTime <= 0) {
                    clearInterval(modalCountdown);
                    cerrarModal();
                }
            }, 1000);
        }

        // Función para eliminar producto
        function eliminarProducto(id, nombre) {
            // Realizar petición AJAX para eliminar producto
            fetch('eliminar_producto_ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const acciones = `
                        <button class="modal-btn primary" onclick="volverALista()">Volver a la Lista</button>
                    `;
                    mostrarModal('success', 'Producto Eliminado', `El producto "${nombre}" ha sido eliminado exitosamente.`, acciones, false);
                } else {
                    mostrarModal('error', 'Error', data.mensaje);
                }
            })
            .catch(error => {
                mostrarModal('error', 'Error', 'Error de conexión. Intente nuevamente.');
            });
        }

        // Función para volver a la lista
        function volverALista() {
            window.location.href = 'productostabla.php';
        }

        // Cerrar modal al hacer clic fuera de él
        window.onclick = function(event) {
            const modal = document.getElementById('modal-resultado');
            if (event.target == modal) {
                cerrarModal();
            }
        }

        // Atajos de teclado
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modalResultado = document.getElementById('modal-resultado');
                if (modalResultado.style.display === 'block') {
                    cerrarModal();
                }
            }
        });
    </script>
</body>
</html>
