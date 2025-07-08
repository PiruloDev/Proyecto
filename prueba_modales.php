<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧪 Prueba de Modales - Sistema de Panadería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background-color: #f8f9fa; }
        .test-container { max-width: 1200px; margin: 0 auto; }
        .test-card { background: white; border-radius: 10px; padding: 20px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn-test { margin: 5px; }
        .result-box { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { color: #198754; }
        .error { color: #dc3545; }
    </style>
</head>
<body>
    <div class="test-container">
        <h1><i class="fas fa-flask me-2"></i>Prueba de Funcionalidades AJAX</h1>
        
        <div class="test-card">
            <h3><i class="fas fa-users me-2"></i>Prueba de Empleados</h3>
            <button class="btn btn-primary btn-test" onclick="probarEmpleados()">
                <i class="fas fa-eye me-2"></i>Cargar Empleados
            </button>
            <button class="btn btn-success btn-test" onclick="probarToggleEmpleado(1, 0)">
                <i class="fas fa-toggle-off me-2"></i>Desactivar Empleado ID 1
            </button>
            <button class="btn btn-warning btn-test" onclick="probarToggleEmpleado(1, 1)">
                <i class="fas fa-toggle-on me-2"></i>Activar Empleado ID 1
            </button>
            <div id="resultadoEmpleados" class="result-box"></div>
        </div>
        
        <div class="test-card">
            <h3><i class="fas fa-user-friends me-2"></i>Prueba de Clientes</h3>
            <button class="btn btn-primary btn-test" onclick="probarClientes()">
                <i class="fas fa-eye me-2"></i>Cargar Clientes
            </button>
            <button class="btn btn-success btn-test" onclick="probarToggleCliente(1, 0)">
                <i class="fas fa-toggle-off me-2"></i>Desactivar Cliente ID 1
            </button>
            <button class="btn btn-warning btn-test" onclick="probarToggleCliente(1, 1)">
                <i class="fas fa-toggle-on me-2"></i>Activar Cliente ID 1
            </button>
            <div id="resultadoClientes" class="result-box"></div>
        </div>
        
        <div class="test-card">
            <h3><i class="fas fa-user-plus me-2"></i>Prueba de Agregar Empleado</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="testNombre" value="Empleado Prueba">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="testEmail" value="prueba@test.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="testPassword" value="123456">
                    </div>
                    <button class="btn btn-success" onclick="probarAgregarEmpleado()">
                        <i class="fas fa-save me-2"></i>Agregar Empleado de Prueba
                    </button>
                </div>
            </div>
            <div id="resultadoAgregar" class="result-box"></div>
        </div>
        
        <div class="test-card">
            <h3><i class="fas fa-link me-2"></i>Enlaces Directos</h3>
            <a href="dashboard_admin.php" class="btn btn-primary">
                <i class="fas fa-tachometer-alt me-2"></i>Ir al Dashboard
            </a>
            <a href="login.php" class="btn btn-secondary">
                <i class="fas fa-sign-in-alt me-2"></i>Página de Login
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function mostrarResultado(elementId, mensaje, tipo = 'info') {
            const elemento = document.getElementById(elementId);
            const timestamp = new Date().toLocaleTimeString();
            const clase = tipo === 'success' ? 'success' : tipo === 'error' ? 'error' : '';
            elemento.innerHTML += `<div class="${clase}">[${timestamp}] ${mensaje}</div>`;
            elemento.scrollTop = elemento.scrollHeight;
        }
        
        function probarEmpleados() {
            mostrarResultado('resultadoEmpleados', 'Cargando empleados...', 'info');
            
            fetch('obtener_empleados_ajax.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarResultado('resultadoEmpleados', `✅ Empleados cargados: ${data.empleados.length} encontrados`, 'success');
                        data.empleados.forEach(emp => {
                            const estado = emp.activo ? 'Activo' : 'Inactivo';
                            mostrarResultado('resultadoEmpleados', `- ID: ${emp.id}, Nombre: ${emp.nombre}, Email: ${emp.email || 'N/A'}, Estado: ${estado}`, 'info');
                        });
                    } else {
                        mostrarResultado('resultadoEmpleados', `❌ Error: ${data.mensaje}`, 'error');
                    }
                })
                .catch(error => {
                    mostrarResultado('resultadoEmpleados', `❌ Error de conexión: ${error}`, 'error');
                });
        }
        
        function probarToggleEmpleado(id, estado) {
            const accion = estado === 1 ? 'activar' : 'desactivar';
            mostrarResultado('resultadoEmpleados', `Intentando ${accion} empleado ID ${id}...`, 'info');
            
            fetch('toggle_estado_empleado.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: id,
                    estado: estado
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarResultado('resultadoEmpleados', `✅ ${data.mensaje}`, 'success');
                } else {
                    mostrarResultado('resultadoEmpleados', `❌ Error: ${data.mensaje}`, 'error');
                }
            })
            .catch(error => {
                mostrarResultado('resultadoEmpleados', `❌ Error de conexión: ${error}`, 'error');
            });
        }
        
        function probarClientes() {
            mostrarResultado('resultadoClientes', 'Cargando clientes...', 'info');
            
            fetch('obtener_clientes_ajax.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarResultado('resultadoClientes', `✅ Clientes cargados: ${data.clientes.length} encontrados`, 'success');
                        data.clientes.forEach(cli => {
                            const estado = cli.activo ? 'Activo' : 'Inactivo';
                            mostrarResultado('resultadoClientes', `- ID: ${cli.id}, Nombre: ${cli.nombre}, Email: ${cli.email}, Estado: ${estado}`, 'info');
                        });
                    } else {
                        mostrarResultado('resultadoClientes', `❌ Error: ${data.mensaje}`, 'error');
                    }
                })
                .catch(error => {
                    mostrarResultado('resultadoClientes', `❌ Error de conexión: ${error}`, 'error');
                });
        }
        
        function probarToggleCliente(id, estado) {
            const accion = estado === 1 ? 'activar' : 'desactivar';
            mostrarResultado('resultadoClientes', `Intentando ${accion} cliente ID ${id}...`, 'info');
            
            fetch('toggle_estado_cliente.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: id,
                    estado: estado
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarResultado('resultadoClientes', `✅ ${data.mensaje}`, 'success');
                } else {
                    mostrarResultado('resultadoClientes', `❌ Error: ${data.mensaje}`, 'error');
                }
            })
            .catch(error => {
                mostrarResultado('resultadoClientes', `❌ Error de conexión: ${error}`, 'error');
            });
        }
        
        function probarAgregarEmpleado() {
            const nombre = document.getElementById('testNombre').value;
            const email = document.getElementById('testEmail').value;
            const password = document.getElementById('testPassword').value;
            
            mostrarResultado('resultadoAgregar', `Agregando empleado: ${nombre}...`, 'info');
            
            const formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('email', email);
            formData.append('password', password);
            
            fetch('agregar_empleado_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarResultado('resultadoAgregar', `✅ ${data.mensaje}`, 'success');
                } else {
                    mostrarResultado('resultadoAgregar', `❌ Error: ${data.mensaje}`, 'error');
                }
            })
            .catch(error => {
                mostrarResultado('resultadoAgregar', `❌ Error de conexión: ${error}`, 'error');
            });
        }
    </script>
</body>
</html>
