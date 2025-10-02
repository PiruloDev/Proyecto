<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Castillo del Pan - Inicio</title>
</head>
<body>
    <div>
        <header>
            <h1>El Castillo del Pan</h1>
            <nav>
                <a href="../controllers/userscontroller/AuthController.php?action=login">Iniciar Sesión</a>
                <a href="../controllers/userscontroller/AuthController.php?action=registro">Registrarse</a>
                <a href="../controllers/userscontroller/AuthController.php?action=logout">Cerrar Sesión</a>
            </nav>
        </header>
        
        <main>
            <h2>Bienvenido a nuestra panadería</h2>
            <p>Esta es la página principal donde los clientes pueden navegar por nuestros productos.</p>
            
            <?php
            session_start();
            if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
                echo "<p style='color: green;'>¡Bienvenido, " . htmlspecialchars($_SESSION['user_name'] ?? 'Cliente') . "!</p>";
                echo "<p>Rol: " . htmlspecialchars($_SESSION['user_role'] ?? 'N/A') . "</p>";
            } else {
                echo "<p>Para acceder a funciones especiales, por favor inicia sesión.</p>";
            }
            ?>
            
            <div>
                <h3>Nuestros Productos</h3>
                <p>Aquí se mostrarán los productos de la panadería (funcionalidad a implementar).</p>
            </div>
        </main>
        
        <footer>
            <p>© 2025 El Castillo del Pan</p>
        </footer>
    </div>
</body>
</html>