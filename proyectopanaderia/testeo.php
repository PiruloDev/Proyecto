<?php
// Archivo para probar la conexión paso a paso
echo "<h2>Prueba de Conexión y Inserción</h2>";

// 1. Probar conexión básica
echo "<h3>1. Probando conexión...</h3>";
include 'conexion.php';

// 2. Verificar que la tabla existe
echo "<h3>2. Verificando tabla clientes...</h3>";
try {
    $sql_check = "DESCRIBE clientes";
    $resultado = $conexion->query($sql_check);
    echo "✅ Tabla clientes encontrada<br>";
    
    // Mostrar estructura de la tabla
    echo "<strong>Estructura de la tabla:</strong><br>";
    while($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $fila['Field'] . " (" . $fila['Type'] . ")<br>";
    }
} catch(PDOException $e) {
    echo "❌ Error con la tabla: " . $e->getMessage() . "<br>";
}

// 3. Probar inserción simple
echo "<h3>3. Probando inserción...</h3>";
$nombre_test = "Juan Pérez Test";
$telefono_test = "123456789";
$email_test = "juan.test" . rand(1000,9999) . "@test.com"; // Email único
$contraseña_test = "123456";

$salt = bin2hex(random_bytes(16));
$contraseña_encriptada = hash('sha256', $contraseña_test . $salt);

try {
    // Usar signos de interrogación en lugar de nombres
    $sql = "INSERT INTO clientes (NOMBRE_CLI, TELEFONO_CLI, EMAIL_CLI, CONTRASEÑA_CLI, SALT_CLI) VALUES (?, ?, ?, ?, ?)";
    $consulta = $conexion->prepare($sql);
    
    $resultado = $consulta->execute([
        $nombre_test,
        $telefono_test, 
        $email_test,
        $contraseña_encriptada,
        $salt
    ]);
    
    if($resultado) {
        echo "✅ Inserción exitosa - ID: " . $conexion->lastInsertId() . "<br>";
        echo "Datos insertados:<br>";
        echo "- Nombre: $nombre_test<br>";
        echo "- Teléfono: $telefono_test<br>";
        echo "- Email: $email_test<br>";
    }
} catch(PDOException $e) {
    echo "❌ Error en inserción: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h3>4. Datos del formulario (si los hay):</h3>";
if($_POST) {
    echo "<strong>Datos recibidos del formulario:</strong><br>";
    echo "- Nombre: " . ($_POST['nombre'] ?? 'No definido') . "<br>";
    echo "- Teléfono: " . ($_POST['telefono'] ?? 'No definido') . "<br>";
    echo "- Email: " . ($_POST['email'] ?? 'No definido') . "<br>";
    echo "- Contraseña: " . (isset($_POST['contraseña']) ? '[DEFINIDA]' : 'No definida') . "<br>";
    
    // Intentar insertar datos del formulario
    if(isset($_POST['nombre']) && isset($_POST['email']) && isset($_POST['contraseña'])) {
        echo "<h4>Insertando datos del formulario:</h4>";
        
        $nombre = $_POST['nombre'];
        $telefono = $_POST['telefono'] ?? '';
        $email = $_POST['email'];
        $contraseña = $_POST['contraseña'];
        
        $salt = bin2hex(random_bytes(16));
        $contraseña_encriptada = hash('sha256', $contraseña . $salt);
        
        try {
            $sql = "INSERT INTO clientes (NOMBRE_CLI, TELEFONO_CLI, EMAIL_CLI, CONTRASEÑA_CLI, SALT_CLI) VALUES (?, ?, ?, ?, ?)";
            $consulta = $conexion->prepare($sql);
            
            $resultado = $consulta->execute([
                $nombre,
                $telefono,
                $email,
                $contraseña_encriptada,
                $salt
            ]);
            
            if($resultado) {
                echo "✅ Cliente del formulario registrado exitosamente - ID: " . $conexion->lastInsertId() . "<br>";
            }
        } catch(PDOException $e) {
            echo "❌ Error al registrar cliente del formulario: " . $e->getMessage() . "<br>";
        }
    }
} else {
    echo "No hay datos POST<br>";
    echo "<a href='index.html'>Ir al formulario de registro</a>";
}
?>