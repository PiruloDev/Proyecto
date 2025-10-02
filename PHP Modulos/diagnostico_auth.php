<?php
// Archivo de diagnóstico para el sistema de autenticación
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/services/userservices/AuthService.php';

echo "<h1>Diagnóstico del Sistema de Autenticación</h1>";

// Test 1: Verificar que el servicio se puede instanciar
echo "<h2>Test 1: Instanciación del AuthService</h2>";
try {
    $authService = new AuthService();
    echo "✅ AuthService creado correctamente<br>";
} catch (Exception $e) {
    echo "❌ Error al crear AuthService: " . $e->getMessage() . "<br>";
    exit;
}

// Test 2: Verificar endpoint
echo "<h2>Test 2: Verificación de endpoints</h2>";
try {
    $endpoint = $authService->getAuthEndpoint('login');
    echo "✅ Endpoint de login: " . $endpoint . "<br>";
} catch (Exception $e) {
    echo "❌ Error en endpoint: " . $e->getMessage() . "<br>";
}

// Test 3: Verificar conectividad con el backend
echo "<h2>Test 3: Conectividad con el backend</h2>";
$ch = curl_init('http://localhost:8080/auth/login');
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 405 || $httpCode === 400) { // 405 Method Not Allowed es esperado para GET en login
    echo "✅ Backend accesible (HTTP $httpCode)<br>";
} else {
    echo "❌ Backend no accesible (HTTP $httpCode)<br>";
}

// Test 4: Test de login con credenciales de prueba
echo "<h2>Test 4: Prueba de autenticación</h2>";
$credenciales = [
    'email' => 'ana@admin.com',
    'contrasena' => 'admin321'
];

echo "Enviando credenciales: " . json_encode($credenciales) . "<br>";

try {
    $resultado = $authService->autenticarUsuario($credenciales);
    echo "<h3>Resultado de autenticación:</h3>";
    echo "<pre>" . json_encode($resultado, JSON_PRETTY_PRINT) . "</pre>";
    
    if ($resultado['success']) {
        echo "✅ Autenticación exitosa<br>";
        
        // Test 5: Decodificar JWT
        echo "<h2>Test 5: Decodificación del JWT</h2>";
        if (isset($resultado['data']['token'])) {
            $reflection = new ReflectionClass($authService);
            $method = $reflection->getMethod('decodificarJWT');
            $method->setAccessible(true);
            $tokenData = $method->invoke($authService, $resultado['data']['token']);
            echo "<pre>" . json_encode($tokenData, JSON_PRETTY_PRINT) . "</pre>";
        }
    } else {
        echo "❌ Autenticación fallida: " . ($resultado['error'] ?? 'Error desconocido') . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Excepción durante autenticación: " . $e->getMessage() . "<br>";
}

// Test 6: Verificar logs de error
echo "<h2>Test 6: Logs de error de PHP</h2>";
echo "Ubicación del log de errores: " . ini_get('error_log') . "<br>";
echo "Si no aparecen logs arriba, revisa el archivo de logs del servidor web.<br>";

?>

<hr>
<h2>Formulario de prueba manual</h2>
<form method="POST" action="">
    <input type="hidden" name="test_manual" value="1">
    <label>Email: <input type="email" name="email" value="ana@admin.com"></label><br><br>
    <label>Contraseña: <input type="password" name="contrasena" value="admin321"></label><br><br>
    <button type="submit">Probar Login Manual</button>
</form>

<?php
if (isset($_POST['test_manual'])) {
    echo "<h3>Resultado de prueba manual:</h3>";
    $credencialesManual = [
        'email' => $_POST['email'],
        'contrasena' => $_POST['contrasena']
    ];
    
    $resultadoManual = $authService->autenticarUsuario($credencialesManual);
    echo "<pre>" . json_encode($resultadoManual, JSON_PRETTY_PRINT) . "</pre>";
}
?>