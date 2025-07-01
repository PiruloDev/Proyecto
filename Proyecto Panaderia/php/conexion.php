<?php
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "proyectopanaderia";

try {
    $conexion = new PDO("mysql:host=$servidor;dbname=$base_datos", $usuario, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión exitosa a la base de datos 'proyectopanaderia'<br>";

} catch(PDOException $e) {
    echo "Error de conexión: " . $e->getMessage() . "<br>";
}
?>
