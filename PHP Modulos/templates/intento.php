
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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="stylesheet" href="css/styleadmindst.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
                            <button type="button" class="action-btn" data-bs-toggle="modal" data-bs-target="#empleadosModal">
                                <i class="fas fa-users-cog me-2"></i>Gestionar Empleados
                            </button>
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

                    <div class="action-card">
                        <div class="card-header">
                            <h3><i class="fas fa-cubes me-2"></i>Gestión de Ingredientes</h3>
                        </div>
                        <div class="card-body">
                            <button type="button" class="action-btn" data-bs-toggle="modal" data-bs-target="#ingredientesModal">
                                <i class="fas fa-list-ul me-2"></i>Ver Ingredientes y Proveedores
                            </button>
                            <button type="button" class="action-btn" data-bs-toggle="modal" data-bs-target="#agregarIngredienteModal" onclick="abrirModalAgregarIngrediente()">
                                <i class="fas fa-plus me-2"></i>Agregar Ingrediente
                            </button>
                            <a href="proveedores_tabla.php" class="action-btn">
                                <i class="fas fa-truck me-2"></i>Gestionar Proveedores
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Gestión de Empleados -->
<div class="modal fade" id="empleadosModal" tabindex="-1" aria-labelledby="empleadosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="empleadosModalLabel">
                    <i class="fas fa-users me-2"></i>Gestión de Empleados
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Sección de agregar empleado -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-user-plus me-2"></i>Agregar Nuevo Empleado</h6>
                            </div>
                            <div class="card-body">
                                <form id="formAgregarEmpleado" class="row g-3">
                                    <div class="col-md-4">
                                        <label for="empleadoNombre" class="form-label">Nombre Completo</label>
                                        <input type="text" class="form-control" id="empleadoNombre" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="empleadoEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="empleadoEmail" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="empleadoPassword" class="form-label">Contraseña</label>
                                        <input type="password" class="form-control" id="empleadoPassword" required minlength="6">
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-plus me-2"></i>Agregar Empleado
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de lista de empleados -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-list me-2"></i>Lista de Empleados</h6>
                            </div>
                            <div class="card-body">
                                <div id="empleadosLoader" class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-2">Cargando empleados...</p>
                                </div>
                                <div id="empleadosContainer" style="display: none;">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Email</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="empleadosTableBody">
                                                <!-- Los empleados se cargarán aquí -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div id="empleadosError" style="display: none;" class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <span id="empleadosErrorMessage"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cerrar
                </button>
                <button type="button" class="btn btn-primary" onclick="cargarEmpleados()">
                    <i class="fas fa-sync-alt me-2"></i>Actualizar Lista
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Gestión de Ingredientes -->
<div class="modal fade" id="ingredientesModal" tabindex="-1" aria-labelledby="ingredientesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="ingredientesModalLabel">
                    <i class="fas fa-cubes me-2"></i>Gestión de Ingredientes y Proveedores
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Sección de estadísticas rápidas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h5 id="totalIngredientes">0</h5>
                                <small>Total Ingredientes</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h5 id="totalProveedores">0</h5>
                                <small>Proveedores Activos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h5 id="stockBajo">0</h5>
                                <small>Stock Bajo (≤ 10)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h5 id="valorInventario">$0</h5>
                                <small>Valor Inventario</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de ingredientes -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-list me-2"></i>Inventario de Ingredientes</h6>
                            </div>
                            <div class="card-body">
                                <div id="ingredientesLoader" class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-2">Cargando ingredientes...</p>
                                </div>
                                <div id="ingredientesContainer" style="display: none;">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Ingrediente</th>
                                                    <th>Categoría</th>
                                                    <th>Proveedor</th>
                                                    <th>Stock</th>
                                                    <th>Precio Compra</th>
                                                    <th>Valor Total</th>
                                                    <th>Referencia</th>
                                                    <th>Vencimiento</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="ingredientesTableBody">
                                                <!-- Los ingredientes se cargarán aquí -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div id="ingredientesError" style="display: none;" class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <span id="ingredientesErrorMessage"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cerrar
                </button>
                <button type="button" class="btn btn-success" onclick="cargarIngredientes()">
                    <i class="fas fa-sync-alt me-2"></i>Actualizar Lista
                </button>
                <button type="button" class="btn btn-primary" onclick="exportarInventario()">
                    <i class="fas fa-download me-2"></i>Exportar Inventario
                </button>
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#agregarIngredienteModal" onclick="abrirModalAgregarIngrediente()">
                    <i class="fas fa-plus me-2"></i>Agregar Ingrediente
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Agregar Ingrediente -->
<div class="modal fade" id="agregarIngredienteModal" tabindex="-1" aria-labelledby="agregarIngredienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="agregarIngredienteModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Agregar Nuevo Ingrediente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formAgregarIngrediente" class="row g-3">
                    <div class="col-md-6">
                        <label for="ingredienteNombre" class="form-label">
                            <i class="fas fa-tag me-1"></i>Nombre del Ingrediente <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="ingredienteNombre" required 
                               placeholder="Ej: Harina de Trigo">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="ingredienteReferencia" class="form-label">
                            <i class="fas fa-barcode me-1"></i>Referencia <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="ingredienteReferencia" required 
                               placeholder="Ej: HAR-TRG-001">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="ingredienteCategoria" class="form-label">
                            <i class="fas fa-layer-group me-1"></i>Categoría <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="ingredienteCategoria" required>
                            <option value="">Seleccionar categoría...</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="ingredienteProveedor" class="form-label">
                            <i class="fas fa-truck me-1"></i>Proveedor
                        </label>
                        <select class="form-select" id="ingredienteProveedor">
                            <option value="">Sin asignar</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="ingredienteCantidad" class="form-label">
                            <i class="fas fa-boxes me-1"></i>Stock Inicial
                        </label>
                        <input type="number" class="form-control" id="ingredienteCantidad" 
                               min="0" value="0" placeholder="0">
                    </div>
                    
                    <div class="col-md-4">
                        <label for="ingredientePrecio" class="form-label">
                            <i class="fas fa-dollar-sign me-1"></i>Precio de Compra
                        </label>
                        <input type="number" class="form-control" id="ingredientePrecio" 
                               min="0" step="0.01" value="0" placeholder="0.00">
                    </div>
                    
                    <div class="col-md-4">
                        <label for="ingredienteFechaVencimiento" class="form-label">
                            <i class="fas fa-calendar-times me-1"></i>Fecha Vencimiento <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="ingredienteFechaVencimiento" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="ingredienteFechaEntrega" class="form-label">
                            <i class="fas fa-calendar-check me-1"></i>Fecha de Entrega <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="ingredienteFechaEntrega" required>
                    </div>
                    
                    <div class="col-12">
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nota:</strong> Los campos marcados con <span class="text-danger">*</span> son obligatorios.
                            El proveedor es opcional y se puede asignar posteriormente.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-warning" onclick="agregarIngrediente()">
                    <i class="fas fa-plus me-2"></i>Agregar Ingrediente
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Editar Stock/Precio -->
<div class="modal fade" id="editarIngredienteModal" tabindex="-1" aria-labelledby="editarIngredienteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="editarIngredienteModalLabel">
                    <i class="fas fa-edit me-2"></i>Editar Ingrediente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarIngrediente">
                    <input type="hidden" id="editarIngredienteId">
                    <input type="hidden" id="editarIngredienteCampo">
                    
                    <div class="mb-3">
                        <label for="editarIngredienteNombre" class="form-label">Ingrediente:</label>
                        <input type="text" class="form-control" id="editarIngredienteNombre" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editarIngredienteValor" class="form-label" id="editarIngredienteLabel">Nuevo Valor:</label>
                        <input type="number" class="form-control" id="editarIngredienteValor" 
                               min="0" step="0.01" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-info" onclick="guardarCambioIngrediente()">
                    <i class="fas fa-save me-2"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alertas flotantes -->
