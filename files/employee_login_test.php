<?php
// Debug login process for employee
session_start();

echo "<h2>Employee Login Debug</h2>";

// Clear any existing session
session_destroy();
session_start();

echo "<p><strong>Step 1:</strong> Session cleared and restarted</p>";
echo "<p>Session ID: " . session_id() . "</p>";

// Simulate the login process for an employee
require_once 'conexion.php';

$test_employee = 'admin'; // or whatever employee username exists
$test_password = 'admin123'; // or whatever password

echo "<h3>Step 2: Testing Employee Login</h3>";
echo "<p>Testing user: $test_employee</p>";

try {
    $consulta = "SELECT ID_EMPLEADO, NOMBRE_EMPLEADO, CONTRASEÑA_EMPLEADO, SALT_EMPLEADO, ACTIVO_EMPLEADO, EMAIL_EMPLEADO
                FROM Empleados 
                WHERE (NOMBRE_EMPLEADO = :usuario OR EMAIL_EMPLEADO = :usuario) AND ACTIVO_EMPLEADO = 1";
    
    $stmt = $pdo_conexion->prepare($consulta);
    $stmt->bindParam(':usuario', $test_employee, PDO::PARAM_STR);
    $stmt->execute();
    
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user_data) {
        echo "<p><strong>✓ Employee found:</strong> " . $user_data['NOMBRE_EMPLEADO'] . "</p>";
        
        // Test password
        $password_hash = $user_data['CONTRASEÑA_EMPLEADO'];
        $salt = $user_data['SALT_EMPLEADO'];
        
        $password_valid = false;
        
        if (!empty($salt) && !empty($password_hash)) {
            $hash_to_check = hash('sha256', $test_password . $salt);
            if ($hash_to_check === $password_hash) {
                $password_valid = true;
            }
        } elseif (!empty($password_hash)) {
            if ($test_password === $password_hash) {
                $password_valid = true;
            }
        }
        
        if ($password_valid) {
            echo "<p><strong>✓ Password valid</strong></p>";
            
            // Set session like the real login process
            $_SESSION['usuario_logueado'] = true;
            $_SESSION['usuario_tipo'] = 'empleado';
            $_SESSION['usuario_id'] = $user_data['ID_EMPLEADO'];
            $_SESSION['usuario_nombre'] = $user_data['NOMBRE_EMPLEADO'];
            $_SESSION['user_name'] = $user_data['NOMBRE_EMPLEADO'];
            $_SESSION['role'] = 'empleado';
            
            echo "<p><strong>✓ Session set</strong></p>";
            echo "<pre>Session data:\n" . print_r($_SESSION, true) . "</pre>";
            
            echo "<h3>Step 3: Testing AJAX Endpoint</h3>";
            
            // Test if this session works with the AJAX endpoint
            echo '<div id="ajaxTest"></div>';
            echo '<script>
            fetch("obtener_pedidos_ajax.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    estado: "",
                    fecha: "",
                    solo_pendientes: true
                })
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById("ajaxTest").innerHTML = "<h4>AJAX Test Result:</h4><pre>" + data + "</pre>";
            })
            .catch(error => {
                document.getElementById("ajaxTest").innerHTML = "<h4>AJAX Test Error:</h4><pre>" + error + "</pre>";
            });
            </script>';
            
        } else {
            echo "<p><strong>✗ Password invalid</strong></p>";
        }
        
    } else {
        echo "<p><strong>✗ Employee not found</strong></p>";
    }
    
} catch (Exception $e) {
    echo "<p><strong>✗ Error:</strong> " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Navigation</h3>";
echo '<a href="dashboard_empleado.php">Go to Employee Dashboard</a><br>';
echo '<a href="login.php">Go to Login</a><br>';
echo '<a href="session_debug.php">Session Debug</a><br>';
?>
