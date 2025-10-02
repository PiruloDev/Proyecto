<?php
session_start();

// Depuración completa de la sesión
echo "<h2>Información de la Sesión</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Status:</strong> " . session_status() . "</p>";

echo "<h3>Variables de Sesión:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Cookies:</h3>";
echo "<pre>";
print_r($_COOKIE);
echo "</pre>";

echo "<h3>Información del Servidor:</h3>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Server IP:</strong> " . $_SERVER['SERVER_ADDR'] . "</p>";
echo "<p><strong>Client IP:</strong> " . $_SERVER['REMOTE_ADDR'] . "</p>";
echo "<p><strong>User Agent:</strong> " . $_SERVER['HTTP_USER_AGENT'] . "</p>";

echo "<h3>Validación de Empleado:</h3>";
$is_empleado = isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'empleado';
echo "<p><strong>¿Es empleado?:</strong> " . ($is_empleado ? "SÍ" : "NO") . "</p>";

if (isset($_SESSION['usuario_tipo'])) {
    echo "<p><strong>Tipo actual:</strong> " . $_SESSION['usuario_tipo'] . "</p>";
} else {
    echo "<p><strong>Tipo actual:</strong> NO DEFINIDO</p>";
}

// Verificar si hay conflicto de sesiones
if (isset($_SESSION['usuario_logueado'])) {
    echo "<p><strong>Usuario logueado:</strong> " . ($_SESSION['usuario_logueado'] ? "SÍ" : "NO") . "</p>";
} else {
    echo "<p><strong>Usuario logueado:</strong> NO DEFINIDO</p>";
}

echo "<h3>Enlaces de Prueba:</h3>";
echo "<a href='dashboard_empleado.php'>Dashboard Empleado</a><br>";
echo "<a href='login.php'>Login</a><br>";
echo "<a href='logout.php'>Logout</a><br>";
?>
