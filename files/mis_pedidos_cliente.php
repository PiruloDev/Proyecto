<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Verificar que el usuario esté logueado y sea cliente
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_tipo'] !== 'cliente') {
    error_log("Mis Pedidos Cliente - Sesión inválida: " . json_encode($_SESSION));
    header('Location: login.php?error=session_invalid');
    exit();
}

require_once 'conexion.php';

// Obtener todos los pedidos del cliente
$mis_pedidos = null;
try {
    $stmt = $conexion->prepare("
        SELECT p.ID_PEDIDO, p.FECHA_INGRESO, p.FECHA_ENTREGA, p.TOTAL_PRODUCTO,
               e.NOMBRE_EMPLEADO, ep.NOMBRE_ESTADO, ep.ID_ESTADO_PEDIDO
        FROM Pedidos p
        INNER JOIN Empleados e ON p.ID_EMPLEADO = e.ID_EMPLEADO
        INNER JOIN Estado_Pedidos ep ON p.ID_ESTADO_PEDIDO = ep.ID_ESTADO_PEDIDO
        WHERE p.ID_CLIENTE = ?
        ORDER BY p.FECHA_INGRESO DESC
    ");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $mis_pedidos = $stmt->get_result();
} catch (Exception $e) {
    error_log("Error obteniendo pedidos del cliente: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos - Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            margin-top: 30px;
        }
        
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .pedidos-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .pedido-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .pedido-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(0,0,0,0.1);
        }
        
        .pedido-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .pedido-id {
            font-size: 1.2rem;
            font-weight: bold;
            color: #495057;
        }
        
        .estado-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .estado-pendiente {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .estado-enpreparacion {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .estado-listoparaentrega {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .estado-entregado {
            background: #28a745;
            color: white;
            border: 1px solid #28a745;
        }
        
        .estado-cancelado {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .pedido-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .detail-icon {
            color: #667eea;
            font-size: 1.1rem;
        }
        
        .detail-text {
            color: #495057;
            font-size: 0.9rem;
        }
        
        .detail-value {
            font-weight: 600;
        }
        
        .btn-ver-detalles {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .btn-ver-detalles:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .no-pedidos {
            text-align: center;
            padding: 50px;
            color: #6c757d;
        }
        
        .no-pedidos i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #dee2e6;
        }
        
        .btn-regresar {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-regresar:hover {
            background: #5a6268;
            color: white;
            transform: translateY(-1px);
        }
        
        .refresh-btn {
            background: #17a2b8;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .refresh-btn:hover {
            background: #138496;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="container main-container">
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2">
                        <i class="bi bi-cart-check"></i> Mis Pedidos
                    </h1>
                    <p class="mb-0">Consulta el estado de todos tus pedidos</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="refresh-btn" onclick="actualizarEstados()" title="Actualizar estados">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                    <a href="dashboard_cliente.php" class="btn-regresar">
                        <i class="bi bi-arrow-left"></i>
                        Regresar al Dashboard
                    </a>
                </div>
            </div>
        </div>

        <div class="pedidos-container">
            <?php if ($mis_pedidos && $mis_pedidos->num_rows > 0): ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>
                        <i class="bi bi-list-ul"></i> 
                        Historial de Pedidos (<?php echo $mis_pedidos->num_rows; ?>)
                    </h3>
                    <small class="text-muted">
                        <i class="bi bi-clock"></i> 
                        Actualizado automáticamente cada 30 segundos
                    </small>
                </div>
                
                <div id="pedidos-lista">
                    <?php while ($pedido = $mis_pedidos->fetch_assoc()): ?>
                        <div class="pedido-card" data-pedido-id="<?php echo $pedido['ID_PEDIDO']; ?>">
                            <div class="pedido-header">
                                <div class="pedido-id">
                                    <i class="bi bi-receipt"></i>
                                    Pedido #<?php echo $pedido['ID_PEDIDO']; ?>
                                </div>
                                <div class="estado-badge estado-<?php echo strtolower(str_replace(' ', '', $pedido['NOMBRE_ESTADO'])); ?>" 
                                     id="estado-<?php echo $pedido['ID_PEDIDO']; ?>">
                                    <?php echo htmlspecialchars($pedido['NOMBRE_ESTADO']); ?>
                                </div>
                            </div>
                            
                            <div class="pedido-details">
                                <div class="detail-item">
                                    <i class="bi bi-calendar-event detail-icon"></i>
                                    <div class="detail-text">
                                        <strong>Fecha de Pedido:</strong><br>
                                        <span class="detail-value"><?php echo date('d/m/Y H:i', strtotime($pedido['FECHA_INGRESO'])); ?></span>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <i class="bi bi-truck detail-icon"></i>
                                    <div class="detail-text">
                                        <strong>Fecha de Entrega:</strong><br>
                                        <span class="detail-value"><?php echo date('d/m/Y H:i', strtotime($pedido['FECHA_ENTREGA'])); ?></span>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <i class="bi bi-person-badge detail-icon"></i>
                                    <div class="detail-text">
                                        <strong>Atendido por:</strong><br>
                                        <span class="detail-value"><?php echo htmlspecialchars($pedido['NOMBRE_EMPLEADO']); ?></span>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <i class="bi bi-currency-dollar detail-icon"></i>
                                    <div class="detail-text">
                                        <strong>Total:</strong><br>
                                        <span class="detail-value">$<?php echo number_format($pedido['TOTAL_PRODUCTO'], 2); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button class="btn-ver-detalles" 
                                        onclick="verDetallesPedido(<?php echo $pedido['ID_PEDIDO']; ?>)">
                                    <i class="bi bi-eye"></i> Ver Detalles
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-pedidos">
                    <i class="bi bi-cart-x"></i>
                    <h4>No tienes pedidos aún</h4>
                    <p>Cuando realices tu primer pedido, aparecerá aquí.</p>
                    <a href="menu.php" class="btn btn-primary">
                        <i class="bi bi-shop"></i> Explorar Productos
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/navigation-system.js"></script>
    
    <script>
        // Función para actualizar estados automáticamente
        function actualizarEstados() {
            const refreshBtn = document.querySelector('.refresh-btn');
            refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
            refreshBtn.style.animation = 'spin 1s linear infinite';
            
            // Agregar CSS animation si no existe
            if (!document.querySelector('#spin-animation')) {
                const style = document.createElement('style');
                style.id = 'spin-animation';
                style.textContent = `
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                `;
                document.head.appendChild(style);
            }
            
            fetch('obtener_estados_pedidos_cliente.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    cliente_id: <?php echo $_SESSION['usuario_id']; ?>
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar estados en la página
                    data.pedidos.forEach(pedido => {
                        const estadoElement = document.getElementById(`estado-${pedido.id}`);
                        if (estadoElement) {
                            const estadoAnterior = estadoElement.textContent;
                            const estadoNuevo = pedido.estado;
                            
                            if (estadoAnterior !== estadoNuevo) {
                                // Cambiar clase CSS
                                estadoElement.className = `estado-badge estado-${pedido.estado.toLowerCase().replace(' ', '')}`;
                                estadoElement.textContent = estadoNuevo;
                                
                                // Animación de cambio
                                estadoElement.style.animation = 'pulse 0.5s ease-in-out';
                                
                                // Mostrar notificación
                                mostrarNotificacionCambio(pedido.id, estadoAnterior, estadoNuevo);
                            }
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
            })
            .finally(() => {
                refreshBtn.style.animation = '';
                setTimeout(() => {
                    refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
                }, 500);
            });
        }
        
        // Función para mostrar notificación de cambio de estado
        function mostrarNotificacionCambio(pedidoId, estadoAnterior, estadoNuevo) {
            Swal.fire({
                icon: 'info',
                title: 'Estado Actualizado',
                html: `
                    <div style="text-align: center;">
                        <h4>Pedido #${pedidoId}</h4>
                        <p><strong>Estado anterior:</strong> ${estadoAnterior}</p>
                        <p><strong>Estado nuevo:</strong> <span style="color: #28a745;">${estadoNuevo}</span></p>
                    </div>
                `,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                toast: true,
                position: 'top-end'
            });
        }
        
        // Función para ver detalles del pedido
        function verDetallesPedido(pedidoId) {
            fetch('obtener_detalles_pedido.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `pedido_id=${pedidoId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarModalDetalles(data.pedido, data.detalles);
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'No se pudieron cargar los detalles del pedido', 'error');
            });
        }
        
        // Función para mostrar modal con detalles
        function mostrarModalDetalles(pedido, detalles) {
            let htmlDetalles = '';
            let total = 0;
            
            detalles.forEach(detalle => {
                htmlDetalles += `
                    <div class="row mb-2">
                        <div class="col-6">${detalle.producto}</div>
                        <div class="col-2 text-center">${detalle.cantidad}</div>
                        <div class="col-2 text-end">$${parseFloat(detalle.precio_unitario).toFixed(2)}</div>
                        <div class="col-2 text-end"><strong>$${parseFloat(detalle.subtotal).toFixed(2)}</strong></div>
                    </div>
                `;
                total += parseFloat(detalle.subtotal);
            });
            
            Swal.fire({
                title: `Detalles del Pedido #${pedido.id}`,
                html: `
                    <div class="text-start">
                        <div class="mb-3">
                            <strong>Fecha:</strong> ${pedido.fecha_ingreso}<br>
                            <strong>Estado:</strong> ${pedido.estado}<br>
                            <strong>Empleado:</strong> ${pedido.empleado}
                        </div>
                        <hr>
                        <div class="row mb-2 fw-bold">
                            <div class="col-6">Producto</div>
                            <div class="col-2 text-center">Cant.</div>
                            <div class="col-2 text-end">Precio</div>
                            <div class="col-2 text-end">Total</div>
                        </div>
                        ${htmlDetalles}
                        <hr>
                        <div class="row">
                            <div class="col-8 text-end"><strong>Total del Pedido:</strong></div>
                            <div class="col-4 text-end"><strong>$${total.toFixed(2)}</strong></div>
                        </div>
                    </div>
                `,
                width: '800px',
                showCancelButton: false,
                confirmButtonText: 'Cerrar'
            });
        }
        
        // Actualizar automáticamente cada 30 segundos
        setInterval(actualizarEstados, 30000);
        
        // Agregar CSS para animación de pulse
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
