<?php
include 'conexion.php';

$consulta = "SELECT * FROM Productos";
$resultado = $conexion->query($consulta);

if (!$resultado) {
    // Si $conexion es PDO
    if (method_exists($conexion, 'errorInfo')) {
        echo "Error en la consulta: " . implode(" - ", $conexion->errorInfo());
    } else {
        // Si $conexion es mysqli
        echo "Error en la consulta: " . $conexion->error;
    }
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Mostrar datos de MySQL</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <?php
                // Obtener los nombres de las columnas dinámicamente
                $columnas = array();
                for ($i = 0; $i < $resultado->columnCount(); $i++) {
                    $columnaMeta = $resultado->getColumnMeta($i);
                    $columnas[] = $columnaMeta['name'];
                    echo "<th>" . htmlspecialchars($columnaMeta['name']) . "</th>";
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <?php foreach ($columnas as $col): ?>
                        <td><?php echo htmlspecialchars($fila[$col]); ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
