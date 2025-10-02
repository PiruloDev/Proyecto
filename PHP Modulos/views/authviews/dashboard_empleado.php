<?php
require_once __DIR__ . '/../../controllers/userscontroller/AuthController.php';

// Proteger la ruta - solo empleados pueden acceder
$authController = new AuthController();
$authController->protegerRuta(['empleado']); // Cambiar a minúsculas

// Obtener información del usuario autenticado
$authService = new AuthService();
session_start(); // Asegurar que la sesión esté iniciada
$nombreUsuario = $_SESSION['user_name'] ?? 'Empleado';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Empleado - El Castillo del Pan</title>
</head>
<body>
    <div>
        <header>
            <h1>Dashboard Empleado</h1>
            <p>Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?></p>
            <nav>
                <a href="../../controllers/userscontroller/AuthController.php?action=logout">Cerrar Sesión</a>
            </nav>
        </header>
        
        <main>
            <div>
                <h2>Panel de Control del Empleado</h2>
                <p>Desde aquí puedes acceder a las funciones operativas del sistema.</p>
            </div>
            
            <div>
                <h3>Módulos Operativos</h3>
                <ul>
                    <li>
                        <h4>Gestión de Productos</h4>
                        <p>Consulta y actualiza información de productos</p>
                        <a href="../../Productosindex.php">Ir a Gestión de Productos</a>
                    </li>
                    
                    <li>
                        <h4>Gestión de Pedidos</h4>
                        <p>Procesa pedidos de clientes</p>
                        <a href="../../Pedidosindex.php">Ir a Gestión de Pedidos</a>
                    </li>
                    
                    <li>
                        <h4>Control de Producción</h4>
                        <p>Gestiona la producción diaria</p>
                        <a href="../../Produccionindex.php">Ir a Control de Producción</a>
                    </li>
                    
                    <li>
                        <h4>Gestión de Ingredientes</h4>
                        <p>Consulta inventario de ingredientes</p>
                        <a href="../../Ingredienteindex.php">Ir a Gestión de Ingredientes</a>
                    </li>
                    
                    <li>
                        <h4>Gestión de Recetas</h4>
                        <p>Consulta las recetas de los productos</p>
                        <a href="../../Recetasindex.php">Ir a Gestión de Recetas</a>
                    </li>
                    
                    <li>
                        <h4>Reportes Operativos</h4>
                        <p>Consulta reportes operativos</p>
                        <a href="../../Reportesindex.php">Ir a Reportes</a>
                    </li>
                </ul>
            </div>
            
            <div>
                <h3>Información de la Sesión</h3>
                <ul>
                    <li>Rol: Empleado</li>
                    <li>Sesión iniciada: <?php echo date('d/m/Y H:i:s'); ?></li>
                    <li>Token de autenticación: Activo</li>
                </ul>
            </div>
            
            <div>
                <h3>Tareas Pendientes</h3>
                <p>Las funcionalidades específicas de cada módulo se implementarán en fases posteriores.</p>
                <ul>
                    <li>Verificar producción diaria</li>
                    <li>Procesar pedidos pendientes</li>
                    <li>Actualizar inventario</li>
                    <li>Generar reportes operativos</li>
                </ul>
            </div>
        </main>
        
        <footer>
            <p>© 2025 El Castillo del Pan - Sistema Operativo</p>
        </footer>
    </div>
</body>
</html>