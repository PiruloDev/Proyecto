<?php
session_start();

// Verificar que el usuario esté logueado y sea administrador
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header('Location: login.php?error=invalid');
    exit();
}

require_once 'conexion.php';

// Obtener estadísticas
try {
    // Contar productos
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM Productos");
    $stmt->execute();
    $total_productos = $stmt->get_result()->fetch_assoc()['total'];
    
    // Contar productos activos
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM Productos WHERE ACTIVO = 1");
    $stmt->execute();
    $productos_activos = $stmt->get_result()->fetch_assoc()['total'];
    
    // Contar empleados activos
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM Empleados WHERE ACTIVO_EMPLEADO = 1");
    $stmt->execute();
    $empleados_activos = $stmt->get_result()->fetch_assoc()['total'];
    
    // Contar clientes activos
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM Clientes WHERE ACTIVO_CLI = 1");
    $stmt->execute();
    $clientes_activos = $stmt->get_result()->fetch_assoc()['total'];
    
    // Contar pedidos del día
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM Pedidos WHERE DATE(FECHA_INGRESO) = CURDATE()");
    $stmt->execute();
    $pedidos_hoy = $stmt->get_result()->fetch_assoc()['total'];
    
    // Obtener productos con stock bajo
    $stmt = $conexion->prepare("
        SELECT p.ID_PRODUCTO, p.NOMBRE_PRODUCTO, p.PRODUCTO_STOCK_MIN, 
        COALESCE(SUM(dp.CANTIDAD_PRODUCTO), 0) as stock_actual
        FROM Productos p
        LEFT JOIN Detalle_Pedidos dp ON p.ID_PRODUCTO = dp.ID_PRODUCTO
        GROUP BY p.ID_PRODUCTO, p.NOMBRE_PRODUCTO, p.PRODUCTO_STOCK_MIN
        HAVING stock_actual <= p.PRODUCTO_STOCK_MIN
        ORDER BY stock_actual ASC
        LIMIT 5
    ");
    $stmt->execute();
    $productos_stock_bajo = $stmt->get_result();
    
} catch (Exception $e) {
    error_log("Error obteniendo estadísticas: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="syle.css">
    <title>Dashboard Administrador - El Castillo del Pan</title>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <img src="img/457119359_937614238287133_2801289601643472204_n.jpg" alt="Logo" class="logo-img">
                <h4 class="logo-text">El Castillo del Pan</h4>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="#dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="productostabla.php">
                        <i class="fas fa-box"></i>
                        <span>Productos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="empleados_tabla.php">
                        <i class="fas fa-users"></i>
                        <span>Empleados</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="clientes_tabla.php">
                        <i class="fas fa-user-friends"></i>
                        <span>Clientes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reportes_ventas.php">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reportes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="configuracion.php">
                        <i class="fas fa-cog"></i>
                        <span>Configuración</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="user-details">
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
                    <span class="user-role">Administrador</span>
                </div>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Header -->
        <header class="top-header">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="page-title">Panel de Control del Administrador</h1>
            <div class="header-actions">
                <button class="btn btn-outline-secondary">
                    <i class="fas fa-bell"></i>
                </button>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <div class="welcome-section">
                <div class="row">
                    <div class="col-12">
                        <div class="welcome-card">
                            <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h2>
                        </div>
                    </div>
                </div>
            </div>

        <div class="stats-grid">
            <div class="row g-4">
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_productos ?? 0; ?></div>
                        <div class="stat-label">Total Productos</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-number"><?php echo $productos_activos ?? 0; ?></div>
                        <div class="stat-label">Productos Activos</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number"><?php echo $empleados_activos ?? 0; ?></div>
                        <div class="stat-label">Empleados Activos</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div class="stat-number"><?php echo $clientes_activos ?? 0; ?></div>
                        <div class="stat-label">Clientes Activos</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-number"><?php echo $pedidos_hoy ?? 0; ?></div>
                        <div class="stat-label">Pedidos Hoy</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="actions-section">
            <div class="row g-4">
                <div class="col-lg-6 col-md-12">
                    <div class="action-card">
                        <div class="card-header">
                            <h3><i class="fas fa-box me-2"></i>Gestión de Productos</h3>
                        </div>
                        <div class="card-body">
                            <a href="productostabla.php" class="action-btn primary">
                                <i class="fas fa-list me-2"></i>Ver Todos los Productos
                            </a>
                            <a href="agregar_producto.php" class="action-btn success">
                                <i class="fas fa-plus me-2"></i>Agregar Producto
                            </a>
                            <div id="countdown1" class="countdown">
                                Preparando herramientas... <span id="timer1">5</span> segundos
                            </div>
                            <a href="#" class="action-btn danger" id="manageBtn" onclick="startCountdown('manage')">
                                <i class="fas fa-cogs me-2"></i>Gestión Avanzada
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12">
                    <div class="action-card">
                        <div class="card-header">
                            <h3><i class="fas fa-users me-2"></i>Gestión de Personal</h3>
                        </div>
                        <div class="card-body">
                            <a href="empleados_tabla.php" class="action-btn primary">
                                <i class="fas fa-user-tie me-2"></i>Ver Empleados
                            </a>
                            <a href="agregar_empleado.php" class="action-btn success">
                                <i class="fas fa-user-plus me-2"></i>Agregar Empleado
                            </a>
                            <a href="clientes_tabla.php" class="action-btn info">
                                <i class="fas fa-user-friends me-2"></i>Ver Clientes
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12">
                    <div class="action-card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-bar me-2"></i>Reportes y Estadísticas</h3>
                        </div>
                        <div class="card-body">
                            <a href="reportes_ventas.php" class="action-btn primary">
                                <i class="fas fa-chart-line me-2"></i>Reporte de Ventas
                            </a>
                            <a href="inventario_reporte.php" class="action-btn info">
                                <i class="fas fa-warehouse me-2"></i>Reporte de Inventario
                            </a>
                            <a href="estadisticas.php" class="action-btn warning">
                                <i class="fas fa-analytics me-2"></i>Estadísticas Generales
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12">
                    <div class="action-card">
                        <div class="card-header">
                            <h3><i class="fas fa-cog me-2"></i>Configuración del Sistema</h3>
                        </div>
                        <div class="card-body">
                            <a href="configuracion.php" class="action-btn primary">
                                <i class="fas fa-sliders-h me-2"></i>Configuración General
                            </a>
                            <a href="backup.php" class="action-btn info">
                                <i class="fas fa-database me-2"></i>Respaldo de Datos
                            </a>
                            <div id="countdown2" class="countdown">
                                Verificando permisos... <span id="timer2">5</span> segundos
                            </div>
                            <a href="#" class="action-btn danger" id="systemBtn" onclick="startCountdown('system')">
                                <i class="fas fa-tools me-2"></i>Mantenimiento Sistema
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($productos_stock_bajo) && $productos_stock_bajo->num_rows > 0): ?>
        <div class="warning-section">
            <h3>⚠️ Productos con Stock Bajo</h3>
            <p>Los siguientes productos requieren atención inmediata:</p>
            <?php while ($producto = $productos_stock_bajo->fetch_assoc()): ?>
                <div class="stock-item">
                    <strong><?php echo htmlspecialchars($producto['NOMBRE_PRODUCTO']); ?></strong><br>
                    Stock actual: <?php echo $producto['stock_actual']; ?> | 
                    Stock mínimo: <?php echo $producto['PRODUCTO_STOCK_MIN']; ?>
                </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let countdownActive = false;

        // Toggle sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        function startCountdown(type) {
            if (countdownActive) return;
            
            countdownActive = true;
            let countdownDiv, timerSpan, actionBtn;
            let redirectUrl = '';
            
            if (type === 'manage') {
                countdownDiv = document.getElementById('countdown1');
                timerSpan = document.getElementById('timer1');
                actionBtn = document.getElementById('manageBtn');
                redirectUrl = 'gestionar_producto.php';
            } else if (type === 'system') {
                countdownDiv = document.getElementById('countdown2');
                timerSpan = document.getElementById('timer2');
                actionBtn = document.getElementById('systemBtn');
                redirectUrl = 'mantenimiento_sistema.php';
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

        // Mostrar información adicional en consola para desarrollo
        console.log('=== PANEL ADMINISTRADOR ===');
        console.log('Usuario logueado:', '<?php echo $_SESSION['usuario_nombre']; ?>');
        console.log('Tipo de usuario:', '<?php echo $_SESSION['usuario_tipo']; ?>');
        console.log('ID de sesión:', '<?php echo session_id(); ?>');
    </script>
</body>
</html>
