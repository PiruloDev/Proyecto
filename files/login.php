<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styleslogin.css">
    <title>Iniciar Sesión - Panadería</title>
</head>
<body>
    <div class="login-container">
        <div class="logo animated-logo">
            <img src="../files/img/logoprincipal.jpg" alt="Logo Panadería" class="logo-img">
        </div>
        <h1>Iniciar Sesión</h1>
        
        <div id="errorMessage" class="error-message"></div>
        
        <?php if (isset($_GET['logout']) && $_GET['logout'] === 'success'): ?>
            <div class="success-message">
                Sesión cerrada correctamente. Gracias por usar nuestro sistema.
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success']) && $_GET['success'] === 'registered'): ?>
            <div class="success-message">
                Registro exitoso. Ya puede iniciar sesión con sus credenciales.
            </div>
        <?php endif; ?>
        
        <form id="loginForm" action="procesar_login.php" method="POST">
            <div class="form-group">
                <label for="tipoUsuario">Tipo de Usuario:</label>
                <select id="tipoUsuario" name="tipo_usuario" class="user-type-select" required onchange="updateUserInfo()">
                    <option value="">Seleccione el tipo de usuario</option>
                    <option value="admin"> Administrador</option>
                    <option value="empleado"> Empleado</option>
                    <option value="cliente"> Cliente</option>
                </select>
            </div>
            
            <div id="userInfo" class="user-type-info" style="display: none;">
                <h4 id="infoTitle"></h4>
                <p id="infoDescription"></p>
            </div>
            
            <div class="form-group">
                <label for="usuario" id="labelUsuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required placeholder="Ingrese su usuario">
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required placeholder="Ingrese su contraseña">
            </div>
            
            <button type="submit" class="btn">
                <span id="btnText">Iniciar Sesión</span>
            </button>
            
            <div id="loading" class="loading">
                <div class="loading-spinner"></div>
                <p>Validando credenciales...</p>
            </div>
        </form>
        
        <div class="footer-links">
            <a href="registro_cliente.php">¿No tienes cuenta?<br>Regístrate como cliente</br></a>
        </div>
    </div>
    
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btnText = document.getElementById('btnText');
            const loading = document.getElementById('loading');
            const errorMessage = document.getElementById('errorMessage');
            
            // Ocultar mensajes de error previos
            errorMessage.style.display = 'none';
            
            // Mostrar loading
            btnText.textContent = 'Validando...';
            loading.style.display = 'block';
            
            // Validar que se haya seleccionado un tipo de usuario
            const tipoUsuario = document.getElementById('tipoUsuario').value;
            if (!tipoUsuario) {
                e.preventDefault();
                showError('Por favor seleccione el tipo de usuario');
                resetButton();
                return;
            }
            
            // Validar campos
            const usuario = document.getElementById('usuario').value.trim();
            const password = document.getElementById('password').value;
            
            if (!usuario || !password) {
                e.preventDefault();
                showError('Por favor complete todos los campos');
                resetButton();
                return;
            }
            
            // Validar email para clientes
            if (tipoUsuario === 'cliente') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(usuario)) {
                    e.preventDefault();
                    showError('Por favor ingrese un email válido');
                    resetButton();
                    return;
                }
            }
        });
        
        function showError(message) {
            const errorMessage = document.getElementById('errorMessage');
            errorMessage.textContent = message;
            errorMessage.style.display = 'block';
        }
        
        function resetButton() {
            const btnText = document.getElementById('btnText');
            const loading = document.getElementById('loading');
            
            if (btnText) {
                btnText.textContent = 'Iniciar Sesión';
            }
            if (loading) {
                loading.style.display = 'none';
            }
        }
        
        // Resetear el formulario al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            resetButton();
        });
        
        // Verificar si hay error en la URL (después de redirección)
        window.onload = function() {
            // Asegurar que el loading esté oculto al cargar la página
            const loading = document.getElementById('loading');
            const btnText = document.getElementById('btnText');
            
            if (loading) {
                loading.style.display = 'none';
            }
            if (btnText) {
                btnText.textContent = 'Iniciar Sesión';
            }
            
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            
            if (error) {
                switch(error) {
                    case 'invalid_credentials':
                        showError('Usuario o contraseña incorrectos');
                        break;
                    case 'missing_data':
                        showError('Por favor complete todos los campos');
                        break;
                    case 'invalid_user_type':
                        showError('Tipo de usuario no válido');
                        break;
                    case 'database_error':
                        showError('Error en el servidor. Intente nuevamente');
                        break;
                    default:
                        showError('Error desconocido. Intente nuevamente');
                }
            }
        };
        function updateUserInfo() {
            const tipoUsuario = document.getElementById('tipoUsuario').value;
            const userInfo = document.getElementById('userInfo');
            const infoTitle = document.getElementById('infoTitle');
            const infoDescription = document.getElementById('infoDescription');
            const labelUsuario = document.getElementById('labelUsuario');
            const usuarioInput = document.getElementById('usuario');
        }
    </script>
</body>
</html>
