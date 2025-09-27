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
?>
