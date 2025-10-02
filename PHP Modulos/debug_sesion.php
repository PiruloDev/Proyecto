<?php
// Página de debug para verificar el estado de la sesión
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/services/userservices/AuthService.php';
require_once __DIR__ . '/controllers/userscontroller/AuthController.php';

echo "<h1>Debug de Sesión de Usuario</h1>";

$authService = new AuthService();

echo "<h2>Estado de la sesión:</h2>";
session_start();
echo "<pre>";
echo "Datos de sesión:\n";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Verificaciones de AuthService:</h2>";
echo "¿Está autenticado?: " . ($authService->estaAutenticado() ? "✅ SÍ" : "❌ NO") . "<br>";
echo "Rol del usuario: '" . ($authService->obtenerRolUsuario() ?? 'NULL') . "'<br>";
echo "Token: " . (strlen($authService->obtenerToken() ?? '') > 0 ? "✅ Presente" : "❌ Ausente") . "<br>";

echo "<h2>Información del token JWT:</h2>";
$token = $authService->obtenerToken();
if ($token) {
    $reflection = new ReflectionClass($authService);
    $method = $reflection->getMethod('decodificarJWT');
    $method->setAccessible(true);
    $tokenData = $method->invoke($authService, $token);
    echo "<pre>";
    print_r($tokenData);
    echo "</pre>";
} else {
    echo "❌ No hay token disponible<br>";
}

echo "<h2>Test de verificación de acceso:</h2>";
$authController = new AuthController();
echo "¿Puede acceder como administrador?: " . ($authController->verificarAcceso(['administrador']) ? "✅ SÍ" : "❌ NO") . "<br>";
echo "¿Puede acceder como empleado?: " . ($authController->verificarAcceso(['empleado']) ? "✅ SÍ" : "❌ NO") . "<br>";
echo "¿Puede acceder como cliente?: " . ($authController->verificarAcceso(['cliente']) ? "✅ SÍ" : "❌ NO") . "<br>";

echo "<hr>";
echo "<h2>Enlaces de prueba:</h2>";
echo "<a href='controllers/userscontroller/AuthController.php?action=login'>Ir al Login</a><br>";
echo "<a href='views/authviews/dashboard_admin.php'>Dashboard Admin</a><br>";
echo "<a href='views/authviews/dashboard_empleado.php'>Dashboard Empleado</a><br>";
echo "<a href='controllers/userscontroller/AuthController.php?action=logout'>Cerrar Sesión</a><br>";
?>