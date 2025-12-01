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
                
                // Mostrar aviso emergente antes de redirigir
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        mostrarAvisoExitoso();
                    });
                </script>";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styleregister.css">
    <title>Registro de Cliente - Panadería</title>
</head>
<body>
    <div class="registro-container">
        <!-- Formulario de registro -->
        <div class="registro-left">
            <div class="logo-registro">
                <img src="../files/img/logoprincipal.jpg" alt="Logo Panadería" class="logo-img-registro">
            </div>
            <h1 class="titulo-registro">Registro Cliente</h1>
        </div>
        
        <div class="registro-right">
            <?php if (!empty($errores)): ?>
                <div class="error-message-registro" style="display: block;">
                    <strong>Por favor corrige los siguientes errores:</strong>
                    <ul style="margin: 10px 0; padding-left: 20px;">
                        <?php foreach ($errores as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="form-group-registro">
                        <label for="nombre" class="label-registro">Nombre Completo <span style="color: #F44336;">*</span></label>
                        <input type="text" 
                               id="nombre" 
                               name="nombre" 
                               class="input-registro"
                               value="<?php echo isset($nombre) ? htmlspecialchars($nombre) : ''; ?>"
                               required
                               maxlength="100"
                               placeholder="Ingresa tu nombre completo">
                    </div>
                    
                    <div class="form-group-registro">
                        <label for="email" class="label-registro">Correo Electrónico <span style="color: #F44336;">*</span></label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="input-registro"
                               value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                               required
                               maxlength="150"
                               placeholder="ejemplo@correo.com">
                    </div>
                    
                    <div class="form-group-registro">
                        <label for="telefono" class="label-registro">Teléfono (opcional)</label>
                        <input type="text" 
                               id="telefono" 
                               name="telefono" 
                               class="input-registro"
                               value="<?php echo isset($telefono) ? htmlspecialchars($telefono) : ''; ?>"
                               maxlength="20"
                               placeholder="Ej: +52 123 456 7890">
                    </div>
                    
                    <div class="form-group-registro">
                        <label for="password" class="label-registro">Contraseña <span style="color: #F44336;">*</span></label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="input-registro"
                               required
                               minlength="6"
                               maxlength="255"
                               placeholder="Mínimo 6 caracteres">
                        <small style="color: var(--text-light); font-size: 12px; margin-top: 5px; display: block;">Mínimo 6 caracteres</small>
                    </div>
                    
                    <div class="form-group-registro">
                        <label for="confirm_password" class="label-registro">Confirmar Contraseña <span style="color: #F44336;">*</span></label>
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               class="input-registro"
                               required
                               minlength="6"
                               maxlength="255"
                               placeholder="Repite tu contraseña">
                    </div>
                    
                    <button type="submit" class="btn-registro">Registrarme</button>
                </form>
                
                <div class="footer-links-registro">
                    ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
                </div>
            </div>
        </div>
        
        <!-- Modal de registro exitoso -->
        <div id="modalExitoso" class="modal-overlay" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="check-icon">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="12" fill="#28a745"/>
                            <path d="m9 12 2 2 4-4" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h2>¡Registro Exitoso!</h2>
                </div>
                <div class="modal-body">
                    <p>Tu cuenta ha sido creada exitosamente.</p>
                    <p>Ahora puedes iniciar sesión con tus credenciales.</p>
                </div>
                <div class="modal-footer">
                    <button onclick="irAlLogin()" class="btn-modal">Ir al Login</button>
                </div>
            </div>
        </div>
    
    <script>
        // Función para mostrar el aviso de registro exitoso
        function mostrarAvisoExitoso() {
            const modal = document.getElementById('modalExitoso');
            modal.style.display = 'flex';
            
            // Auto-cerrar después de 5 segundos si el usuario no hace click
            setTimeout(function() {
                irAlLogin();
            }, 5000);
        }
        
        // Función para ir al login
        function irAlLogin() {
            window.location.href = 'login.php?success=registered';
        }
        
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
