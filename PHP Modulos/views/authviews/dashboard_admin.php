<?php
require_once __DIR__ . '/../../controllers/userscontroller/AuthController.php';

// Proteger la ruta - solo administradores pueden acceder
$authController = new AuthController();
$authController->protegerRuta(['administrador']); // Cambiar a minúsculas

// Obtener información del usuario autenticado
$authService = new AuthService();
session_start(); // Asegurar que la sesión esté iniciada
$nombreUsuario = $_SESSION['user_name'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador - El Castillo del Pan</title>
</head>
<body>
    <div>
        <header>
            <h1>Dashboard Administrador</h1>
            <p>Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?></p>
            <nav>
                <a href="../../controllers/userscontroller/AuthController.php?action=logout">Cerrar Sesión</a>
            </nav>
        </header>
        
        <main>
            <div>
                <h2>Panel de Control Administrativo</h2>
                <p>Desde aquí puedes gestionar todas las funciones administrativas del sistema.</p>
            </div>
            
            <div>
                <h3>Módulos Administrativos</h3>
                <ul>
                    <li>
                        <h4>Gestión de Usuarios</h4>
                        <p>Administra empleados y clientes del sistema</p>
                        <a href="../../Userindex.php">Ir a Gestión de Usuarios</a>
                    </li>
                    
                    <li>
                        <h4>Gestión de Productos</h4>
                        <p>Administra el catálogo de productos de la panadería</p>
                        <a href="../../Productosindex.php">Ir a Gestión de Productos</a>
                    </li>
                    
                    <li>
                        <h4>Gestión de Ingredientes</h4>
                        <p>Administra ingredientes y proveedores</p>
                        <a href="../../Ingredienteindex.php">Ir a Gestión de Ingredientes</a>
                    </li>
                    
                    <li>
                        <h4>Gestión de Pedidos</h4>
                        <p>Administra pedidos de clientes y proveedores</p>
                        <a href="../../Pedidosindex.php">Ir a Gestión de Pedidos</a>
                    </li>
                    
                    <li>
                        <h4>Control de Producción</h4>
                        <p>Supervisa la producción diaria</p>
                        <a href="../../Produccionindex.php">Ir a Control de Producción</a>
                    </li>
                    
                    <li>
                        <h4>Reportes</h4>
                        <p>Genera y consulta reportes del sistema</p>
                        <a href="../../Reportesindex.php">Ir a Reportes</a>
                    </li>
                    
                    <li>
                        <h4>Gestión de Recetas</h4>
                        <p>Administra las recetas de los productos</p>
                        <a href="../../Recetasindex.php">Ir a Gestión de Recetas</a>
                    </li>
                </ul>
            </div>
            
            <div>
                <h3>Información del Sistema</h3>
                <ul>
                    <li>Rol: Administrador</li>
                    <li>Sesión iniciada: <?php echo date('d/m/Y H:i:s'); ?></li>
                    <li>Token de autenticación: Activo</li>
                </ul>
            </div>
        </main>
        
        <footer>
            <p>© 2025 El Castillo del Pan - Sistema de Gestión Administrativa</p>
        </footer>
    </div>
</body>
</html>