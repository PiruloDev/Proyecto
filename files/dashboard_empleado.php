<?php
session_start();

// Headers de seguridad para prevenir cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Verificar que el usuario esté logueado y sea empleado
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_tipo'] !== 'empleado') {
    header('Location: login.php?error=invalid');
    exit();
}
require_once 'conexion.php';

try {
    // Contar pedidos asignados al empleado
    $stmt = $conexion->prepare("
        SELECT COUNT(*) as total 
        FROM Pedidos 
        WHERE ID_EMPLEADO = ? AND DATE(FECHA_INGRESO) = CURDATE()
    ");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $mis_pedidos_hoy = $stmt->get_result()->fetch_assoc()['total'];
    
    // Contar pedidos pendientes asignados al empleado
    $stmt = $conexion->prepare("
        SELECT COUNT(*) as total 
        FROM Pedidos 
        WHERE ID_EMPLEADO = ? AND ID_ESTADO_PEDIDO IN (1, 2)
    ");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $mis_pedidos_pendientes = $stmt->get_result()->fetch_assoc()['total'];
    
    // Contar productos activos
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM Productos WHERE ACTIVO = 1");
    $stmt->execute();
    $productos_disponibles = $stmt->get_result()->fetch_assoc()['total'];
    
    // Obtener mis pedidos recientes
    $stmt = $conexion->prepare("
        SELECT p.ID_PEDIDO, c.NOMBRE_CLI, ep.NOMBRE_ESTADO, 
               p.FECHA_INGRESO, p.FECHA_ENTREGA, p.TOTAL_PRODUCTO
        FROM Pedidos p
        INNER JOIN Clientes c ON p.ID_CLIENTE = c.ID_CLIENTE
        INNER JOIN Estado_Pedidos ep ON p.ID_ESTADO_PEDIDO = ep.ID_ESTADO_PEDIDO
        WHERE p.ID_EMPLEADO = ?
        ORDER BY p.FECHA_INGRESO DESC
        LIMIT 5
    ");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $mis_pedidos_recientes = $stmt->get_result();
    
} catch (Exception $e) {
    error_log("Error obteniendo estadísticas del empleado: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Dashboard Empleado - Panadería</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logout-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .welcome-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            border-left: 4px solid #28a745;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #666;
            font-size: 1rem;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .action-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .action-card h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .action-btn {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px 5px 5px 0;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .warning-btn {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }

        .pedidos-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #28a745;
        }

        .pedidos-section h3 {
            color: #28a745;
            margin-bottom: 15px;
        }

        .pedido-item {
            padding: 15px;
            margin: 10px 0;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 3px solid #28a745;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .pedido-info {
            flex: 1;
        }

        .pedido-status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-pendiente {
            background: #fff3cd;
            color: #856404;
        }

        .status-preparacion {
            background: #cce5ff;
            color: #004085;
        }

        .status-listo {
            background: #d4edda;
            color: #155724;
        }

        .status-entregado {
            background: #d1ecf1;
            color: #0c5460;
        }

        .countdown {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            text-align: center;
            font-weight: bold;
            display: none;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }

            .actions-grid {
                grid-template-columns: 1fr;
            }

            .pedido-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">🥖 Panadería - Panel Empleado</div>
            <div class="user-info">
                <span>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
                <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="welcome-section">
            <h1>Panel de Empleado</h1>
            <p>Gestiona pedidos y consulta información de productos</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $mis_pedidos_hoy ?? 0; ?></div>
                <div class="stat-label">Mis Pedidos Hoy</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $mis_pedidos_pendientes ?? 0; ?></div>
                <div class="stat-label">Pedidos Pendientes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $productos_disponibles ?? 0; ?></div>
                <div class="stat-label">Productos Disponibles</div>
            </div>
        </div>

        <div class="actions-grid">
            <div class="action-card">
                <h3>📋 Gestión de Pedidos</h3>
                <a href="crear_pedido.php" class="action-btn">Crear Nuevo Pedido</a>
                <a href="mis_pedidos.php" class="action-btn">Ver Mis Pedidos</a>
                <div id="countdown1" class="countdown">
                    Preparando herramientas... <span id="timer1">5</span> segundos
                </div>
                <a href="#" class="action-btn warning-btn" id="updateBtn" onclick="startCountdown('update')">Actualizar Estados</a>
            </div>

            <div class="action-card">
                <h3>📦 Consulta de Productos</h3>
                <a href="productostabla.php" class="action-btn">Ver Productos</a>
                <a href="buscar_productos.php" class="action-btn">Buscar Productos</a>
                <a href="stock_consulta.php" class="action-btn">Consultar Stock</a>
            </div>

            <div class="action-card">
                <h3>👥 Información de Clientes</h3>
                <a href="clientes_consulta.php" class="action-btn">Buscar Clientes</a>
                <a href="historial_cliente.php" class="action-btn">Historial de Pedidos</a>
            </div>

            <div class="action-card">
                <h3>📊 Mis Reportes</h3>
                <a href="mis_ventas.php" class="action-btn">Mis Ventas del Día</a>
                <a href="reporte_personal.php" class="action-btn">Reporte Personal</a>
            </div>
        </div>

        <?php if (isset($mis_pedidos_recientes) && $mis_pedidos_recientes->num_rows > 0): ?>
        <div class="pedidos-section">
            <h3>📋 Mis Pedidos Recientes</h3>
            <?php while ($pedido = $mis_pedidos_recientes->fetch_assoc()): ?>
                <div class="pedido-item">
                    <div class="pedido-info">
                        <strong>Pedido #<?php echo $pedido['ID_PEDIDO']; ?></strong><br>
                        Cliente: <?php echo htmlspecialchars($pedido['NOMBRE_CLI']); ?><br>
                        Fecha: <?php echo date('d/m/Y H:i', strtotime($pedido['FECHA_INGRESO'])); ?><br>
                        Entrega: <?php echo date('d/m/Y H:i', strtotime($pedido['FECHA_ENTREGA'])); ?><br>
                        Total: $<?php echo number_format($pedido['TOTAL_PRODUCTO'], 2); ?>
                    </div>
                    <div class="pedido-status status-<?php echo strtolower(str_replace(' ', '', $pedido['NOMBRE_ESTADO'])); ?>">
                        <?php echo htmlspecialchars($pedido['NOMBRE_ESTADO']); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </div>

    <script>
        let countdownActive = false;

        function startCountdown(type) {
            if (countdownActive) return;
            
            countdownActive = true;
            let countdownDiv, timerSpan, actionBtn;
            let redirectUrl = '';
            
            if (type === 'update') {
                countdownDiv = document.getElementById('countdown1');
                timerSpan = document.getElementById('timer1');
                actionBtn = document.getElementById('updateBtn');
                redirectUrl = 'actualizar_pedidos.php';
            }
            
            let timeLeft = 5;
            countdownDiv.style.display = 'block';
            actionBtn.style.opacity = '0.5';
            actionBtn.style.pointerEvents = 'none';
            
            const countdown = setInterval(() => {
                timeLeft--;
                timerSpan.textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    countdownDiv.style.display = 'none';
                    actionBtn.style.opacity = '1';
                    actionBtn.style.pointerEvents = 'auto';
                    actionBtn.href = redirectUrl;
                    countdownActive = false;
                }
            }, 1000);
        }

        // Seguridad: Prevenir navegación hacia atrás
        (function() {
            // Reemplazar la entrada actual del historial
            history.replaceState(null, null, window.location.pathname);
            
            // Agregar una nueva entrada al historial
            history.pushState(null, null, window.location.pathname);
            
            // Escuchar el evento popstate (botón atrás)
            window.addEventListener('popstate', function(event) {
                // Mostrar confirmación
                if (confirm('¿Estás seguro de que quieres salir del dashboard? Serás redirigido al login por seguridad.')) {
                    // Redirigir al login
                    window.location.href = 'logout.php';
                } else {
                    // Volver a agregar la entrada al historial
                    history.pushState(null, null, window.location.pathname);
                }
            });
            
            // Prevenir el cache de la página
            window.addEventListener('beforeunload', function() {
                // Limpiar datos sensibles del navegador
                if (typeof(Storage) !== "undefined") {
                    sessionStorage.clear();
                }
            });
        })();
        
        // Prevenir cache HTTP
        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        // Información de usuario en consola para desarrollo
        console.log('=== PANEL EMPLEADO ===');
        console.log('Usuario logueado:', '<?php echo $_SESSION['usuario_nombre']; ?>');
        console.log('Tipo de usuario:', '<?php echo $_SESSION['usuario_tipo']; ?>');
        console.log('ID empleado:', '<?php echo $_SESSION['usuario_id']; ?>');
        console.log('Seguridad del historial activada');
    </script>
</body>
</html>
