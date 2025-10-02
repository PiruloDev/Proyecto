<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Autenticación JWT</title>
</head>
<body>
    <h1>Sistema de Autenticación JWT - Panadería</h1>
    
    <div>
        <h3>Dashboards Disponibles por Rol:</h3>
        <ul>
            <li><strong>Administrador:</strong> Gestión completa del sistema</li>
            <li><strong>Empleado:</strong> Gestión de pedidos y producción</li>
            <li><strong>Cliente:</strong> Acceso al homepage con funciones de cliente</li>
        </ul>
    </div>
    
    <div>
        <h2>Iniciar Sesión</h2>
        <form method="POST" action="loginusers.php">
            <div>
                <label for="username">Usuario/Email:</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    placeholder="Ingrese su usuario o email"
                >
            </div>
            <br>
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
            <br>
            <div>
                <button type="submit">Iniciar Sesión</button>
            </div>
        </form>
    </div>
    
    <div>
        <h3>Estado del Sistema:</h3>
        <?php
        // Verificar si el backend está funcionando
        $backend_url = 'http://localhost:8080/auth/test';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $backend_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 200) {
            echo "<p style='color:green;'>✅ Backend JWT: Conectado</p>";
        } else {
            echo "<p style='color:red;'>❌ Backend JWT: Desconectado</p>";
        }
        ?>
    </div>
    
    <!-- Enlace al registro -->
    <div>
        <p>¿Aún no tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
    </div>
</body>
</html>