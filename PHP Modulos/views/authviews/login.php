<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Autenticación JWT</title>
</head>
<body>
    <div>
        <h1>Iniciar Sesión</h1>
        
        <!-- Mostrar mensajes de error o éxito -->
        <?php if (!empty($mensaje)): ?>
            <div>
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulario de login -->
        <form method="POST" action="">
            <div>
                <label for="username">Usuario o Email:</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    placeholder="Ingrese su usuario o email"
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                >
            </div>
            
            <div>
                <label for="password">Contraseña:</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    placeholder="Ingrese su contraseña"
                >
            </div>
            
            <div>
                <button type="submit">Iniciar Sesión</button>
            </div>
        </form>
        
        <!-- Información adicional -->
        <div>
            <h3>Credenciales de prueba:</h3>
            <p><strong>Administrador:</strong> admin / password</p>
            <p><strong>Nota:</strong> Este sistema utiliza autenticación JWT del backend</p>
        </div>
        
        <!-- Enlaces de navegación -->
        <div>
            <a href="../../index.php">Volver al Menú Principal</a>
        </div>
    </div>
</body>
</html>