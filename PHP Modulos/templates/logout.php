<?php
session_start();

// Registrar logout en log para seguridad
if (isset($_SESSION['usuario_logueado']) && $_SESSION['usuario_logueado']) {
    $usuario_tipo = $_SESSION['usuario_tipo'] ?? 'desconocido';
    $usuario_nombre = $_SESSION['usuario_nombre'] ?? 'desconocido';
    error_log("Logout exitoso - Tipo: $usuario_tipo, Usuario: $usuario_nombre, IP: " . $_SERVER['REMOTE_ADDR']);
}

// Destruir todas las variables de sesión
$_SESSION = array();

// Si se está usando cookies de sesión, borrar la cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Redirigir al login con mensaje de logout exitoso
header('Location: login.php?logout=success');
exit();
?>
