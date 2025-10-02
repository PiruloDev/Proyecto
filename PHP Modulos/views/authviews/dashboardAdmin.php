<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador - Sistema JWT</title>
</head>
<body>
    <?php
    require_once __DIR__ . '/../../controllers/authcontroller/AuthController.php';
    
    // Verificar acceso y obtener datos del usuario
    $authController = new AuthController();
    $authController->verificarAcceso();
    $usuario = $authController->obtenerUsuarioActual();
    ?>
    
    <div>
        <h1>Dashboard de Administrador</h1>
        
        <!-- Información del usuario autenticado -->
        <div>
            <h2>Bienvenido, <?php echo htmlspecialchars($usuario['nombre']); ?></h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
            <p><strong>Rol:</strong> <?php echo htmlspecialchars($usuario['rol']); ?></p>
            <p><strong>ID Usuario:</strong> <?php echo htmlspecialchars($usuario['id']); ?></p>
        </div>
        
        <!-- Funcionalidades específicas del administrador -->
        <div>
            <h3>Funciones de Administrador</h3>
            <ul>
                <li><a href="../../Userindex.php">Gestión de Usuarios</a></li>
                <li><a href="../../Empleadoindex.php">Gestión de Empleados</a></li>
                <li><a href="../../Productosindex.php">Gestión de Productos</a></li>
                <li><a href="../../CategoriaProductosindex.php">Categorías de Productos</a></li>
                <li><a href="../../Ingredienteindex.php">Gestión de Ingredientes</a></li>
                <li><a href="../../Pedidosindex.php">Gestión de Pedidos</a></li>
                <li><a href="../../Produccionindex.php">Control de Producción</a></li>
                <li><a href="../../Reportesindex.php">Reportes y Analytics</a></li>
            </ul>
        </div>
        
        <!-- Panel de estado del sistema -->
        <div>
            <h3>Estado del Sistema</h3>
            <p>✅ Autenticación JWT: Activa</p>
            <p>✅ Sesión: Válida</p>
            <p>✅ Permisos: Administrador</p>
        </div>
        
        <!-- Opciones de sesión -->
        <div>
            <h3>Opciones de Sesión</h3>
            <a href="?action=logout">Cerrar Sesión</a>
        </div>
    </div>
    
    <?php
    // Manejar cierre de sesión
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        $authController->cerrarSesion();
    }
    ?>
</body>
</html>