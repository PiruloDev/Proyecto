<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Panadería</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        
        .logo {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 30px;
            font-size: 28px;
        }
        
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
        }
        
        input[type="text"], input[type="password"], select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        input[type="text"]:focus, input[type="password"]:focus, select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 10px rgba(102, 126, 234, 0.1);
        }
        
        .user-type-select {
            background: #f8f9fa;
            border: 2px solid #ddd;
            padding: 12px;
            border-radius: 8px;
            font-size: 16px;
            color: #333;
        }
        
        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: block;
            text-align: center;
            font-weight: 500;
        }
        
        .footer-links {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .footer-links a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
        }
        
        .user-type-info {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #6c757d;
        }
        
        .user-type-info h4 {
            margin-bottom: 10px;
            color: #495057;
        }
        
        .loading {
            display: none;
            margin-top: 10px;
        }
        
        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">🥖</div>
        <h1>Iniciar Sesión</h1>
        
        <div id="errorMessage" class="error-message"></div>
        
        <?php if (isset($_GET['logout']) && $_GET['logout'] === 'success'): ?>
            <div class="success-message">
                ✅ Sesión cerrada correctamente. Gracias por usar nuestro sistema.
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success']) && $_GET['success'] === 'registered'): ?>
            <div class="success-message">
                ✅ Registro exitoso. Ya puede iniciar sesión con sus credenciales.
            </div>
        <?php endif; ?>
        
        <form id="loginForm" action="procesar_login.php" method="POST">
            <div class="form-group">
                <label for="tipoUsuario">Tipo de Usuario:</label>
                <select id="tipoUsuario" name="tipo_usuario" class="user-type-select" required onchange="updateUserInfo()">
                    <option value="">Seleccione el tipo de usuario</option>
                    <option value="admin">👑 Administrador</option>
                    <option value="empleado">👨‍💼 Empleado</option>
                    <option value="cliente">👤 Cliente</option>
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
            <a href="registro_cliente.php">¿No tienes cuenta? Regístrate como cliente</a>
        </div>
    </div>
    
    <script>
        function updateUserInfo() {
            const tipoUsuario = document.getElementById('tipoUsuario').value;
            const userInfo = document.getElementById('userInfo');
            const infoTitle = document.getElementById('infoTitle');
            const infoDescription = document.getElementById('infoDescription');
            const labelUsuario = document.getElementById('labelUsuario');
            const inputUsuario = document.getElementById('usuario');
            
            if (tipoUsuario) {
                userInfo.style.display = 'block';
                
                switch(tipoUsuario) {
                    case 'admin':
                        infoTitle.textContent = '👑 Acceso de Administrador';
                        infoDescription.textContent = 'Acceso completo al sistema: gestión de productos, empleados, reportes y configuración.';
                        labelUsuario.textContent = 'Nombre de Usuario:';
                        inputUsuario.placeholder = 'Ingrese su nombre de usuario';
                        break;
                        
                    case 'empleado':
                        infoTitle.textContent = '👨‍💼 Acceso de Empleado';
                        infoDescription.textContent = 'Acceso a gestión de pedidos, inventario y atención al cliente.';
                        labelUsuario.textContent = 'Nombre de Usuario:';
                        inputUsuario.placeholder = 'Ingrese su nombre de usuario';
                        break;
                        
                    case 'cliente':
                        infoTitle.textContent = '👤 Acceso de Cliente';
                        infoDescription.textContent = 'Acceso a realizar pedidos, ver historial y gestionar perfil.';
                        labelUsuario.textContent = 'Email:';
                        inputUsuario.placeholder = 'Ingrese su email';
                        inputUsuario.type = 'email';
                        break;
                }
            } else {
                userInfo.style.display = 'none';
                inputUsuario.type = 'text';
                labelUsuario.textContent = 'Usuario:';
                inputUsuario.placeholder = 'Ingrese su usuario';
            }
        }
        
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
            document.getElementById('btnText').textContent = 'Iniciar Sesión';
            document.getElementById('loading').style.display = 'none';
        }
        
        // Verificar si hay error en la URL (después de redirección)
        window.onload = function() {
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
    </script>
</body>
</html>
