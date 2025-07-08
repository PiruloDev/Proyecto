<?php
session_start();

// Headers de seguridad para prevenir cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Verificación de autenticación mejorada
if (!isset($_SESSION['usuario_logueado']) || 
    $_SESSION['usuario_logueado'] !== true || 
    !isset($_SESSION['usuario_tipo']) || 
    $_SESSION['usuario_tipo'] !== 'admin') {
    
    // Limpiar sesión si hay datos inconsistentes
    session_destroy();
    header('Location: login.php?error=session_invalid');
    exit();
}

// Verificar que las variables de sesión sean válidas
if (empty($_SESSION['usuario_nombre']) || empty($_SESSION['usuario_id'])) {
    session_destroy();
    header('Location: login.php?error=session_corrupted');
    exit();
}
require_once 'conexion.php';

// Inicializar variables con valores por defecto
$total_productos = 0;
$productos_activos = 0;
$empleados_activos = 0;
$clientes_activos = 0;
$pedidos_hoy = 0;
$error_message = '';

try {
    // Verificar conexión
    if (!$conexion) {
        throw new Exception("No se pudo conectar a la base de datos");
    }

    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM Productos");
    if (!$stmt) {
        throw new Exception("Error preparando consulta de productos: " . $conexion->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $total_productos = $result->fetch_assoc()['total'];
    }
    
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM Productos WHERE ACTIVO = 1");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $productos_activos = $result->fetch_assoc()['total'];
        }
    }
    
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM Empleados WHERE ACTIVO_EMPLEADO = 1");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $empleados_activos = $result->fetch_assoc()['total'];
        }
    }
    
    // Contar clientes activos
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM Clientes WHERE ACTIVO_CLI = 1");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $clientes_activos = $result->fetch_assoc()['total'];
        }
    }
    
    // Contar pedidos del día
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM Pedidos WHERE DATE(FECHA_INGRESO) = CURDATE()");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $pedidos_hoy = $result->fetch_assoc()['total'];
        }
    }
    
} catch (Exception $e) {
    $error_message = "Error obteniendo estadísticas: " . $e->getMessage();
    error_log($error_message);
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
    <link rel="stylesheet" href="styleadmindst.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Ocultar mensajes de error residuales del login */
        #errorMessage {
            display: none !important;
        }
        
        .error-message {
            display: none !important;
        }
        
        /* Asegurar que solo se muestren errores del sistema del dashboard */
        .alert-danger {
            display: block !important;
        }
    </style>

    <title>Dashboard Administrador - El Castillo del Pan </title>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <a href="homepage.php" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
                    <img src="../files/img/logoprincipal.jpg" alt="Logo" class="logo-img">
                    <h4 class="logo-text">El Castillo del Pan</h4>
                </a>
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
                    <a class="nav-link" href="pedidos_historial.php">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Pedidos</span>
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
                <li class="nav-item">
                    <a class="nav-link" href="perfil_admin.php">
                        <i class="fas fa-user-circle"></i>
                        <span>Mi Perfil</span>
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
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Error del Sistema:</strong> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
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
        // Función para refrescar el dashboard
        function refreshDashboard() {
            try {
                // Mostrar indicador de carga
                const loadingOverlay = document.createElement('div');
                loadingOverlay.id = 'loading-overlay';
                loadingOverlay.innerHTML = `
                    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 9999;">
                        <div style="background: white; padding: 20px; border-radius: 10px; text-align: center;">
                            <i class="fas fa-spinner fa-spin" style="font-size: 2em; color: #007bff;"></i>
                            <p style="margin: 10px 0 0 0;">Actualizando mi perfil...</p>
                        </div>
                    </div>
                `;
                document.body.appendChild(loadingOverlay);
                
                // Recargar página después de un breve delay
                setTimeout(() => {
                    window.location.reload();
                }, 800);
                
            } catch (error) {
                console.error('Error en refreshDashboard:', error);
                // Fallback: recargar directamente
                window.location.reload();
            }
        }
        
        // Función para activar el enlace correcto en el sidebar
        function activateNavLink() {
            try {
                // Remover clase active de todos los enlaces
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                });
                
                // Activar el enlace del Dashboard
                const dashboardLink = document.querySelector('a[href="#dashboard"]');
                if (dashboardLink) {
                    dashboardLink.classList.add('active');
                }
            } catch (error) {
                console.error('Error en activateNavLink:', error);
            }
        }
        
        // Función para verificar el estado del sistema
        function checkSystemStatus() {
            const errorAlert = document.querySelector('.alert-danger');
            if (errorAlert) {
                console.warn('Sistema con errores detectados');
                return false;
            }
            return true;
        }
        
        // Función para limpiar estado del navegador
        function cleanBrowserState() {
            try {
                // Limpiar localStorage de errores previos
                if (typeof(Storage) !== "undefined") {
                    localStorage.removeItem('loginError');
                    localStorage.removeItem('errorMessage');
                }
                
                // Limpiar sessionStorage
                if (typeof(Storage) !== "undefined") {
                    sessionStorage.removeItem('loginError');
                    sessionStorage.removeItem('errorMessage');
                }
                
                // Remover cualquier elemento de error en el DOM
                const errorElements = document.querySelectorAll('[id*="error"], [class*="error"]');
                errorElements.forEach(element => {
                    if (element.textContent && element.textContent.includes('Error desconocido')) {
                        element.remove();
                    }
                });
                
                console.log('Estado del navegador limpiado');
            } catch (error) {
                console.error('Error limpiando estado del navegador:', error);
            }
        }
        
        // Ejecutar al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            try {
                // Limpiar estado del navegador primero
                cleanBrowserState();
                
                // Limpiar mensajes de error residuales del login
                const errorMessage = document.getElementById('errorMessage');
                if (errorMessage) {
                    console.log('Eliminando mensaje de error residual del login');
                    errorMessage.remove();
                }
                
                // Limpiar otros elementos de error
                const errorElements = document.querySelectorAll('.error-message');
                errorElements.forEach(element => {
                    if (element.textContent.includes('Error desconocido')) {
                        console.log('Eliminando elemento de error:', element);
                        element.remove();
                    }
                });
                
                activateNavLink();
                checkSystemStatus();
                
                console.log('=== PANEL ADMINISTRADOR ===');
                console.log('Usuario:', '<?php echo $_SESSION['usuario_nombre']; ?>');
                console.log('Tipo:', '<?php echo $_SESSION['usuario_tipo']; ?>');
                console.log('Sistema:', checkSystemStatus() ? 'OK' : 'Con errores');
                console.log('Seguridad del historial activada');
                
            } catch (error) {
                console.error('Error al inicializar dashboard:', error);
            }
        });
        
        // Manejar errores globales
        window.addEventListener('error', function(event) {
            console.error('Error global:', event.error);
        });
        
        // Prevenir errores en navegación y mejorar experiencia de usuario
        window.addEventListener('beforeunload', function() {
            const loadingOverlay = document.getElementById('loading-overlay');
            if (loadingOverlay) {
                loadingOverlay.remove();
            }
        });
        
        // Sistema de navegación inteligente para administrador
        (function() {
            let isNavigatingWithinSystem = false;
            
            // Detectar clics en enlaces internos
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && link.href) {
                    const currentDomain = window.location.hostname;
                    try {
                        const linkDomain = new URL(link.href).hostname;
                        if (linkDomain === currentDomain) {
                            isNavigatingWithinSystem = true;
                            console.log('Navegación interna detectada:', link.href);
                        }
                    } catch (error) {
                        // URL relativa o malformada, asumir navegación interna
                        isNavigatingWithinSystem = true;
                    }
                }
            });
            
            // Manejar navegación del historial de manera inteligente
            let backButtonCount = 0;
            window.addEventListener('popstate', function(event) {
                backButtonCount++;
                
                // Solo mostrar advertencia después de múltiples intentos
                if (backButtonCount > 3 && !isNavigatingWithinSystem) {
                    if (confirm('¿Deseas cerrar la sesión de administrador?')) {
                        window.location.href = 'logout.php';
                    } else {
                        backButtonCount = 0; // Resetear contador
                        history.pushState(null, null, window.location.pathname);
                    }
                } else {
                    // Permitir navegación normal
                    console.log('Navegación permitida, intento:', backButtonCount);
                }
                
                // Resetear flag después de un tiempo
                setTimeout(() => {
                    isNavigatingWithinSystem = false;
                }, 1000);
            });
            
            // Establecer estado inicial del historial
            if (window.history && window.history.replaceState) {
                history.replaceState(null, null, window.location.pathname);
            }
        })();
    </script>
</body>
</html>
