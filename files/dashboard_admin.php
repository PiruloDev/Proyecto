<?php
session_start();

// Headers de seguridad para prevenir cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header('Location: login.php?error=invalid');
    exit();
}
require_once 'conexion.php';
try {

    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM Productos");
    $stmt->execute();
    $total_productos = $stmt->get_result()->fetch_assoc()['total'];
    
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM Productos WHERE ACTIVO = 1");
    $stmt->execute();
    $productos_activos = $stmt->get_result()->fetch_assoc()['total'];
    
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM Empleados WHERE ACTIVO_EMPLEADO = 1");
    $stmt->execute();
    $empleados_activos = $stmt->get_result()->fetch_assoc()['total'];
    
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM Clientes WHERE ACTIVO_CLI = 1");
    $stmt->execute();
    $clientes_activos = $stmt->get_result()->fetch_assoc()['total'];
    
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM Pedidos WHERE DATE(FECHA_INGRESO) = CURDATE()");
    $stmt->execute();
    $pedidos_hoy = $stmt->get_result()->fetch_assoc()['total'];
    
} catch (Exception $e) {
    error_log("Error obteniendo estadísticas: " . $e->getMessage());
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
    <link rel="stylesheet" href="styleadminds.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <title>Dashboard Administrador - El Castillo del Pan </title>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <img src="img/68e66361-fb35-4d56-9cba-5c466a186142.jpg" alt="Logo" class="logo-img">
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
    <div class="main-content">
        <header class="top-header">
            <div class="bienvenida-admin">
                <span>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
            </div>
            <h1 class="page-title">Panel de Control</h1>
        </header>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Stats Grid -->
            <div class="stats-container">
                <div class="stats-grid">
                    <div class="stat-card stat-primary">
                        <div class="stat-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_productos ?? 0; ?></div>
                        <div class="stat-label">Total Productos</div>
                    </div>
                    <div class="stat-card stat-success">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-number"><?php echo $productos_activos ?? 0; ?></div>
                        <div class="stat-label">Productos Activos</div>
                    </div>
                    <div class="stat-card stat-info">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number"><?php echo $empleados_activos ?? 0; ?></div>
                        <div class="stat-label">Empleados Activos</div>
                    </div>
                    <div class="stat-card stat-warning">
                        <div class="stat-icon">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div class="stat-number"><?php echo $clientes_activos ?? 0; ?></div>
                        <div class="stat-label">Clientes Activos</div>
                    </div>
                    <div class="stat-card stat-danger">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-number"><?php echo $pedidos_hoy ?? 0; ?></div>
                        <div class="stat-label">Pedidos Hoy</div>
                    </div>
                </div>
            </div>

            <!-- Actions Section -->
            <div class="actions-container">
                <div class="actions-grid">
                    <div class="action-card">
                        <div class="card-header">
                            <h3><i class="fas fa-box me-2"></i>Gestión de Productos</h3>
                        </div>
                        <div class="card-body">
                            <a href="productostabla.php" class="action-btn">
                                <i class="fas fa-list me-2"></i>Ver Productos
                            </a>
                            <a href="agregar_producto.php" class="action-btn">
                                <i class="fas fa-plus me-2"></i>Agregar Producto
                            </a>
                        </div>
                    </div>

                    <div class="action-card">
                        <div class="card-header">
                            <h3><i class="fas fa-users me-2"></i>Gestión de Personal</h3>
                        </div>
                        <div class="card-body">
                            <a href="empleados_tabla.php" class="action-btn">
                                <i class="fas fa-user-tie me-2"></i>Ver Empleados
                            </a>
                            <a href="agregar_empleado.php" class="action-btn">
                                <i class="fas fa-user-plus me-2"></i>Agregar Empleado
                            </a>
                            <a href="clientes_tabla.php" class="action-btn">
                                <i class="fas fa-user-friends me-2"></i>Ver Clientes
                            </a>
                        </div>
                    </div>

                    <div class="action-card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-bar me-2"></i>Reportes y Estadísticas</h3>
                        </div>
                        <div class="card-body">
                            <a href="reportes_ventas.php" class="action-btn">
                                <i class="fas fa-chart-line me-2"></i>Reporte de Ventas
                            </a>
                            <a href="inventario_reporte.php" class="action-btn">
                                <i class="fas fa-warehouse me-2"></i>Inventario
                            </a>
                            <a href="estadisticas.php" class="action-btn">
                                <i class="fas fa-analytics me-2"></i>Estadísticas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
        
        // Debug console
        console.log('=== PANEL ADMINISTRADOR ===');
        console.log('Usuario:', '<?php echo $_SESSION['usuario_nombre']; ?>');
        console.log('Tipo:', '<?php echo $_SESSION['usuario_tipo']; ?>');
        console.log('Seguridad del historial activada');
    </script>
</body>
</html>
