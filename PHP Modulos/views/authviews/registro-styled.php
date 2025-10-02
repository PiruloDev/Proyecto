<?php
// Procesar formulario de registro
$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../controllers/authcontroller/RegistroController.php';
    
    $controller = new RegistroController();
    $resultado = $controller->procesarRegistro();
    
    $mensaje = $resultado['mensaje'];
    $tipoMensaje = $resultado['tipo'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cliente - El Castillo del Pan</title>
    <link rel="icon" type="image/x-icon" href="../../images/logoprincipal.jpg">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Animate CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../../css/auth-styles.css">
</head>
<body class="auth-body">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="auth-container">
                    <!-- Logo y título -->
                    <div class="text-center mb-4 animate-fade-in">
                        <img src="../../images/logoprincipal.jpg" width="80" alt="Logo El Castillo del Pan" 
                             class="rounded-circle border border-3 border-warning p-2 bg-white shadow mb-3">
                        <h1 class="form-title">
                            <i class="bi bi-person-plus me-2"></i>
                            Registro de Cliente
                        </h1>
                    </div>
                    
                    <!-- Mensajes de estado -->
                    <?php if (!empty($mensaje)): ?>
                        <div class="alert <?php echo $tipoMensaje === 'error' ? 'alert-danger' : 'alert-success'; ?> animate-fade-in" role="alert">
                            <i class="bi <?php echo $tipoMensaje === 'error' ? 'bi-exclamation-triangle' : 'bi-check-circle'; ?> me-2"></i>
                            <?php echo htmlspecialchars($mensaje); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Información de registro -->
                    <div class="auth-info animate-fade-in">
                        <h5><i class="bi bi-info-circle me-2"></i>Crea tu cuenta de cliente</h5>
                        <p class="mb-0">Regístrate para acceder a funciones exclusivas del sistema.</p>
                    </div>
                    
                    <!-- Formulario de registro -->
                    <div class="glass-form animate-fade-in">
                        <form method="POST" action="" id="registroForm">
                            <!-- Campo de nombre -->
                            <div class="form-floating mb-3">
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="nombre" 
                                    name="nombre" 
                                    placeholder="Nombre Completo"
                                    required 
                                    value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>"
                                >
                                <label for="nombre">
                                    <i class="bi bi-person me-2"></i>Nombre Completo
                                </label>
                            </div>
                            
                            <!-- Campo de email -->
                            <div class="form-floating mb-3">
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="email" 
                                    name="email" 
                                    placeholder="Correo Electrónico"
                                    required 
                                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                >
                                <label for="email">
                                    <i class="bi bi-envelope me-2"></i>Correo Electrónico
                                </label>
                            </div>
                            
                            <!-- Campo de teléfono -->
                            <div class="form-floating mb-3">
                                <input 
                                    type="tel" 
                                    class="form-control" 
                                    id="telefono" 
                                    name="telefono" 
                                    placeholder="Número de Teléfono"
                                    required 
                                    pattern="[0-9]{10}"
                                    title="Ingrese un número de teléfono de 10 dígitos"
                                    value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>"
                                >
                                <label for="telefono">
                                    <i class="bi bi-phone me-2"></i>Número de Teléfono
                                </label>
                            </div>
                            
                            <!-- Campo de contraseña -->
                            <div class="form-floating mb-3">
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="contrasena" 
                                    name="contrasena" 
                                    placeholder="Contraseña"
                                    required 
                                    minlength="6"
                                    title="La contraseña debe tener al menos 6 caracteres"
                                >
                                <label for="contrasena">
                                    <i class="bi bi-lock me-2"></i>Contraseña
                                </label>
                            </div>
                            
                            <!-- Campo de confirmar contraseña -->
                            <div class="form-floating mb-4">
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="confirmar_contrasena" 
                                    name="confirmar_contrasena" 
                                    placeholder="Confirmar Contraseña"
                                    required 
                                    minlength="6"
                                >
                                <label for="confirmar_contrasena">
                                    <i class="bi bi-lock-fill me-2"></i>Confirmar Contraseña
                                </label>
                            </div>
                            
                            <!-- Mensaje de validación de contraseñas -->
                            <div id="password-match-message" class="mb-3" style="display: none;"></div>
                            
                            <!-- Botón de registro -->
                            <button type="submit" class="btn btn-auth-primary mb-3" id="registroBtn">
                                <i class="bi bi-person-plus me-2"></i>
                                Crear Cuenta
                            </button>
                        </form>
                        
                        <!-- Enlaces adicionales -->
                        <div class="text-center">
                            <p class="mb-0 text-muted">¿Ya tienes una cuenta?</p>
                            <a href="../../loginusuarios.php" class="auth-link">
                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                Iniciar Sesión
                            </a>
                        </div>
                    </div>
                    
                    <!-- Enlace de regreso -->
                    <div class="text-center mt-4 animate-fade-in">
                        <a href="../../templates/Homepage.php" class="auth-link">
                            <i class="bi bi-arrow-left me-2"></i>
                            Volver al inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script para validación y efectos -->
    <script>
        // Validación de contraseñas en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('contrasena');
            const confirmPassword = document.getElementById('confirmar_contrasena');
            const message = document.getElementById('password-match-message');
            const submitBtn = document.getElementById('registroBtn');
            
            function validatePasswords() {
                if (password.value && confirmPassword.value) {
                    if (password.value === confirmPassword.value) {
                        message.className = 'alert alert-success small';
                        message.innerHTML = '<i class="bi bi-check-circle me-1"></i>Las contraseñas coinciden';
                        message.style.display = 'block';
                        submitBtn.disabled = false;
                    } else {
                        message.className = 'alert alert-danger small';
                        message.innerHTML = '<i class="bi bi-x-circle me-1"></i>Las contraseñas no coinciden';
                        message.style.display = 'block';
                        submitBtn.disabled = true;
                    }
                } else {
                    message.style.display = 'none';
                    submitBtn.disabled = false;
                }
            }
            
            password.addEventListener('input', validatePasswords);
            confirmPassword.addEventListener('input', validatePasswords);
            
            // Efecto de carga en envío
            document.getElementById('registroForm').addEventListener('submit', function() {
                if (!submitBtn.disabled) {
                    submitBtn.classList.add('btn-loading');
                    submitBtn.disabled = true;
                }
            });
            
            // Animaciones al cargar la página
            const elements = document.querySelectorAll('.animate-fade-in');
            elements.forEach((el, index) => {
                setTimeout(() => {
                    el.style.animationDelay = (index * 0.2) + 's';
                }, 100);
            });
            
            // Validación de teléfono
            document.getElementById('telefono').addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            });
        });
    </script>
</body>
</html>