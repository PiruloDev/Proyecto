<?php
session_start();
header('Content-Type: application/json');

echo json_encode([
    'session_id' => session_id(),
    'session_status' => session_status(),
    'session_data' => $_SESSION,
    'cookies' => $_COOKIE,
    'is_empleado' => (
        isset($_SESSION['usuario_logueado']) && 
        $_SESSION['usuario_logueado'] === true &&
        isset($_SESSION['usuario_tipo']) && 
        $_SESSION['usuario_tipo'] === 'empleado'
    ),
    'auth_check' => [
        'usuario_logueado_set' => isset($_SESSION['usuario_logueado']),
        'usuario_logueado_value' => $_SESSION['usuario_logueado'] ?? 'not_set',
        'usuario_tipo_set' => isset($_SESSION['usuario_tipo']),
        'usuario_tipo_value' => $_SESSION['usuario_tipo'] ?? 'not_set'
    ]
]);
?>
