<?php
include 'conexion.php';

echo "<h1>Verificación y Configuración de Administradores</h1>";

try {
    // Verificar si hay administradores en la base de datos
    $stmt = $conexion->prepare("SELECT * FROM Administradores");
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "<h2>Administradores actuales en la base de datos:</h2>";
    
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>ID</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Tiene Contraseña</th><th>Tiene Salt</th>";
        echo "</tr>";
        
        while ($admin = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($admin['ID_ADMIN']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['NOMBRE_ADMIN']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['EMAIL_ADMIN'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($admin['TELEFONO_ADMIN'] ?? 'N/A') . "</td>";
            echo "<td>" . (!empty($admin['CONTRASEÑA_ADMIN']) ? 'SÍ' : 'NO') . "</td>";
            echo "<td>" . (!empty($admin['SALT_ADMIN']) ? 'SÍ' : 'NO') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ No hay administradores registrados en la base de datos.</p>";
    }
    
    echo "<hr>";
    
    // Crear o actualizar administrador con credenciales
    echo "<h2>Configurar Administrador de Prueba:</h2>";
    
    $admin_usuario = "admin";
    $admin_password = "123456";
    $admin_email = "admin@panaderia.com";
    $admin_telefono = "3001234567";
    
    // Generar salt y hash
    $salt = bin2hex(random_bytes(16));
    $password_hash = hash('sha256', $admin_password . $salt);
    
    // Verificar si ya existe un admin con ese nombre
    $stmt = $conexion->prepare("SELECT ID_ADMIN FROM Administradores WHERE NOMBRE_ADMIN = ?");
    $stmt->bind_param("s", $admin_usuario);
    $stmt->execute();
    $existing = $stmt->get_result();
    
    if ($existing->num_rows > 0) {
        // Actualizar admin existente
        $stmt = $conexion->prepare("
            UPDATE Administradores 
            SET EMAIL_ADMIN = ?, TELEFONO_ADMIN = ?, CONTRASEÑA_ADMIN = ?, SALT_ADMIN = ?
            WHERE NOMBRE_ADMIN = ?
        ");
        $stmt->bind_param("sssss", $admin_email, $admin_telefono, $password_hash, $salt, $admin_usuario);
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✅ Administrador '$admin_usuario' actualizado exitosamente.</p>";
        } else {
            echo "<p style='color: red;'>❌ Error al actualizar administrador: " . $conexion->error . "</p>";
        }
    } else {
        // Crear nuevo admin
        $stmt = $conexion->prepare("
            INSERT INTO Administradores (NOMBRE_ADMIN, EMAIL_ADMIN, TELEFONO_ADMIN, CONTRASEÑA_ADMIN, SALT_ADMIN)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssss", $admin_usuario, $admin_email, $admin_telefono, $password_hash, $salt);
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✅ Administrador '$admin_usuario' creado exitosamente.</p>";
        } else {
            echo "<p style='color: red;'>❌ Error al crear administrador: " . $conexion->error . "</p>";
        }
    }
    
    echo "<div style='background-color: #e8f5e8; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>Credenciales del Administrador:</h3>";
    echo "<p><strong>Usuario:</strong> $admin_usuario</p>";
    echo "<p><strong>Contraseña:</strong> $admin_password</p>";
    echo "<p><strong>Email:</strong> $admin_email</p>";
    echo "<p><strong>Tipo de usuario:</strong> admin</p>";
    echo "</div>";
    
    echo "<hr>";
    echo "<h2>Verificación Final:</h2>";
    
    // Verificar que el login funcione
    $stmt = $pdo_conexion->prepare("
        SELECT ID_ADMIN, NOMBRE_ADMIN, CONTRASEÑA_ADMIN, SALT_ADMIN, EMAIL_ADMIN, TELEFONO_ADMIN 
        FROM Administradores 
        WHERE (NOMBRE_ADMIN = :usuario OR EMAIL_ADMIN = :usuario)
    ");
    $stmt->bindParam(':usuario', $admin_usuario, PDO::PARAM_STR);
    $stmt->execute();
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user_data) {
        $hash_verificacion = hash('sha256', $admin_password . $user_data['SALT_ADMIN']);
        $password_correcta = ($hash_verificacion === $user_data['CONTRASEÑA_ADMIN']);
        
        echo "<p style='color: " . ($password_correcta ? 'green' : 'red') . ";'>";
        echo ($password_correcta ? "✅" : "❌") . " Verificación de login: " . ($password_correcta ? "EXITOSA" : "FALLIDA");
        echo "</p>";
        
        if ($password_correcta) {
            echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>🎉 ¡Listo para usar!</h3>";
            echo "<p>Ahora puedes hacer login con:</p>";
            echo "<ul>";
            echo "<li><strong>Tipo de usuario:</strong> admin</li>";
            echo "<li><strong>Usuario:</strong> $admin_usuario</li>";
            echo "<li><strong>Contraseña:</strong> $admin_password</li>";
            echo "</ul>";
            echo "<p><a href='login.php' style='color: blue;'>🔗 Ir al Login</a></p>";
            echo "</div>";
        }
    } else {
        echo "<p style='color: red;'>❌ No se pudo encontrar el administrador creado.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
