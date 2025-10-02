<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - El Castillo del Pan</title>
</head>
<body>
    <div>
        <h1>Iniciar Sesión</h1>
        
        <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'registro_exitoso'): ?>
            <p style="color: green;">
                <?php 
                if (isset($_GET['detalle'])) {
                    echo htmlspecialchars(urldecode($_GET['detalle']));
                } else {
                    echo "Registro exitoso. Ahora puedes iniciar sesión con tus credenciales.";
                }
                ?>
            </p>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <?php if ($_GET['error'] === 'no_autenticado'): ?>
                <p style="color: red;">Debes iniciar sesión para acceder a esta sección.</p>
            <?php elseif ($_GET['error'] === 'sin_permisos'): ?>
                <p style="color: red;">No tienes permisos para acceder a esta sección.</p>
            <?php elseif ($_GET['error'] === 'rol_invalido'): ?>
                <p style="color: red;">
                    Rol de usuario no válido.
                    <?php if (isset($_GET['rol_recibido'])): ?>
                        <br>Rol recibido: "<?php echo htmlspecialchars($_GET['rol_recibido']); ?>"
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if (!empty($mensaje)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div>
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div>
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            
            <div>
                <button type="submit">Iniciar Sesión</button>
            </div>
        </form>
        
        <div>
            <p>
                <a href="../../controllers/userscontroller/AuthController.php?action=registro">
                    ¿Aun no tienes Cuenta?, Registrate aquí
                </a>
            </p>
        </div>
        
        <div>
            <a href="../homepage.php">Volver al inicio</a>
        </div>
    </div>
</body>
</html>