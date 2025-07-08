<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");



// Verificar que el usuario esté logueado y sea cliente
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_tipo'] !== 'cliente') {
    header('Location: login.php?error=invalid');
    exit();
}

require_once 'conexion.php';

// Obtener estadísticas para el cliente
try {
    // Contar mis pedidos totales
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM Pedidos WHERE ID_CLIENTE = ?");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $mis_pedidos_total = $stmt->get_result()->fetch_assoc()['total'];
    
    // Contar mis pedidos pendientes
    $stmt = $conexion->prepare("
        SELECT COUNT(*) as total 
        FROM Pedidos 
        WHERE ID_CLIENTE = ? AND ID_ESTADO_PEDIDO IN (1, 2, 3)
    ");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $mis_pedidos_pendientes = $stmt->get_result()->fetch_assoc()['total'];
    
    // Obtener mis pedidos recientes
    $stmt = $conexion->prepare("
        SELECT p.ID_PEDIDO, e.NOMBRE_EMPLEADO, ep.NOMBRE_ESTADO, 
        p.FECHA_INGRESO, p.FECHA_ENTREGA, p.TOTAL_PRODUCTO
        FROM Pedidos p
        INNER JOIN Empleados e ON p.ID_EMPLEADO = e.ID_EMPLEADO
        INNER JOIN Estado_Pedidos ep ON p.ID_ESTADO_PEDIDO = ep.ID_ESTADO_PEDIDO
        WHERE p.ID_CLIENTE = ?
        ORDER BY p.FECHA_INGRESO DESC
        LIMIT 5
    ");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $mis_pedidos_recientes = $stmt->get_result();
    
} catch (Exception $e) {
    error_log("Error obteniendo estadísticas del cliente: " . $e->getMessage());
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
    <title>Dashboard Cliente - Panadería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styleclienteds.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="sidebar-content">
                    <div class="sidebar-brand">
                        <img src="img/logoprincipal.jpg" alt="Logo Panadería" class="sidebar-logo">
                        <h5>Portal Cliente</h5>
                    </div>
                    <!-- Botón Explorar Productos debajo del logo -->
                    <div class="px-3 mb-3">
                        <a href="Homepage.php" class="btn btn-explore w-100">
                            <i class="bi bi-compass"></i>
                            Explorar Productos
                        </a>
                    </div>
                    <div class="sidebar-divider"></div>
                    
                    <ul class="nav flex-column">
                        <div class="nav-item">
                            <a class="nav-link active" href="#dashboard" data-section="dashboard">
                                <i class="bi bi-house"></i>
                                Dashboard
                            </a>
                        </div>
                        <div class="nav-item">
                            <a class="nav-link" href="mis_pedidos_cliente.php">
                                <i class="bi bi-cart-check"></i>
                                Mis Pedidos
                            </a>
                        </div>
                        <div class="nav-item">
                            <a class="nav-link" href="perfil_cliente.php">
                                <i class="bi bi-person-circle"></i>
                                Mi Perfil
                            </a>
                        </div>
                    </ul>
                    
                    
                    <div class="sidebar-divider"></div>
                    
                    <div class="sidebar-user">
                        <div class="user-info">
                            <i class="bi bi-person-circle"></i>
                            <span><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
                        </div>
                        <a href="cambiar_password.php" class="logout-btn mb-2 change-pass-btn">
                            <i class="bi bi-key"></i>
                            Cambiar Contraseña
                        </a>
                        <a href="logout.php" class="logout-btn">
                            <i class="bi bi-box-arrow-right"></i>
                            Cerrar Sesión
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
                <!-- Mobile menu button -->
                <button class="btn btn-outline-primary d-md-none mb-3" type="button" id="sidebarToggle">
                    <i class="bi bi-list"></i> Menú
                </button>

                <!-- Dashboard Section -->
                <div class="section-content" id="dashboard-section">
                    <div class="welcome-section">
                        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>!</h2>
                        <p>Gestiona tus pedidos y explora nuestros deliciosos productos</p>
                    </div>

                    <!-- Stats Cards -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="card-icon">
                                <i class="bi bi-cart-check"></i>
                            </div>
                            <div class="card-content">
                                <div class="stat-number"><?php echo $mis_pedidos_total ?? 0; ?></div>
                                <div class="stat-label">Total Pedidos</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="card-icon">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div class="card-content">
                                <div class="stat-number"><?php echo $mis_pedidos_pendientes ?? 0; ?></div>
                                <div class="stat-label">Pendientes</div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <?php if (isset($mis_pedidos_recientes) && $mis_pedidos_recientes->num_rows > 0): ?>
                    <div class="orders-section">
                        <div class="section-header">
                            <h4><i class="bi bi-receipt"></i> Pedidos Recientes</h4>
                            <a href="mis_pedidos_cliente.php" class="btn btn-primary btn-sm">
                                <i class="bi bi-list-ul"></i> Ver Todos
                            </a>
                        </div>
                        <div class="orders-list">
                            <?php while ($pedido = $mis_pedidos_recientes->fetch_assoc()): ?>
                            <div class="order-item">
                                <div class="order-info">
                                    <h6>Pedido #<?php echo $pedido['ID_PEDIDO']; ?></h6>
                                    <p class="mb-1">Por: <?php echo htmlspecialchars($pedido['NOMBRE_EMPLEADO']); ?></p>
                                    <p class="mb-1">Fecha: <?php echo date('d/m/Y H:i', strtotime($pedido['FECHA_INGRESO'])); ?></p>
                                    <p class="mb-0">Total: $<?php echo number_format($pedido['TOTAL_PRODUCTO'], 2); ?></p>
                                </div>
                                <div class="order-status status-<?php echo strtolower(str_replace(' ', '', $pedido['NOMBRE_ESTADO'])); ?>">
                                    <?php echo htmlspecialchars($pedido['NOMBRE_ESTADO']); ?>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="orders-section">
                        <div class="section-header">
                            <h4><i class="bi bi-receipt"></i> Mis Pedidos</h4>
                        </div>
                        <div class="no-orders">
                            <div class="text-center py-4">
                                <i class="bi bi-cart-x" style="font-size: 3rem; color: #dee2e6;"></i>
                                <p class="text-muted">No tienes pedidos aún</p>
                                <a href="menu.php" class="btn btn-primary">
                                    <i class="bi bi-shop"></i> Explorar Productos
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <!-- Mi Cuenta Section -->
                <div class="section-content" id="mi-cuenta-section" style="display: none;">
                    <h3>Mi Cuenta</h3>
                    <div class="action-cards">
                        <div class="action-card">
                            <div class="card-header">
                                <i class="bi bi-person-gear"></i>
                                <h5>Información Personal</h5>
                            </div>
                            <div class="card-body">
                                <a href="perfil_cliente.php" class="action-btn">
                                    <i class="bi bi-person-lines-fill"></i>
                                    Editar Perfil
                                </a>
                                <a href="direcciones.php" class="action-btn">
                                    <i class="bi bi-geo"></i>
                                    Mis Direcciones
                                </a>
                                <a href="cambiar_password.php" class="action-btn">
                                    <i class="bi bi-key"></i>
                                    Cambiar Contraseña
                                </a>
                                <a href="configuracion.php" class="action-btn">
                                    <i class="bi bi-gear"></i>
                                    Configuración
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/navigation-system.js"></script>

    <script>
        // Navegación del sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link');
            const sections = document.querySelectorAll('.section-content');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            
            // Navegación entre secciones
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Solo interceptar enlaces con data-section (navegación interna del dashboard)
                    if (!this.getAttribute('data-section')) {
                        return; // Permitir navegación normal a otras páginas
                    }
                    
                    e.preventDefault();
                    
                    // Remover clase activa de todos los enlaces
                    navLinks.forEach(l => l.classList.remove('active'));
                    // Agregar clase activa al enlace clickeado
                    this.classList.add('active');
                    
                    // Ocultar todas las secciones
                    sections.forEach(section => {
                        section.style.display = 'none';
                    });
                    
                    // Mostrar la sección correspondiente
                    const sectionId = this.getAttribute('data-section');
                    const targetSection = document.getElementById(sectionId + '-section');
                    if (targetSection) {
                        targetSection.style.display = 'block';
                    }
                    
                    // Cerrar sidebar en móvil después de hacer clic
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('show');
                    }
                });
            });
            
            // Toggle sidebar en móvil
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
            
            // Cerrar sidebar al hacer clic fuera (móvil)
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 768 && 
                    !sidebar.contains(e.target) && 
                    sidebarToggle && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            });
            
            // Configurar navegación inteligente
            if (window.NavSystem) {
                window.NavSystem.setDebugMode(false);
            }
            
            // Log de información para desarrollo
            console.log('=== PORTAL CLIENTE ===');
            console.log('Usuario logueado:', '<?php echo $_SESSION['usuario_nombre']; ?>');
            console.log('Email:', '<?php echo $_SESSION['usuario_email'] ?? "No disponible"; ?>');
            console.log('ID cliente:', '<?php echo $_SESSION['usuario_id']; ?>');
            console.log('Tipo de usuario:', '<?php echo $_SESSION['usuario_tipo']; ?>');
            console.log('Dashboard cliente cargado correctamente');
        });
    </script>
</body>
</html>