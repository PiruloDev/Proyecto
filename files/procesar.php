<?php
include 'conexion.php';

if($_POST) {
    $nombre = $_POST['Nombre'];
    $telefono = $_POST['telefono'] ?? '';
    $email = $_POST['email'];
    $contraseña = $_POST['contraseña'];
    
    $salt = bin2hex(random_bytes(16));
    $contraseña_encriptada = hash('sha256', $contraseña . $salt);
    
    try {
        $sql = "INSERT INTO clientes (NOMBRE_CLI, TELEFONO_CLI, EMAIL_CLI, CONTRASEÑA_CLI, SALT_CLI) VALUES (?, ?, ?, ?, ?)";
        $consulta = $pdo_conexion->prepare($sql);
        
        $resultado = $consulta->execute([
            $nombre,
            $telefono,
            $email,
            $contraseña_encriptada,
            $salt
        ]);
        
        if($resultado) {
            echo "<h2>✅ Cliente registrado exitosamente</h2>";
            echo "<p><strong>ID del cliente:</strong> " . $pdo_conexion->lastInsertId() . "</p>";
            echo "<p><strong>Nombre:</strong> " . htmlspecialchars($nombre) . "</p>";
            echo "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
            echo "<br><a href='index.html'>Registrar otro cliente</a>";
        }
        
    } catch(PDOException $e) {
        echo "<h2>❌ Error al registrar cliente</h2>";
        echo "<p>Error: " . $e->getMessage() . "</p>";
        echo "<br><a href='index.html'>Volver al formulario</a>";
    }
} else {
    echo "<h2>No hay datos para procesar</h2>";
    echo "<a href='index.html'>Ir al formulario</a>";
}
?>