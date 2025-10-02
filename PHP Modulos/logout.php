<?php
// Archivo para limpiar la sesión y empezar de nuevo
session_start();
session_destroy();

echo "<h1>Sesión Limpiada</h1>";
echo "<p>La sesión ha sido eliminada completamente.</p>";
echo "<p><a href='login.php'>Volver al Login</a></p>";
?>