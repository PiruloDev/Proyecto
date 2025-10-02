<?php
// Script de prueba para verificar endpoints de ingredientes
session_start();

// Simular sesión de administrador para las pruebas
$_SESSION['usuario_logueado'] = true;
$_SESSION['usuario_tipo'] = 'admin';
$_SESSION['usuario_id'] = 1;

echo "<h2>Prueba de Endpoints de Ingredientes</h2>\n";
echo "<h3>1. Prueba de Categorías de Ingredientes:</h3>\n";

// Probar endpoint de categorías
$url_categorias = 'http://localhost/proyectopanaderia/files/obtener_categorias_ingredientes_ajax.php';
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => [
            'Content-Type: application/json',
            'X-Requested-With: XMLHttpRequest',
            'Cookie: ' . session_name() . '=' . session_id()
        ]
    ]
]);

$response_categorias = file_get_contents($url_categorias, false, $context);
echo "<pre>" . htmlspecialchars($response_categorias) . "</pre>\n";

echo "<h3>2. Prueba de Proveedores:</h3>\n";

// Probar endpoint de proveedores
$url_proveedores = 'http://localhost/proyectopanaderia/files/obtener_proveedores_ajax.php';
$response_proveedores = file_get_contents($url_proveedores, false, $context);
echo "<pre>" . htmlspecialchars($response_proveedores) . "</pre>\n";

// Verificar datos en base de datos directamente
echo "<h3>3. Verificación directa en Base de Datos:</h3>\n";

require_once 'conexion.php';

echo "<h4>Categorías en BD:</h4>\n";
$consulta_cat = "SELECT * FROM Categoria_Ingredientes ORDER BY NOMBRE_CATEGORIA_INGREDIENTE";
$resultado_cat = $conexion->query($consulta_cat);
if ($resultado_cat) {
    echo "<ul>\n";
    while ($fila = $resultado_cat->fetch_assoc()) {
        echo "<li>ID: {$fila['ID_CATEGORIA']} - {$fila['NOMBRE_CATEGORIA_INGREDIENTE']}</li>\n";
    }
    echo "</ul>\n";
} else {
    echo "Error: " . $conexion->error . "\n";
}

echo "<h4>Proveedores en BD:</h4>\n";
$consulta_prov = "SELECT * FROM Proveedores ORDER BY NOMBRE_PROV";
$resultado_prov = $conexion->query($consulta_prov);
if ($resultado_prov) {
    echo "<ul>\n";
    while ($fila = $resultado_prov->fetch_assoc()) {
        $activo = $fila['ACTIVO_PROV'] ? 'Activo' : 'Inactivo';
        echo "<li>ID: {$fila['ID_PROVEEDOR']} - {$fila['NOMBRE_PROV']} ({$activo})</li>\n";
    }
    echo "</ul>\n";
} else {
    echo "Error: " . $conexion->error . "\n";
}

$conexion->close();
?>
