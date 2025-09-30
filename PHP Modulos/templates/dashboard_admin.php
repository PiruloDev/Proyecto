
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="stylesheet" href="../css/styleadmindst.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <title>Dashboard Administrador - El Castillo del Pan </title>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <a href="homepage.php" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
                    <img src="../images/logoprincipal.jpg" alt="Logo" class="logo-img">
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
    <!-- Ver Ingredientes y Proveedores -->
    <a href="Ingredienteindex.php?modulo=ingredientes&accion=listar" class="action-btn">
        <i class="fas fa-list-ul me-2"></i>Ver Ingredientes y Proveedores
    </a>

    <!-- Agregar Ingrediente -->
    <a href="Ingredienteindex.php?modulo=ingredientes&accion=agregar" class="action-btn">
        <i class="fas fa-plus me-2"></i>Agregar Ingrediente
    </a>

    <!-- Gestionar Proveedores -->
    <a href="Ingredienteindex.php?modulo=proveedores&accion=listar" class="action-btn">
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

    
</body>
</html>
