<?php
// Archivo de prueba para verificar conectividad con el backend
require_once __DIR__ . '/services/authservices/AuthService.php';

echo "<h1>Prueba de Conectividad Backend JWT</h1>";

// Probar el endpoint de test
$url_test = 'http://localhost:8080/auth/test';
$proceso = curl_init();
curl_setopt($proceso, CURLOPT_URL, $url_test);
curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
curl_setopt($proceso, CURLOPT_TIMEOUT, 10);
curl_setopt($proceso, CURLOPT_HTTPHEADER, ['Accept: application/json']);

$response = curl_exec($proceso);
$http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);
$curl_error = curl_error($proceso);
curl_close($proceso);

echo "<h2>Test del Backend:</h2>";
echo "<p><strong>URL:</strong> $url_test</p>";
echo "<p><strong>Código HTTP:</strong> $http_code</p>";
if ($curl_error) {
    echo "<p><strong>Error cURL:</strong> $curl_error</p>";
}
echo "<p><strong>Respuesta:</strong></p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

// Probar login con credenciales hardcodeadas
echo "<hr>";
echo "<h2>Prueba de Login:</h2>";

$authService = new AuthService();
$resultado = $authService->autenticarUsuario('admin', 'password');

echo "<p><strong>Resultado de autenticación:</strong></p>";
echo "<pre>" . htmlspecialchars(json_encode($resultado, JSON_PRETTY_PRINT)) . "</pre>";

if ($resultado['success']) {
    echo "<p style='color:green;'>✅ Login exitoso!</p>";
    
    // Probar iniciar sesión
    $authService->iniciarSesion($resultado['data']);
    echo "<p>Sesión iniciada</p>";
    
    // Verificar sesión
    $sesionValida = $authService->verificarSesionActiva();
    echo "<p><strong>Sesión válida:</strong> " . ($sesionValida ? 'SÍ' : 'NO') . "</p>";
    
    // Mostrar datos de sesión
    session_start();
    echo "<p><strong>Datos de sesión:</strong></p>";
    echo "<ul>";
    echo "<li>Token: " . (isset($_SESSION['jwt_token']) ? 'Presente' : 'Ausente') . "</li>";
    echo "<li>Rol: " . ($_SESSION['usuario_rol'] ?? 'No definido') . "</li>";
    echo "<li>Nombre: " . ($_SESSION['usuario_nombre'] ?? 'No definido') . "</li>";
    echo "</ul>";
    
} else {
    echo "<p style='color:red;'>❌ Error en login</p>";
}
?>