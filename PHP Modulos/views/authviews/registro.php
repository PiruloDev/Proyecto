<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cliente - Sistema JWT</title>
</head>
<body>
    <div>
        <h1>Registro de Cliente</h1>
        
        <!-- Mostrar mensajes de error o éxito -->
        <?php if (!empty($mensaje)): ?>
            <div>
                <p style="color: <?php echo $tipoMensaje === 'error' ? 'red' : 'green'; ?>;">
                    <?php echo htmlspecialchars($mensaje); ?>
                </p>
            </div>
        <?php endif; ?>
        
        <!-- Formulario de registro -->
        <form method="POST" action="">
            <div>
                <label for="nombre">Nombre Completo:</label>
                <input 
                    type="text" 
                    id="nombre" 
                    name="nombre" 
                    required 
                    placeholder="Ingrese su nombre completo"
                    value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>"
                >
            </div>
            <br>
            
            <div>
                <label for="email">Correo Electrónico:</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required 
                    placeholder="ejemplo@correo.com"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                >
            </div>
            <br>
            
            <div>
                <label for="telefono">Número de Teléfono:</label>
                <input 
                    type="text" 
                    id="telefono" 
                    name="telefono" 
                    required 
                    placeholder="123-456-7890"
                    value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>"
                >
            </div>
            <br>
            
            <div>
                <label for="contrasena">Contraseña:</label>
                <input 
                    type="password" 
                    id="contrasena" 
                    name="contrasena" 
                    required 
                    placeholder="Mínimo 6 caracteres"
                >
            </div>
            <br>
            
            <div>
                <label for="confirmar_contrasena">Confirmar Contraseña:</label>
                <input 
                    type="password" 
                    id="confirmar_contrasena" 
                    name="confirmar_contrasena" 
                    required 
                    placeholder="Repita su contraseña"
                >
            </div>
            <br>
            
            <div>
                <button type="submit">Registrarse</button>
            </div>
        </form>
        
        <!-- Información adicional -->
        <div>
            <h3>Información del Registro:</h3>
            <ul>
                <li>Todos los campos son obligatorios</li>
                <li>El email debe tener un formato válido</li>
                <li>La contraseña debe tener al menos 6 caracteres</li>
                <li>El teléfono debe contener solo números y caracteres válidos</li>
            </ul>
        </div>
        
        <!-- Enlaces de navegación -->
        <div>
            <p><a href="../../loginusers.php">← Volver al Login</a></p>
            <p><a href="../../index.php">Ir al Menú Principal</a></p>
        </div>
    </div>
</body>
</html>