<div id="alertContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

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

        // ===== GESTIÓN DE EMPLEADOS =====
        
        // Variables globales para empleados
        let empleadosData = [];
        
        // Función para mostrar alertas flotantes
        function mostrarAlerta(mensaje, tipo = 'success') {
            const alertContainer = document.getElementById('alertContainer');
            const alertId = 'alert_' + Date.now();
            
            const alertClass = tipo === 'success' ? 'alert-success' : 'alert-error';
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
        
        // Función para cargar empleados
        async function cargarEmpleados() {
            const loader = document.getElementById('empleadosLoader');
            const container = document.getElementById('empleadosContainer');
            const errorDiv = document.getElementById('empleadosError');
            
            try {
                // Mostrar loader
                loader.style.display = 'block';
                container.style.display = 'none';
                errorDiv.style.display = 'none';
                
                const response = await fetch('obtener_empleados_ajax.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    empleadosData = data.empleados;
                    renderizarEmpleados(empleadosData);
                    
                    // Ocultar loader y mostrar container
                    loader.style.display = 'none';
                    container.style.display = 'block';
                } else {
                    throw new Error(data.mensaje || 'Error al cargar empleados');
                }
                
            } catch (error) {
                console.error('Error cargando empleados:', error);
                
                // Mostrar error
                loader.style.display = 'none';
                container.style.display = 'none';
                errorDiv.style.display = 'block';
                document.getElementById('empleadosErrorMessage').textContent = error.message;
            }
        }
        
        // Función para renderizar la tabla de empleados
        function renderizarEmpleados(empleados) {
            const tbody = document.getElementById('empleadosTableBody');
            
            if (!empleados || empleados.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <p>No hay empleados registrados</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = empleados.map(empleado => {
                const estadoClass = empleado.activo ? 'status-active' : 'status-inactive';
                const estadoTexto = empleado.activo ? 'Activo' : 'Inactivo';
                const toggleClass = empleado.activo ? 'btn-success' : 'btn-secondary';
                const toggleIcon = empleado.activo ? 'fa-toggle-on' : 'fa-toggle-off';
                const toggleTexto = empleado.activo ? 'Desactivar' : 'Activar';
                
                return `
                    <tr data-empleado-id="${empleado.id}">
                        <td>${empleado.id}</td>
                        <td>${empleado.nombre}</td>
                        <td>${empleado.email}</td>
                        <td>
                            <span class="status-badge ${estadoClass}">
                                ${estadoTexto}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn ${toggleClass} btn-sm me-1" 
                                    onclick="toggleEstadoEmpleado(${empleado.id}, ${empleado.activo ? 0 : 1})"
                                    title="${toggleTexto}">
                                <i class="fas ${toggleIcon}"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" 
                                    onclick="confirmarEliminarEmpleado(${empleado.id}, '${empleado.nombre}')"
                                    title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }
        
        // Función para agregar empleado
        async function agregarEmpleado(formData) {
            try {
                const response = await fetch('agregar_empleado_ajax.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    mostrarAlerta('Empleado agregado exitosamente', 'success');
                    
                    // Limpiar formulario
                    document.getElementById('formAgregarEmpleado').reset();
                    
                    // Recargar lista de empleados
                    await cargarEmpleados();
                    
                    // Actualizar estadísticas del dashboard
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                    
                } else {
                    throw new Error(data.mensaje || 'Error al agregar empleado');
                }
                
            } catch (error) {
                console.error('Error agregando empleado:', error);
                mostrarAlerta('Error: ' + error.message, 'error');
            }
        }
        
        // Función para cambiar estado de empleado
        async function toggleEstadoEmpleado(empleadoId, nuevoEstado) {
            try {
                const response = await fetch('toggle_estado_empleado.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        id: empleadoId,
                        estado: nuevoEstado
                    })
                });
                
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    const accion = nuevoEstado ? 'activado' : 'desactivado';
                    mostrarAlerta(`Empleado ${accion} exitosamente`, 'success');
                    
                    // Actualizar el empleado en los datos locales
                    const empleadoIndex = empleadosData.findIndex(emp => emp.id == empleadoId);
                    if (empleadoIndex !== -1) {
                        empleadosData[empleadoIndex].activo = nuevoEstado;
                        renderizarEmpleados(empleadosData);
                    }
                    
                    // Actualizar estadísticas del dashboard después de un breve delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                    
                } else {
                    throw new Error(data.mensaje || 'Error al cambiar estado del empleado');
                }
                
            } catch (error) {
                console.error('Error cambiando estado:', error);
                mostrarAlerta('Error: ' + error.message, 'error');
            }
        }
        
        // Función para confirmar eliminación de empleado
        function confirmarEliminarEmpleado(empleadoId, nombreEmpleado) {
            if (confirm(`¿Está seguro de que desea eliminar al empleado "${nombreEmpleado}"?\n\nEsta acción no se puede deshacer.`)) {
                eliminarEmpleado(empleadoId);
            }
        }
        
        // Función para eliminar empleado
        async function eliminarEmpleado(empleadoId) {
            try {
                const response = await fetch('eliminar_empleado_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        id: empleadoId
                    })
                });
                
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    mostrarAlerta('Empleado eliminado exitosamente', 'success');
                    
                    // Remover empleado de los datos locales
                    empleadosData = empleadosData.filter(emp => emp.id != empleadoId);
                    renderizarEmpleados(empleadosData);
                    
                    // Actualizar estadísticas del dashboard después de un breve delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                    
                } else {
                    throw new Error(data.mensaje || 'Error al eliminar empleado');
                }
                
            } catch (error) {
                console.error('Error eliminando empleado:', error);
                mostrarAlerta('Error: ' + error.message, 'error');
            }
        }
        
        // Event listeners para el modal de empleados
        document.addEventListener('DOMContentLoaded', function() {
            // Cargar empleados cuando se abra el modal
            const empleadosModal = document.getElementById('empleadosModal');
            if (empleadosModal) {
                empleadosModal.addEventListener('shown.bs.modal', function() {
                    cargarEmpleados();
                });
            }
            
            // Manejar envío del formulario de agregar empleado
            const formAgregar = document.getElementById('formAgregarEmpleado');
            if (formAgregar) {
                formAgregar.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData();
                    formData.append('nombre', document.getElementById('empleadoNombre').value.trim());
                    formData.append('email', document.getElementById('empleadoEmail').value.trim());
                    formData.append('password', document.getElementById('empleadoPassword').value);
                    
                    // Validaciones básicas
                    if (!formData.get('nombre')) {
                        mostrarAlerta('El nombre es requerido', 'error');
                        return;
                    }
                    
                    if (!formData.get('email')) {
                        mostrarAlerta('El email es requerido', 'error');
                        return;
                    }
                    
                    if (!formData.get('password') || formData.get('password').length < 6) {
                        mostrarAlerta('La contraseña debe tener al menos 6 caracteres', 'error');
                        return;
                    }
                    
                    await agregarEmpleado(formData);
                });
            }

            // ===== GESTIÓN DE INGREDIENTES =====
            
            // Cargar ingredientes cuando se abra el modal
            const ingredientesModal = document.getElementById('ingredientesModal');
            if (ingredientesModal) {
                ingredientesModal.addEventListener('shown.bs.modal', function() {
                    cargarIngredientes();
                });
            }
        });

        // ===== FUNCIONES PARA INGREDIENTES =====
        
        // Variables globales para ingredientes
        let ingredientesData = [];
        
        // Función para cargar ingredientes
        async function cargarIngredientes() {
            const loader = document.getElementById('ingredientesLoader');
            const container = document.getElementById('ingredientesContainer');
            const errorDiv = document.getElementById('ingredientesError');
            
            try {
                // Mostrar loader
                loader.style.display = 'block';
                container.style.display = 'none';
                errorDiv.style.display = 'none';
                
                const response = await fetch('obtener_ingredientes_proveedores_ajax.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    ingredientesData = data.data;
                    renderizarIngredientes(ingredientesData);
                    actualizarEstadisticasIngredientes(ingredientesData);
                    
                    // Ocultar loader y mostrar container
                    loader.style.display = 'none';
                    container.style.display = 'block';
                } else {
                    throw new Error(data.error || 'Error al cargar ingredientes');
                }
                
            } catch (error) {
                console.error('Error cargando ingredientes:', error);
                
                // Mostrar error
                loader.style.display = 'none';
                container.style.display = 'none';
                errorDiv.style.display = 'block';
                document.getElementById('ingredientesErrorMessage').textContent = error.message;
            }
        }
        
        // Función para renderizar la tabla de ingredientes
        function renderizarIngredientes(ingredientes) {
            const tbody = document.getElementById('ingredientesTableBody');
            
            if (!ingredientes || ingredientes.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="fas fa-cubes fa-3x mb-3"></i>
                            <p>No hay ingredientes registrados</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = ingredientes.map(ingrediente => {
                const stockClass = ingrediente.stock <= 10 ? 'text-danger fw-bold' : 
                                  ingrediente.stock <= 20 ? 'text-warning fw-bold' : 'text-success';
                
                const precioCompra = parseFloat(ingrediente.precio_compra) || 0;
                const stock = parseInt(ingrediente.stock) || 0;
                const valorTotal = precioCompra * stock;
                
                const fechaVencimiento = new Date(ingrediente.fecha_vencimiento);
                const hoy = new Date();
                const diasVencimiento = Math.ceil((fechaVencimiento - hoy) / (1000 * 60 * 60 * 24));
                
                let vencimientoClass = '';
                if (diasVencimiento < 0) {
                    vencimientoClass = 'text-danger fw-bold';
                } else if (diasVencimiento <= 7) {
                    vencimientoClass = 'text-warning fw-bold';
                } else {
                    vencimientoClass = 'text-success';
                }
                
                return `
                    <tr>
                        <td>${ingrediente.id}</td>
                        <td><strong>${ingrediente.nombre}</strong></td>
                        <td><span class="badge bg-secondary">${ingrediente.categoria}</span></td>
                        <td>
                            ${ingrediente.proveedor}
                            ${ingrediente.telefono_proveedor ? `<br><small class="text-muted">${ingrediente.telefono_proveedor}</small>` : ''}
                        </td>
                        <td class="${stockClass}">
                            <span class="stock-editable" data-id="${ingrediente.id}" data-campo="stock" data-valor="${stock}">
                                ${stock}
                            </span>
                            <button class="btn btn-sm btn-outline-primary ms-1" onclick="editarIngrediente(${ingrediente.id}, 'stock', ${stock}, '${ingrediente.nombre}')" title="Editar Stock">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                        <td>
                            <span class="precio-editable" data-id="${ingrediente.id}" data-campo="precio" data-valor="${precioCompra}">
                                $${precioCompra.toLocaleString('es-CO', {minimumFractionDigits: 2})}
                            </span>
                            <button class="btn btn-sm btn-outline-success ms-1" onclick="editarIngrediente(${ingrediente.id}, 'precio', ${precioCompra}, '${ingrediente.nombre}')" title="Editar Precio">
                                <i class="fas fa-dollar-sign"></i>
                            </button>
                        </td>
                        <td class="fw-bold">$${valorTotal.toLocaleString('es-CO', {minimumFractionDigits: 2})}</td>
                        <td><code>${ingrediente.referencia}</code></td>
                        <td class="${vencimientoClass}">
                            ${ingrediente.fecha_vencimiento}
                            ${diasVencimiento < 0 ? '<br><small>(Vencido)</small>' : 
                              diasVencimiento <= 7 ? `<br><small>(${diasVencimiento} días)</small>` : ''}
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-info" onclick="verDetalleIngrediente(${ingrediente.id})" title="Ver Detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="editarIngrediente(${ingrediente.id}, 'stock', ${stock}, '${ingrediente.nombre}')" title="Editar Stock">
                                    <i class="fas fa-boxes"></i>
                                </button>
                                <button class="btn btn-sm btn-success" onclick="editarIngrediente(${ingrediente.id}, 'precio', ${precioCompra}, '${ingrediente.nombre}')" title="Editar Precio">
                                    <i class="fas fa-dollar-sign"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }
        
        // Función para actualizar estadísticas de ingredientes
        function actualizarEstadisticasIngredientes(ingredientes) {
            const totalIngredientes = ingredientes.length;
            const proveedoresUnicos = [...new Set(ingredientes.map(i => i.proveedor))].filter(p => p !== 'Sin asignar').length;
            const stockBajo = ingredientes.filter(i => parseInt(i.stock) <= 10).length;
            const valorTotal = ingredientes.reduce((total, ingrediente) => {
                const precio = parseFloat(ingrediente.precio_compra) || 0;
                const stock = parseInt(ingrediente.stock) || 0;
                return total + (precio * stock);
            }, 0);
            
            // Actualizar elementos en el DOM
            document.getElementById('totalIngredientes').textContent = totalIngredientes;
            document.getElementById('totalProveedores').textContent = proveedoresUnicos;
            document.getElementById('stockBajo').textContent = stockBajo;
            document.getElementById('valorInventario').textContent = `$${valorTotal.toLocaleString('es-CO', {minimumFractionDigits: 2})}`;
        }
        
        // Función para exportar inventario
        function exportarInventario() {
            if (!ingredientesData || ingredientesData.length === 0) {
                mostrarAlerta('No hay datos para exportar', 'error');
                return;
            }
            
            // Crear CSV
            let csv = 'ID,Ingrediente,Categoría,Proveedor,Stock,Precio Compra,Valor Total,Referencia,Vencimiento\n';
            
            ingredientesData.forEach(ingrediente => {
                const precioCompra = parseFloat(ingrediente.precio_compra) || 0;
                const stock = parseInt(ingrediente.stock) || 0;
                const valorTotal = precioCompra * stock;
                
                csv += `${ingrediente.id},"${ingrediente.nombre}","${ingrediente.categoria}","${ingrediente.proveedor}",${stock},${precioCompra},${valorTotal},"${ingrediente.referencia}","${ingrediente.fecha_vencimiento}"\n`;
            });
            
            // Descargar archivo
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `inventario_ingredientes_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            mostrarAlerta('Inventario exportado exitosamente', 'success');
        }

        // Función para cargar categorías de ingredientes
        async function cargarCategoriasIngredientes() {
            try {
                const response = await fetch('obtener_categorias_ingredientes_ajax.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    const select = document.getElementById('ingredienteCategoria');
                    select.innerHTML = '<option value="">Seleccionar categoría...</option>';
                    
                    data.categorias.forEach(categoria => {
                        const option = document.createElement('option');
                        option.value = categoria.id;
                        option.textContent = categoria.nombre;
                        select.appendChild(option);
                    });
                }
                
            } catch (error) {
                console.error('Error cargando categorías:', error);
                mostrarAlerta('Error al cargar categorías', 'error');
            }
        }

        // Función para cargar proveedores
        async function cargarProveedoresIngredientes() {
            try {
                const response = await fetch('obtener_proveedores_ajax.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    const select = document.getElementById('ingredienteProveedor');
                    select.innerHTML = '<option value="">Sin asignar</option>';
                    
                    data.proveedores.forEach(proveedor => {
                        const option = document.createElement('option');
                        option.value = proveedor.id;
                        option.textContent = proveedor.nombre;
                        select.appendChild(option);
                    });
                }
                
            } catch (error) {
                console.error('Error cargando proveedores:', error);
                mostrarAlerta('Error al cargar proveedores', 'error');
            }
        }

        // Función para abrir modal de agregar ingrediente
        function abrirModalAgregarIngrediente() {
            // Cargar categorías y proveedores
            cargarCategoriasIngredientes();
            cargarProveedoresIngredientes();
            
            // Establecer fecha por defecto
            const hoy = new Date().toISOString().split('T')[0];
            document.getElementById('ingredienteFechaEntrega').value = hoy;
            
            // Establecer fecha de vencimiento por defecto (3 meses después)
            const fechaVencimiento = new Date();
            fechaVencimiento.setMonth(fechaVencimiento.getMonth() + 3);
            document.getElementById('ingredienteFechaVencimiento').value = fechaVencimiento.toISOString().split('T')[0];
        }

        // Función para agregar ingrediente
        async function agregarIngrediente() {
            try {
                const formData = new FormData();
                formData.append('nombre', document.getElementById('ingredienteNombre').value.trim());
                formData.append('referencia', document.getElementById('ingredienteReferencia').value.trim());
                formData.append('categoria', document.getElementById('ingredienteCategoria').value);
                formData.append('proveedor', document.getElementById('ingredienteProveedor').value);
                formData.append('cantidad', document.getElementById('ingredienteCantidad').value);
                formData.append('precio_compra', document.getElementById('ingredientePrecio').value);
                formData.append('fecha_vencimiento', document.getElementById('ingredienteFechaVencimiento').value);
                formData.append('fecha_entrega', document.getElementById('ingredienteFechaEntrega').value);

                // Validaciones básicas
                if (!formData.get('nombre')) {
                    mostrarAlerta('El nombre del ingrediente es requerido', 'error');
                    return;
                }

                if (!formData.get('referencia')) {
                    mostrarAlerta('La referencia es requerida', 'error');
                    return;
                }

                if (!formData.get('categoria')) {
                    mostrarAlerta('Debe seleccionar una categoría', 'error');
                    return;
                }

                const response = await fetch('agregar_ingrediente_ajax.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    mostrarAlerta(data.mensaje, 'success');
                    
                    // Limpiar formulario
                    document.getElementById('formAgregarIngrediente').reset();
                    
                    // Cerrar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('agregarIngredienteModal'));
                    modal.hide();
                    
                    // Recargar lista de ingredientes
                    setTimeout(() => {
                        cargarIngredientes();
                    }, 1000);
                    
                } else {
                    throw new Error(data.error || 'Error al agregar ingrediente');
                }

            } catch (error) {
                console.error('Error agregando ingrediente:', error);
                mostrarAlerta('Error: ' + error.message, 'error');
            }
        }

        // Función para editar ingrediente (stock o precio)
        function editarIngrediente(id, campo, valorActual, nombreIngrediente) {
            document.getElementById('editarIngredienteId').value = id;
            document.getElementById('editarIngredienteCampo').value = campo;
            document.getElementById('editarIngredienteNombre').value = nombreIngrediente;
            document.getElementById('editarIngredienteValor').value = valorActual;
            
            const label = document.getElementById('editarIngredienteLabel');
            if (campo === 'stock') {
                label.textContent = 'Nuevo Stock:';
                document.getElementById('editarIngredienteValor').step = '1';
            } else if (campo === 'precio') {
                label.textContent = 'Nuevo Precio de Compra:';
                document.getElementById('editarIngredienteValor').step = '0.01';
            }
            
            const modal = new bootstrap.Modal(document.getElementById('editarIngredienteModal'));
            modal.show();
        }

        // Función para guardar cambios en ingrediente
        async function guardarCambioIngrediente() {
            try {
                const id = document.getElementById('editarIngredienteId').value;
                const campo = document.getElementById('editarIngredienteCampo').value;
                const valor = document.getElementById('editarIngredienteValor').value;

                if (!valor || valor < 0) {
                    mostrarAlerta('El valor debe ser mayor o igual a 0', 'error');
                    return;
                }

                const response = await fetch('actualizar_ingrediente_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        id: parseInt(id),
                        campo: campo,
                        valor: valor
                    })
                });

                const data = await response.json();

                if (data.success) {
                    mostrarAlerta(data.mensaje, 'success');
                    
                    // Cerrar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editarIngredienteModal'));
                    modal.hide();
                    
                    // Recargar lista de ingredientes
                    setTimeout(() => {
                        cargarIngredientes();
                    }, 1000);
                    
                } else {
                    throw new Error(data.error || 'Error al actualizar ingrediente');
                }

            } catch (error) {
                console.error('Error actualizando ingrediente:', error);
                mostrarAlerta('Error: ' + error.message, 'error');
            }
        }

        // Función para ver detalle de ingrediente
        function verDetalleIngrediente(id) {
            const ingrediente = ingredientesData.find(ing => ing.id == id);
            if (ingrediente) {
                const mensaje = `
                    <strong>Ingrediente:</strong> ${ingrediente.nombre}<br>
                    <strong>Categoría:</strong> ${ingrediente.categoria}<br>
                    <strong>Proveedor:</strong> ${ingrediente.proveedor}<br>
                    <strong>Stock:</strong> ${ingrediente.stock}<br>
                    <strong>Precio:</strong> $${parseFloat(ingrediente.precio_compra).toLocaleString('es-CO', {minimumFractionDigits: 2})}<br>
                    <strong>Referencia:</strong> ${ingrediente.referencia}<br>
                    <strong>Vencimiento:</strong> ${ingrediente.fecha_vencimiento}<br>
                    <strong>Entrega:</strong> ${ingrediente.fecha_entrega}
                `;
                
                // Mostrar en un alert personalizado
                const alertHtml = `
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <h6><i class="fas fa-info-circle me-2"></i>Detalle del Ingrediente</h6>
                        ${mensaje}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                
                const alertContainer = document.getElementById('alertContainer');
                alertContainer.insertAdjacentHTML('beforeend', alertHtml);
                
                // Auto-eliminar después de 8 segundos
                setTimeout(() => {
                    const alert = alertContainer.querySelector('.alert');
                    if (alert) {
                        alert.remove();
                    }
                }, 8000);
            }
        }
    </script>
</body>
</html>