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
    $_SESSION['usuario_tipo'] !== 'empleado') {
    
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
$mis_pedidos_hoy = 0;
$mis_pedidos_pendientes = 0;
$productos_disponibles = 0;
$total_pedidos = 0;
$pedidos_completados_hoy = 0;
$error_message = '';

try {
    // Verificar conexión
    if (!$conexion) {
        throw new Exception("No se pudo conectar a la base de datos");
    }

    // Contar mis pedidos de hoy
    $stmt = $conexion->prepare("
        SELECT COUNT(*) as total 
        FROM Pedidos 
        WHERE ID_EMPLEADO = ? AND DATE(FECHA_INGRESO) = CURDATE()
    ");
    if ($stmt) {
        $stmt->bind_param("i", $_SESSION['usuario_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $mis_pedidos_hoy = $result->fetch_assoc()['total'];
        }
    }
    
    // Contar mis pedidos pendientes
    $stmt = $conexion->prepare("
        SELECT COUNT(*) as total 
        FROM Pedidos 
        WHERE ID_EMPLEADO = ? AND ID_ESTADO_PEDIDO IN (1, 2)
    ");
    if ($stmt) {
        $stmt->bind_param("i", $_SESSION['usuario_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $mis_pedidos_pendientes = $result->fetch_assoc()['total'];
        }
    }
    
    // Contar productos disponibles - usar tabla correcta
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM Productos WHERE ACTIVO = 1");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $productos_disponibles = $result->fetch_assoc()['total'];
        }
    }
    
    // Contar total de pedidos asignados
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM Pedidos WHERE ID_EMPLEADO = ?");
    if ($stmt) {
        $stmt->bind_param("i", $_SESSION['usuario_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $total_pedidos = $result->fetch_assoc()['total'];
        }
    }
    
    // Contar pedidos completados hoy
    $stmt = $conexion->prepare("
        SELECT COUNT(*) as total 
        FROM Pedidos 
        WHERE ID_EMPLEADO = ? AND ID_ESTADO_PEDIDO = 4 AND DATE(FECHA_INGRESO) = CURDATE()
    ");
    if ($stmt) {
        $stmt->bind_param("i", $_SESSION['usuario_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $pedidos_completados_hoy = $result->fetch_assoc()['total'];
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

        /* Tabla de pedidos y productos */
        .badge-pedido {
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-pendiente {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-preparacion {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-listo {
            background-color: #d4edda;
            color: #155724;
        }

        .status-entregado {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-cancelado {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-activo {
            background-color: #d1f2eb;
            color: #0f5132;
        }

        .status-inactivo {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Botones de acción en tablas */
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }

        .btn-sm i {
            font-size: 0.75rem;
        }

        /* Espaciado para botones de estado */
        .table td {
            vertical-align: middle;
        }

        .table .btn-group {
            white-space: nowrap;
        }

        /* Estilos para el modal de detalle del pedido */
        .modal-xl {
            max-width: 1200px;
        }

        .card-header h6 {
            margin: 0;
            font-weight: 600;
        }

        .table img {
            border: 1px solid #dee2e6;
        }

        .badge {
            font-size: 0.85rem;
        }

        /* Alertas flotantes */
        .alert-floating {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        /* Cards principales simplificadas */
        .main-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .main-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .main-card .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, rgba(140, 90, 55, 0.9) 100%);
            color: var(--white);
            padding: 20px;
            border-bottom: none;
        }

        .main-card .card-header h3 {
            font-family: 'Playfair Display', serif;
            margin: 0;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--white);
        }

        .main-card .card-body {
            padding: 30px;
            text-align: center;
        }

        /* Botones principales */
        .btn-main {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid rgba(140, 90, 55, 0.6);
            background: rgba(140, 90, 55, 0.1);
            color: var(--text-dark);
            font-family: 'Exo 2', sans-serif;
            font-size: 1.1rem;
            text-align: center;
            cursor: pointer;
            min-width: 200px;
            margin: 10px;
        }

        .btn-main:hover {
            background: rgba(140, 90, 55, 0.2);
            border-color: var(--primary-color);
            color: var(--text-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(140, 90, 55, 0.3);
        }

        .btn-main i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        /* Layout principal */
        .main-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-actions {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .btn-main {
                min-width: auto;
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>

    <title>Dashboard Empleado - El Castillo del Pan</title>
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
                    <a class="nav-link" href="#" onclick="abrirModalPedidos()">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Pedidos Pendientes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="abrirModalInventario()">
                        <i class="fas fa-box"></i>
                        <span>Ver Inventario</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="perfil_empleado.php">
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
                    <span class="user-role">Empleado</span>
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
            <h1 class="page-title">Panel de Empleado</h1>
        </header>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Error del Sistema:</strong> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <!-- Acciones Principales -->
            <div class="main-actions">
                <div class="main-card">
                    <div class="card-header">
                        <h3><i class="fas fa-clipboard-list me-2"></i>Gestión de Pedidos</h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-4">Consulta y gestiona los pedidos del sistema. Cambia el estado de los pedidos de Pendiente → En Preparación → Listo para Entrega → Entregado</p>
                        <button type="button" class="btn-main" onclick="abrirModalPedidos()">
                            <i class="fas fa-tasks"></i>Gestionar Pedidos
                        </button>
                    </div>
                </div>

                <div class="main-card">
                    <div class="card-header">
                        <h3><i class="fas fa-box me-2"></i>Consulta de Inventario</h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-4">Revisa el estado actual del inventario de productos</p>
                        <button type="button" class="btn-main" onclick="abrirModalInventario()">
                            <i class="fas fa-warehouse"></i>Ver Inventario
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Ver Pedidos Pendientes -->
    <div class="modal fade" id="pedidosModal" tabindex="-1" aria-labelledby="pedidosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="pedidosModalLabel">
                        <i class="fas fa-clipboard-list me-2"></i>Gestión de Pedidos Pendientes
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <select class="form-select" id="filtroEstadoPedidos">
                                <option value="">Todos los estados</option>
                                <option value="1" selected>Pendiente</option>
                                <option value="2">En Preparación</option>
                                <option value="3">Listo para Entrega</option>
                                <option value="4">Entregado</option>
                                <option value="5">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input type="date" class="form-control" id="filtroFechaPedidos" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>

                    <!-- Loading -->
                    <div id="pedidosLoader" class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando pedidos...</p>
                    </div>

                    <!-- Tabla de pedidos -->
                    <div id="pedidosContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Empleado</th>
                                        <th>Fecha Ingreso</th>
                                        <th>Fecha Entrega</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="pedidosTableBody">
                                    <!-- Los pedidos se cargarán aquí -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Error -->
                    <div id="pedidosError" style="display: none;" class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="pedidosErrorMessage"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cerrar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="cargarPedidos()">
                        <i class="fas fa-sync-alt me-2"></i>Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Ver Inventario -->
    <div class="modal fade" id="inventarioModal" tabindex="-1" aria-labelledby="inventarioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="inventarioModalLabel">
                        <i class="fas fa-box me-2"></i>Inventario de Productos
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="buscarProducto" placeholder="Buscar producto...">
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="filtroEstadoProducto">
                                <option value="">Todos los productos</option>
                                <option value="1" selected>Solo Activos</option>
                                <option value="0">Solo Inactivos</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="filtroCategoria">
                                <option value="">Todas las categorías</option>
                                <!-- Las categorías se cargarán dinámicamente -->
                            </select>
                        </div>
                    </div>

                    <!-- Loading -->
                    <div id="inventarioLoader" class="text-center py-3">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando inventario...</p>
                    </div>

                    <!-- Tabla de productos -->
                    <div id="inventarioContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Categoría</th>
                                        <th>Precio</th>
                                        <th>Stock Mínimo</th>
                                        <th>Vencimiento</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="inventarioTableBody">
                                    <!-- Los productos se cargarán aquí -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Error -->
                    <div id="inventarioError" style="display: none;" class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="inventarioErrorMessage"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cerrar
                    </button>
                    <button type="button" class="btn btn-info" onclick="cargarInventario()">
                        <i class="fas fa-sync-alt me-2"></i>Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Ver Detalle del Pedido -->
    <div class="modal fade" id="detallePedidoModal" tabindex="-1" aria-labelledby="detallePedidoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="detallePedidoModalLabel">
                        <i class="fas fa-receipt me-2"></i>Detalle del Pedido
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Loading -->
                    <div id="detalleLoader" class="text-center py-3">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando detalle del pedido...</p>
                    </div>

                    <!-- Información del pedido -->
                    <div id="detalleContainer" style="display: none;">
                        <!-- Información básica -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Pedido</h6>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>ID Pedido:</strong> <span id="detallePedidoId"></span></p>
                                        <p><strong>Estado:</strong> <span id="detallePedidoEstado"></span></p>
                                        <p><strong>Fecha Ingreso:</strong> <span id="detalleFechaIngreso"></span></p>
                                        <p><strong>Fecha Entrega:</strong> <span id="detalleFechaEntrega"></span></p>
                                        <p><strong>Empleado Asignado:</strong> <span id="detalleEmpleado"></span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="fas fa-user me-2"></i>Información del Cliente</h6>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Cliente:</strong> <span id="detalleCliente"></span></p>
                                        <p><strong>Email:</strong> <span id="detalleEmailCliente"></span></p>
                                        <p><strong>Teléfono:</strong> <span id="detalleTelefonoCliente"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Productos del pedido -->
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Productos del Pedido</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Precio Unit.</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detalleProductosTableBody">
                                            <!-- Los productos se cargarán aquí -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Resumen del pedido -->
                                <div class="row mt-3">
                                    <div class="col-md-6 offset-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <span><strong>Total de Items:</strong></span>
                                                    <span id="detalleTotalItems"></span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span><strong>Productos Diferentes:</strong></span>
                                                    <span id="detalleTotalProductos"></span>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between">
                                                    <span><strong>TOTAL:</strong></span>
                                                    <span id="detalleTotalPedido" class="text-success fs-5"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error -->
                    <div id="detalleError" style="display: none;" class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="detalleErrorMessage"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cerrar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnImprimirPedido" onclick="imprimirPedido()">
                        <i class="fas fa-print me-2"></i>Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas flotantes -->
    <div id="alertContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variables globales
        let pedidosData = [];
        let inventarioData = [];
        
        // Función para mostrar alertas flotantes
        function mostrarAlerta(mensaje, tipo = 'success') {
            const alertContainer = document.getElementById('alertContainer');
            const alertId = 'alert_' + Date.now();
            
            const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
            const iconClass = tipo === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
            
            const alertHTML = `
                <div id="${alertId}" class="alert ${alertClass} alert-floating" role="alert">
                    <i class="${iconClass} me-2"></i>
                    ${mensaje}
                </div>
            `;
            
            alertContainer.insertAdjacentHTML('beforeend', alertHTML);
            
            // Auto-eliminar después de 4 segundos
            setTimeout(() => {
                const alertElement = document.getElementById(alertId);
                if (alertElement) {
                    alertElement.classList.add('fade-out');
                    setTimeout(() => {
                        alertElement.remove();
                    }, 500);
                }
            }, 4000);
        }
        
        // Función para abrir modal de pedidos
        function abrirModalPedidos() {
            const modal = new bootstrap.Modal(document.getElementById('pedidosModal'));
            modal.show();
            cargarPedidos();
        }
        
        // Función para abrir modal de inventario
        function abrirModalInventario() {
            const modal = new bootstrap.Modal(document.getElementById('inventarioModal'));
            modal.show();
            cargarInventario();
        }
        
        // Función para cargar pedidos
        async function cargarPedidos() {
            const loader = document.getElementById('pedidosLoader');
            const container = document.getElementById('pedidosContainer');
            const errorDiv = document.getElementById('pedidosError');
            
            try {
                // Mostrar loader
                loader.style.display = 'block';
                container.style.display = 'none';
                errorDiv.style.display = 'none';
                
                console.log('Cargando pedidos...');
                
                const response = await fetch('obtener_pedidos_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        estado: document.getElementById('filtroEstadoPedidos').value,
                        fecha: document.getElementById('filtroFechaPedidos').value,
                        solo_pendientes: true
                    })
                });
                
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                const responseText = await response.text();
                console.log('Response text:', responseText);
                
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}\nRespuesta: ${responseText}`);
                }
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    throw new Error(`Error parsing JSON: ${parseError.message}\nRespuesta: ${responseText}`);
                }
                
                if (data.success) {
                    pedidosData = data.pedidos || [];
                    renderizarPedidos(pedidosData);
                    
                    // Ocultar loader y mostrar container
                    loader.style.display = 'none';
                    container.style.display = 'block';
                    
                    mostrarAlerta(`Se cargaron ${pedidosData.length} pedidos`, 'success');
                } else {
                    throw new Error(data.mensaje || 'Error al cargar pedidos');
                }
                
            } catch (error) {
                console.error('Error cargando pedidos:', error);
                
                // Mostrar error
                loader.style.display = 'none';
                container.style.display = 'none';
                errorDiv.style.display = 'block';
                document.getElementById('pedidosErrorMessage').textContent = error.message;
                
                mostrarAlerta('Error al cargar pedidos: ' + error.message, 'error');
            }
        }
        
        // Función para cargar inventario
        async function cargarInventario() {
            const loader = document.getElementById('inventarioLoader');
            const container = document.getElementById('inventarioContainer');
            const errorDiv = document.getElementById('inventarioError');
            
            try {
                // Mostrar loader
                loader.style.display = 'block';
                container.style.display = 'none';
                errorDiv.style.display = 'none';
                
                console.log('Cargando inventario...');
                
                const response = await fetch('obtener_productos_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        buscar: document.getElementById('buscarProducto').value,
                        estado: document.getElementById('filtroEstadoProducto').value,
                        categoria: document.getElementById('filtroCategoria').value
                    })
                });
                
                console.log('Response status:', response.status);
                
                const responseText = await response.text();
                console.log('Response text:', responseText);
                
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}\nRespuesta: ${responseText}`);
                }
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    throw new Error(`Error parsing JSON: ${parseError.message}\nRespuesta: ${responseText}`);
                }
                
                if (data.success) {
                    inventarioData = data.productos || [];
                    renderizarInventario(inventarioData);
                    
                    // Cargar categorías en el filtro si es la primera vez
                    if (data.categorias && data.categorias.length > 0) {
                        const filtroCategoria = document.getElementById('filtroCategoria');
                        const currentValue = filtroCategoria.value;
                        
                        // Mantener la opción "Todas las categorías"
                        filtroCategoria.innerHTML = '<option value="">Todas las categorías</option>';
                        
                        data.categorias.forEach(categoria => {
                            const option = document.createElement('option');
                            option.value = categoria.id;
                            option.textContent = categoria.nombre;
                            filtroCategoria.appendChild(option);
                        });
                        
                        // Restaurar valor seleccionado
                        filtroCategoria.value = currentValue;
                    }
                    
                    // Ocultar loader y mostrar container
                    loader.style.display = 'none';
                    container.style.display = 'block';
                    
                    mostrarAlerta(`Se cargaron ${inventarioData.length} productos`, 'success');
                } else {
                    throw new Error(data.mensaje || 'Error al cargar inventario');
                }
                
            } catch (error) {
                console.error('Error cargando inventario:', error);
                
                // Mostrar error
                loader.style.display = 'none';
                container.style.display = 'none';
                errorDiv.style.display = 'block';
                document.getElementById('inventarioErrorMessage').textContent = error.message;
                
                mostrarAlerta('Error al cargar inventario: ' + error.message, 'error');
            }
        }
        
        // Función para renderizar la tabla de pedidos
        function renderizarPedidos(pedidos) {
            const tbody = document.getElementById('pedidosTableBody');
            
            if (!pedidos || pedidos.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                            <p>No hay pedidos para mostrar con los filtros seleccionados</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = pedidos.map(pedido => {
                const estadoClass = getEstadoClass(pedido.estado);
                
                return `
                    <tr data-pedido-id="${pedido.id}">
                        <td>#${pedido.id}</td>
                        <td>${pedido.cliente}</td>
                        <td>${pedido.empleado || 'No asignado'}</td>
                        <td>${formatearFecha(pedido.fecha_ingreso)}</td>
                        <td>${formatearFecha(pedido.fecha_entrega)}</td>
                        <td>$${Number(pedido.total).toLocaleString()}</td>
                        <td>
                            <span class="badge-pedido ${estadoClass}">
                                ${pedido.estado_nombre}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info me-1" 
                                    onclick="verDetallePedido(${pedido.id})"
                                    title="Ver Detalle">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${getStatusButtons(pedido.estado, pedido.id)}
                        </td>
                    </tr>
                `;
            }).join('');
        }
        
        // Función para renderizar la tabla de inventario
        function renderizarInventario(productos) {
            const tbody = document.getElementById('inventarioTableBody');
            
            if (!productos || productos.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-box fa-3x mb-3"></i>
                            <p>No hay productos para mostrar</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = productos.map(producto => {
                const estadoClass = producto.activo ? 'status-activo' : 'status-inactivo';
                const estadoTexto = producto.activo ? 'Activo' : 'Inactivo';
                
                return `
                    <tr data-producto-id="${producto.id}">
                        <td>#${producto.id}</td>
                        <td>${producto.nombre}</td>
                        <td>${producto.categoria}</td>
                        <td>$${Number(producto.precio).toLocaleString()}</td>
                        <td>${producto.stock_min}</td>
                        <td>${formatearFecha(producto.vencimiento)}</td>
                        <td>
                            <span class="badge-pedido ${estadoClass}">
                                ${estadoTexto}
                            </span>
                        </td>
                    </tr>
                `;
            }).join('');
        }
        
        // Función auxiliar para obtener la clase del estado
        function getEstadoClass(estado) {
            switch(estado) {
                case 1: return 'status-pendiente';
                case 2: return 'status-preparacion';
                case 3: return 'status-listo';
                case 4: return 'status-entregado';
                case 5: return 'status-cancelado';
                default: return 'status-pendiente';
            }
        }
        
        // Función para generar botones de cambio de estado
        function getStatusButtons(estadoActual, pedidoId) {
            let buttons = '';
            
            // Botones según el estado actual
            switch(estadoActual) {
                case 1: // Pendiente
                    buttons = `
                        <button type="button" class="btn btn-sm btn-warning me-1" 
                                onclick="cambiarEstadoPedido(${pedidoId}, 2)"
                                title="Marcar En Preparación">
                            <i class="fas fa-play"></i>
                        </button>
                    `;
                    break;
                case 2: // En Preparación  
                    buttons = `
                        <button type="button" class="btn btn-sm btn-success me-1" 
                                onclick="cambiarEstadoPedido(${pedidoId}, 3)"
                                title="Marcar Listo para Entrega">
                            <i class="fas fa-check"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary me-1" 
                                onclick="cambiarEstadoPedido(${pedidoId}, 1)"
                                title="Regresar a Pendiente">
                            <i class="fas fa-undo"></i>
                        </button>
                    `;
                    break;
                case 3: // Listo para Entrega
                    buttons = `
                        <button type="button" class="btn btn-sm btn-primary me-1" 
                                onclick="cambiarEstadoPedido(${pedidoId}, 4)"
                                title="Marcar como Entregado">
                            <i class="fas fa-truck"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-warning me-1" 
                                onclick="cambiarEstadoPedido(${pedidoId}, 2)"
                                title="Regresar a Preparación">
                            <i class="fas fa-undo"></i>
                        </button>
                    `;
                    break;
                case 4: // Entregado
                    buttons = `
                        <span class="text-success">
                            <i class="fas fa-check-circle me-1"></i>Completado
                        </span>
                    `;
                    break;
                case 5: // Cancelado
                    buttons = `
                        <span class="text-danger">
                            <i class="fas fa-times-circle me-1"></i>Cancelado
                        </span>
                    `;
                    break;
            }
            
            // Agregar botón de cancelar si no está cancelado o entregado
            if (estadoActual !== 4 && estadoActual !== 5) {
                buttons += `
                    <button type="button" class="btn btn-sm btn-danger me-1" 
                            onclick="cancelarPedido(${pedidoId})"
                            title="Cancelar Pedido">
                        <i class="fas fa-ban"></i>
                    </button>
                `;
            }
            
            return buttons;
        }
        
        // Función auxiliar para formatear fechas
        function formatearFecha(fecha) {
            const date = new Date(fecha);
            return date.toLocaleString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        // Función para ver detalle de pedido
        async function verDetallePedido(pedidoId) {
            try {
                mostrarAlerta(`Cargando detalle del pedido #${pedidoId}...`, 'success');
                
                // Abrir el modal
                const modal = new bootstrap.Modal(document.getElementById('detallePedidoModal'));
                modal.show();
                
                // Mostrar loader y ocultar contenido
                document.getElementById('detalleLoader').style.display = 'block';
                document.getElementById('detalleContainer').style.display = 'none';
                document.getElementById('detalleError').style.display = 'none';
                
                // Hacer petición AJAX
                const response = await fetch('obtener_detalle_pedido_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        pedido_id: pedidoId
                    })
                });
                
                const responseText = await response.text();
                console.log('Detalle pedido response:', responseText);
                
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}\nRespuesta: ${responseText}`);
                }
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    throw new Error(`Error parsing JSON: ${parseError.message}\nRespuesta: ${responseText}`);
                }
                
                if (data.success) {
                    // Cargar datos en el modal
                    cargarDetallePedidoEnModal(data);
                    
                    // Ocultar loader y mostrar contenido
                    document.getElementById('detalleLoader').style.display = 'none';
                    document.getElementById('detalleContainer').style.display = 'block';
                    
                    mostrarAlerta(`✓ Detalle del pedido #${pedidoId} cargado`, 'success');
                } else {
                    throw new Error(data.mensaje || 'Error al cargar detalle del pedido');
                }
                
            } catch (error) {
                console.error('Error cargando detalle del pedido:', error);
                
                // Mostrar error
                document.getElementById('detalleLoader').style.display = 'none';
                document.getElementById('detalleContainer').style.display = 'none';
                document.getElementById('detalleError').style.display = 'block';
                document.getElementById('detalleErrorMessage').textContent = error.message;
                
                mostrarAlerta('Error al cargar detalle: ' + error.message, 'error');
            }
        }
        
        // Función para cargar los datos del detalle en el modal
        function cargarDetallePedidoEnModal(data) {
            const pedido = data.pedido;
            const productos = data.productos;
            const resumen = data.resumen;
            
            // Información básica del pedido
            document.getElementById('detallePedidoId').textContent = '#' + pedido.id;
            document.getElementById('detallePedidoEstado').innerHTML = `<span class="badge-pedido ${getEstadoClass(pedido.estado_id)}">${pedido.estado_nombre}</span>`;
            document.getElementById('detalleFechaIngreso').textContent = formatearFecha(pedido.fecha_ingreso);
            document.getElementById('detalleFechaEntrega').textContent = formatearFecha(pedido.fecha_entrega);
            document.getElementById('detalleEmpleado').textContent = pedido.empleado;
            
            // Información del cliente
            document.getElementById('detalleCliente').textContent = pedido.cliente;
            document.getElementById('detalleEmailCliente').textContent = pedido.email_cliente || 'No disponible';
            document.getElementById('detalleTelefonoCliente').textContent = pedido.telefono_cliente || 'No disponible';
            
            // Actualizar título del modal
            document.getElementById('detallePedidoModalLabel').innerHTML = `<i class="fas fa-receipt me-2"></i>Detalle del Pedido #${pedido.id}`;
            
            // Tabla de productos
            const tbody = document.getElementById('detalleProductosTableBody');
            
            if (!productos || productos.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                            <p>No hay productos en este pedido</p>
                        </td>
                    </tr>
                `;
            } else {
                tbody.innerHTML = productos.map(producto => {
                    return `
                        <tr>
                            <td>
                                <span class="badge bg-info fs-6">#${producto.producto_id}</span>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-bold">${producto.nombre}</div>
                                    ${producto.descripcion ? `<small class="text-muted">${producto.descripcion}</small>` : ''}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary fs-6">${producto.cantidad}</span>
                            </td>
                            <td>$${Number(producto.precio_unitario).toLocaleString()}</td>
                            <td>
                                <strong class="text-success">$${Number(producto.subtotal).toLocaleString()}</strong>
                            </td>
                        </tr>
                    `;
                }).join('');
            }
            
            // Resumen
            document.getElementById('detalleTotalItems').textContent = resumen.total_items;
            document.getElementById('detalleTotalProductos').textContent = resumen.total_productos_diferentes;
            document.getElementById('detalleTotalPedido').textContent = '$' + Number(pedido.total).toLocaleString();
        }
        
        // Función para imprimir pedido
        function imprimirPedido() {
            mostrarAlerta('Función de impresión pendiente de implementar', 'success');
            // Aquí se podría implementar la impresión del pedido
        }
        
        // Función para cambiar estado de pedido
        async function cambiarEstadoPedido(pedidoId, nuevoEstado) {
            const estadosNombres = {
                1: 'Pendiente',
                2: 'En Preparación', 
                3: 'Listo para Entrega',
                4: 'Entregado',
                5: 'Cancelado'
            };
            
            const confirmMessage = `¿Está seguro de cambiar el pedido #${pedidoId} a "${estadosNombres[nuevoEstado]}"?`;
            
            if (!confirm(confirmMessage)) {
                return;
            }
            
            try {
                mostrarAlerta(`Cambiando estado del pedido #${pedidoId}...`, 'success');
                
                const response = await fetch('cambiar_estado_pedido_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        pedido_id: pedidoId,
                        nuevo_estado: nuevoEstado
                    })
                });
                
                const responseText = await response.text();
                console.log('Change status response:', responseText);
                
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}\nRespuesta: ${responseText}`);
                }
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    throw new Error(`Error parsing JSON: ${parseError.message}\nRespuesta: ${responseText}`);
                }
                
                if (data.success) {
                    mostrarAlerta(`✓ Pedido #${pedidoId} cambiado a "${estadosNombres[nuevoEstado]}"`, 'success');
                    
                    // Recargar la tabla de pedidos para mostrar el cambio
                    cargarPedidos();
                } else {
                    throw new Error(data.mensaje || 'Error al cambiar estado del pedido');
                }
                
            } catch (error) {
                console.error('Error cambiando estado:', error);
                mostrarAlerta('Error al cambiar estado: ' + error.message, 'error');
            }
        }
        
        // Función para cancelar pedido
        async function cancelarPedido(pedidoId) {
            const confirmMessage = `¿Está seguro de CANCELAR el pedido #${pedidoId}? Esta acción no se puede deshacer.`;
            
            if (!confirm(confirmMessage)) {
                return;
            }
            
            await cambiarEstadoPedido(pedidoId, 5);
        }
        
        // Función para probar la base de datos
        async function testDatabase() {
            try {
                mostrarAlerta('Probando conexión a la base de datos...', 'success');
                
                const response = await fetch('test_database.php');
                const responseText = await response.text();
                
                console.log('Database test response:', responseText);
                
                const data = JSON.parse(responseText);
                
                if (data.success) {
                    console.log('Database tests:', data.tests);
                    console.log('Session info:', data.session_info);
                    mostrarAlerta('Test de base de datos completado. Ver consola para detalles.', 'success');
                } else {
                    console.error('Database test failed:', data);
                    mostrarAlerta(`Error en test: ${data.error}`, 'error');
                }
                
            } catch (error) {
                console.error('Error testing database:', error);
                mostrarAlerta('Error probando base de datos: ' + error.message, 'error');
            }
        }
        
        // Función para probar el estado de la sesión
        async function testSession() {
            try {
                mostrarAlerta('Verificando estado de la sesión...', 'success');
                
                const response = await fetch('session_check.php');
                const responseText = await response.text();
                
                console.log('Session check response:', responseText);
                
                const data = JSON.parse(responseText);
                
                console.log('Session data:', data);
                console.log('Is empleado:', data.is_empleado);
                console.log('Session ID:', data.session_id);
                console.log('Auth check:', data.auth_check);
                
                if (data.is_empleado) {
                    mostrarAlerta('✓ Sesión de empleado válida', 'success');
                } else {
                    mostrarAlerta('✗ Sesión inválida - ' + JSON.stringify(data.auth_check), 'error');
                }
                
                // También abrir la página de debug en nueva ventana
                window.open('session_debug.php', '_blank');
                
            } catch (error) {
                console.error('Error testing session:', error);
                mostrarAlerta('Error probando sesión: ' + error.message, 'error');
            }
        }
        
        // Función para probar bypass temporal
        async function testBypass() {
            try {
                mostrarAlerta('Probando bypass de sesión (SOLO TESTING)...', 'success');
                
                const response = await fetch('obtener_pedidos_bypass.php');
                const responseText = await response.text();
                
                console.log('Bypass response:', responseText);
                
                const data = JSON.parse(responseText);
                
                if (data.success) {
                    console.log('Bypass data:', data);
                    mostrarAlerta(`✓ Bypass OK: ${data.pedidos?.length || 0} pedidos`, 'success');
                    
                    // Try to load the data in the modal
                    if (data.pedidos && data.pedidos.length > 0) {
                        pedidosData = data.pedidos;
                        renderizarPedidos(pedidosData);
                        
                        // Show the modal
                        const modal = new bootstrap.Modal(document.getElementById('pedidosModal'));
                        modal.show();
                        
                        // Hide loader and show container
                        document.getElementById('pedidosLoader').style.display = 'none';
                        document.getElementById('pedidosContainer').style.display = 'block';
                    }
                } else {
                    mostrarAlerta(`✗ Bypass falló: ${data.mensaje}`, 'error');
                }
                
            } catch (error) {
                console.error('Error testing bypass:', error);
                mostrarAlerta('Error en bypass: ' + error.message, 'error');
            }
        }
        
        // Función para probar login de empleado
        function testEmployeeLogin() {
            mostrarAlerta('Abriendo test de login de empleado...', 'success');
            window.open('employee_login_test.php', '_blank');
        }
        
        // Función para probar AJAX directamente
        async function testAjaxDirectly() {
            console.log('=== TESTING AJAX ENDPOINTS ===');
            mostrarAlerta('Probando endpoints AJAX...', 'success');
            
            // Test pedidos
            try {
                console.log('Testing pedidos endpoint...');
                const pedidosResponse = await fetch('obtener_pedidos_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        estado: '',
                        fecha: '',
                        solo_pendientes: true
                    })
                });
                
                const pedidosText = await pedidosResponse.text();
                console.log('Pedidos Response Status:', pedidosResponse.status);
                console.log('Pedidos Response Text:', pedidosText);
                
                if (pedidosResponse.ok) {
                    const pedidosData = JSON.parse(pedidosText);
                    console.log('Pedidos Data:', pedidosData);
                    mostrarAlerta(`Pedidos OK: ${pedidosData.pedidos?.length || 0} encontrados`, 'success');
                } else {
                    console.error('Pedidos Error:', pedidosText);
                    mostrarAlerta(`Pedidos Error: ${pedidosResponse.status}`, 'error');
                }
            } catch (error) {
                console.error('Error testing pedidos:', error);
                mostrarAlerta('Error en test pedidos: ' + error.message, 'error');
            }
            
            // Test productos
            try {
                console.log('Testing productos endpoint...');
                const productosResponse = await fetch('obtener_productos_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        buscar: '',
                        estado: '1',
                        categoria: ''
                    })
                });
                
                const productosText = await productosResponse.text();
                console.log('Productos Response Status:', productosResponse.status);
                console.log('Productos Response Text:', productosText);
                
                if (productosResponse.ok) {
                    const productosData = JSON.parse(productosText);
                    console.log('Productos Data:', productosData);
                    mostrarAlerta(`Productos OK: ${productosData.productos?.length || 0} encontrados`, 'success');
                } else {
                    console.error('Productos Error:', productosText);
                    mostrarAlerta(`Productos Error: ${productosResponse.status}`, 'error');
                }
            } catch (error) {
                console.error('Error testing productos:', error);
                mostrarAlerta('Error en test productos: ' + error.message, 'error');
            }
        }
        
        // Función para probar pedidos simple
        async function testSimplePedidos() {
            try {
                console.log('Testing simple pedidos endpoint...');
                mostrarAlerta('Probando endpoint simple de pedidos...', 'success');
                
                const response = await fetch('test_pedidos_simple.php');
                const responseText = await response.text();
                
                console.log('Simple Pedidos Response Status:', response.status);
                console.log('Simple Pedidos Response Text:', responseText);
                
                if (response.ok) {
                    const data = JSON.parse(responseText);
                    console.log('Simple Pedidos Data:', data);
                    
                    if (data.success) {
                        mostrarAlerta(`Pedidos Simple OK: ${data.total_pedidos} total, ${data.pedidos?.length || 0} ejemplos`, 'success');
                        
                        // Try to render the data in the modal for testing
                        if (data.pedidos && data.pedidos.length > 0) {
                            pedidosData = data.pedidos;
                            renderizarPedidos(pedidosData);
                            
                            // Show the modal
                            const modal = new bootstrap.Modal(document.getElementById('pedidosModal'));
                            modal.show();
                            
                            // Hide loader and show container
                            document.getElementById('pedidosLoader').style.display = 'none';
                            document.getElementById('pedidosContainer').style.display = 'block';
                        }
                    } else {
                        mostrarAlerta(`Error en pedidos simple: ${data.mensaje}`, 'error');
                    }
                } else {
                    console.error('Simple Pedidos Error:', responseText);
                    mostrarAlerta(`Error HTTP en pedidos simple: ${response.status}`, 'error');
                }
            } catch (error) {
                console.error('Error testing simple pedidos:', error);
                mostrarAlerta('Error en test pedidos simple: ' + error.message, 'error');
            }
        }
        
        // Función para probar detalle de pedido
        function testDetallePedido() {
            mostrarAlerta('Abriendo test de detalle de pedido...', 'success');
            window.open('test_detalle_pedido.php', '_blank');
        }
        
        // Función para probar la corrección de detalle de pedido
        function testCorreccionDetalle() {
            mostrarAlerta('Abriendo test de corrección de detalle...', 'success');
            window.open('test_detalle_pedido_correccion.php', '_blank');
        }
        
        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Activar enlace correcto en el sidebar
            try {
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                });
                
                const dashboardLink = document.querySelector('a[href="#dashboard"]');
                if (dashboardLink) {
                    dashboardLink.classList.add('active');
                }
            } catch (error) {
                console.error('Error en navegación:', error);
            }
            
            // Filtros en tiempo real para inventario
            document.getElementById('buscarProducto').addEventListener('input', function() {
                // Filtrar inventario localmente
                if (inventarioData.length > 0) {
                    const texto = this.value.toLowerCase();
                    const productosFiltrados = inventarioData.filter(producto => 
                        producto.nombre.toLowerCase().includes(texto)
                    );
                    renderizarInventario(productosFiltrados);
                }
            });
            
            // Event listeners para filtros de pedidos
            document.getElementById('filtroEstadoPedidos').addEventListener('change', function() {
                cargarPedidos();
            });
            
            document.getElementById('filtroFechaPedidos').addEventListener('change', function() {
                cargarPedidos();
            });
            
            // Event listeners para filtros de inventario
            document.getElementById('filtroEstadoProducto').addEventListener('change', function() {
                cargarInventario();
            });
            
            document.getElementById('filtroCategoria').addEventListener('change', function() {
                cargarInventario();
            });
            
            console.log('=== PANEL EMPLEADO ===');
            console.log('Usuario:', '<?php echo $_SESSION['usuario_nombre']; ?>');
            console.log('Tipo:', '<?php echo $_SESSION['usuario_tipo']; ?>');
            console.log('ID:', '<?php echo $_SESSION['usuario_id']; ?>');
            console.log('Sistema simplificado activado');
        });
        
        // Sistema de navegación inteligente para empleado
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
                            console.log('Navegación interna del empleado:', link.href);
                        }
                    } catch (error) {
                        isNavigatingWithinSystem = true;
                    }
                }
            });
            
            // Manejar navegación del historial
            let backButtonCount = 0;
            window.addEventListener('popstate', function(event) {
                backButtonCount++;
                
                if (backButtonCount > 3 && !isNavigatingWithinSystem) {
                    if (confirm('¿Deseas cerrar la sesión de empleado?')) {
                        window.location.href = 'logout.php';
                    } else {
                        backButtonCount = 0;
                        history.pushState(null, null, window.location.pathname);
                    }
                } else {
                    console.log('Navegación permitida para empleado, intento:', backButtonCount);
                }
                
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
