<?php
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "ProyectoPanaderia"; // Nota: con mayúsculas como en el SQL

// Conexión MySQLi
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Establecer charset
$conexion->set_charset("utf8");

// Opcional: mantener compatibilidad con PDO para otros archivos que lo usen
try {
    $pdo_conexion = new PDO("mysql:host=$servidor;dbname=$base_datos;charset=utf8", $usuario, $password);
    $pdo_conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión PDO: " . $e->getMessage());
}

// Ordenar por: 'cantidad' o 'ingresos'
$ordenarPor = isset($_GET['orden']) ? $_GET['orden'] : 'cantidad';

$ordenSQL = $ordenarPor === 'ingresos' ? 'total_ingresos' : 'total_cantidad';

while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($fila['producto']) ?></td>
                    <td><?= $fila['total_cantidad'] ?></td>
                    <td><?= number_format($fila['total_ingresos'], 2) ?></td>
                </tr>
            <?php endwhile; 

// Consulta para generar el reporte
$sql = "
    SELECT 
        p.nombre AS producto,
        SUM(v.cantidad) AS total_cantidad,
        SUM(v.cantidad * p.precio) AS total_ingresos
    FROM ventas v
    JOIN productos p ON v.producto_id = p.id
    GROUP BY v.producto_id
    ORDER BY $ordenSQL DESC
";

$resultado = $conexion->query($sql);
?>

<?php
$conexion->close();
?>