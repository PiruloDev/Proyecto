<?php
// Procesar el formulario si se envió
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'conexion.php';
    
    // Obtener y limpiar los datos del formulario
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Array para almacenar errores
    $errores = [];
    
    // Validaciones
    if (empty($nombre)) {
        $errores[] = "El nombre es obligatorio.";
    } elseif (strlen($nombre) < 2) {
        $errores[] = "El nombre debe tener al menos 2 caracteres.";
    } elseif (strlen($nombre) > 100) {
        $errores[] = "El nombre no puede tener más de 100 caracteres.";
    }
    
    if (empty($email)) {
        $errores[] = "El email es obligatorio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del email no es válido.";
    } elseif (strlen($email) > 150) {
        $errores[] = "El email no puede tener más de 150 caracteres.";
    }
    
    if (!empty($telefono)) {
        // El teléfono es opcional, pero si se proporciona debe ser válido
        if (!preg_match('/^[0-9+\-\s\(\)]{7,20}$/', $telefono)) {
            $errores[] = "El formato del teléfono no es válido.";
        }
    }
    
    if (empty($password)) {
        $errores[] = "La contraseña es obligatoria.";
    } elseif (strlen($password) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    } elseif (strlen($password) > 255) {
        $errores[] = "La contraseña no puede tener más de 255 caracteres.";
    }
    
    if ($password !== $confirm_password) {
        $errores[] = "Las contraseñas no coinciden.";
    }
    
    // Si no hay errores de validación, verificar si ya existe el email o nombre
    if (empty($errores)) {
        try {
            // Verificar si ya existe un cliente con ese email
            $stmt = $pdo_conexion->prepare("SELECT ID_CLIENTE FROM Clientes WHERE EMAIL_CLI = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $errores[] = "Ya existe un cliente registrado con ese email.";
            }
            
            // Verificar si ya existe un cliente con ese nombre
            $stmt = $pdo_conexion->prepare("SELECT ID_CLIENTE FROM Clientes WHERE NOMBRE_CLI = ?");
            $stmt->execute([$nombre]);
            
            if ($stmt->rowCount() > 0) {
                $errores[] = "Ya existe un cliente registrado con ese nombre.";
            }
            
        } catch (PDOException $e) {
            $errores[] = "Error al verificar los datos: " . $e->getMessage();
        }
    }
    
    // Si no hay errores, proceder con el registro
    if (empty($errores)) {
        try {
            // Generar salt aleatorio
            $salt = bin2hex(random_bytes(16));
            
            // Hashear la contraseña con salt
            $password_hash = hash('sha256', $password . $salt);
            
            // Insertar el nuevo cliente
            $stmt = $pdo_conexion->prepare("
                INSERT INTO Clientes (NOMBRE_CLI, EMAIL_CLI, TELEFONO_CLI, CONTRASEÑA_CLI, SALT_CLI, ACTIVO_CLI) 
                VALUES (?, ?, ?, ?, ?, 1)
            ");
            
            $resultado = $stmt->execute([
                $nombre,
                $email,
                $telefono,
                $password_hash,
                $salt
            ]);
            
            if ($resultado) {
                $cliente_id = $pdo_conexion->lastInsertId();
                $registro_exitoso = true;
            } else {
                $errores[] = "Error al registrar el cliente.";
            }
            
        } catch (PDOException $e) {
            $errores[] = "Error en la base de datos: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cliente - Panadería</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        
        .logo {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 30px;
            font-size: 24px;
        }
        
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        
        .required {
            color: #e74c3c;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: left;
        }
        
        .error-message ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .success-icon {
            font-size: 48px;
            color: #28a745;
            margin-bottom: 15px;
        }
        
        .cliente-info {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }
        
        .cliente-info h4 {
            margin-bottom: 10px;
            color: #495057;
        }
        
        .cliente-info p {
            margin: 5px 0;
            color: #6c757d;
        }
        
        .login-link {
            margin-top: 20px;
            text-align: center;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .password-help {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($registro_exitoso) && $registro_exitoso): ?>
            <!-- Mensaje de éxito -->
            <div class="success-icon">✓</div>
            <h1>¡Registro Exitoso!</h1>
            
            <div class="success-message">
                Tu cuenta ha sido creada exitosamente. Ya puedes iniciar sesión con tus credenciales.
            </div>
            
            <div class="cliente-info">
                <h4>Datos registrados:</h4>
                <p><strong>ID de Cliente:</strong> <?php echo htmlspecialchars($cliente_id); ?></p>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombre); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                <?php if (!empty($telefono)): ?>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($telefono); ?></p>
                <?php endif; ?>
                <p><strong>Estado:</strong> <span style="color: #28a745;">Activo</span></p>
            </div>
            
            <div class="login-link">
                <a href="login.php" class="btn">Iniciar Sesión</a>
            </div>
            
        <?php else: ?>
            <!-- Formulario de registro -->
            <div class="logo">🥖</div>
            <h1>Registro de Cliente</h1>
            
            <?php if (!empty($errores)): ?>
                <div class="error-message">
                    <strong>Por favor corrige los siguientes errores:</strong>
                    <ul>
                        <?php foreach ($errores as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="form-group">
                    <label for="nombre">Nombre Completo <span class="required">*</span></label>
                    <input type="text" 
                           id="nombre" 
                           name="nombre" 
                           value="<?php echo isset($nombre) ? htmlspecialchars($nombre) : ''; ?>"
                           required
                           maxlength="100"
                           placeholder="Ingresa tu nombre completo">
                </div>
                
                <div class="form-group">
                    <label for="email">Correo Electrónico <span class="required">*</span></label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                           required
                           maxlength="150"
                           placeholder="ejemplo@correo.com">
                </div>
                
                <div class="form-group">
                    <label for="telefono">Teléfono (opcional)</label>
                    <input type="tel" 
                           id="telefono" 
                           name="telefono" 
                           value="<?php echo isset($telefono) ? htmlspecialchars($telefono) : ''; ?>"
                           maxlength="20"
                           placeholder="Ej: +52 123 456 7890">
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña <span class="required">*</span></label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required
                           minlength="6"
                           maxlength="255"
                           placeholder="Mínimo 6 caracteres">
                    <div class="password-help">Mínimo 6 caracteres</div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña <span class="required">*</span></label>
                    <input type="password" 
                           id="confirm_password" 
                           name="confirm_password" 
                           required
                           minlength="6"
                           maxlength="255"
                           placeholder="Repite tu contraseña">
                </div>
                
                <button type="submit" class="btn">Registrarme</button>
            </form>
            
            <div class="login-link">
                ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Validación en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            
            function validatePasswords() {
                if (password.value && confirmPassword.value) {
                    if (password.value !== confirmPassword.value) {
                        confirmPassword.setCustomValidity('Las contraseñas no coinciden');
                    } else {
                        confirmPassword.setCustomValidity('');
                    }
                }
            }
            
            password.addEventListener('input', validatePasswords);
            confirmPassword.addEventListener('input', validatePasswords);
            
            // Validación del email
            const email = document.getElementById('email');
            email.addEventListener('input', function() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (this.value && !emailRegex.test(this.value)) {
                    this.setCustomValidity('Por favor ingresa un email válido');
                } else {
                    this.setCustomValidity('');
                }
            });
            
            // Validación del teléfono
            const telefono = document.getElementById('telefono');
            telefono.addEventListener('input', function() {
                if (this.value) {
                    const telefonoRegex = /^[0-9+\-\s\(\)]{7,20}$/;
                    if (!telefonoRegex.test(this.value)) {
                        this.setCustomValidity('Formato de teléfono no válido');
                    } else {
                        this.setCustomValidity('');
                    }
                }
            });
        });
    </script>
</body>
</html>
