<?php
// Script para limpiar empleados de prueba
require_once 'conexion.php';

echo "<h2>🧹 Limpieza de Empleados de Prueba</h2>";

// Mostrar empleados actuales
echo "<h3>👥 Empleados Actuales</h3>";
$query = "SELECT * FROM Empleados ORDER BY ID_EMPLEADO";
$result = $conexion->query($query);

if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Acción</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['ID_EMPLEADO'] . "</td>";
        echo "<td>" . ($row['NOMBRE_EMPLEADO'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['EMAIL_EMPLEADO'] ?? 'N/A') . "</td>";
        echo "<td><a href='?delete=" . $row['ID_EMPLEADO'] . "' onclick='return confirm(\"¿Eliminar este empleado?\")'>Eliminar</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No hay empleados registrados</p>";
}

// Procesar eliminación
if (isset($_GET['delete'])) {
    $id_empleado = (int)$_GET['delete'];
    
    $delete_query = "DELETE FROM Empleados WHERE ID_EMPLEADO = ?";
    $stmt = $conexion->prepare($delete_query);
    $stmt->bind_param('i', $id_empleado);
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Empleado eliminado exitosamente</p>";
        echo "<script>window.location.href = 'limpiar_empleados.php';</script>";
    } else {
        echo "<p style='color: red;'>❌ Error al eliminar empleado</p>";
    }
    
    $stmt->close();
}

// Opción para eliminar todos los empleados
if (isset($_GET['deleteall'])) {
    $delete_all_query = "DELETE FROM Empleados";
    if ($conexion->query($delete_all_query)) {
        echo "<p style='color: green;'>✅ Todos los empleados eliminados</p>";
        echo "<script>window.location.href = 'limpiar_empleados.php';</script>";
    } else {
        echo "<p style='color: red;'>❌ Error al eliminar todos los empleados</p>";
    }
}

echo "<hr>";
echo "<p><a href='?deleteall=1' onclick='return confirm(\"¿Eliminar TODOS los empleados?\")' style='color: red;'>🗑️ Eliminar Todos los Empleados</a></p>";
echo "<p><a href='dashboard_admin.php'>← Volver al Dashboard</a></p>";

$conexion->close();
?>
