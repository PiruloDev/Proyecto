<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cliente - El Castillo del Pan</title>
</head>
<body>
    <div>
        <h1>Registro de Cliente</h1>
        
        <?php if (!empty($mensaje)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div>
                <label for="nombre">Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            
            <div>
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div>
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required>
            </div>
            
            <div>
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            
            <div>
                <button type="submit">Registrarse</button>
            </div>
        </form>
        
        <div>
            <p>
                <a href="../../controllers/userscontroller/AuthController.php?action=login">
                    ¿Ya tienes cuenta? Inicia sesión aquí
                </a>
            </p>
        </div>
        
        <div>
            <a href="../homepage.php">Volver al inicio</a>
        </div>
    </div>
</body>
</html>