<?php
echo "<h1>¡Hola desde GitHub Codespaces con PHP y Apache!</h1>";
echo "<p>Versión de PHP: " . phpversion() . "</p>";

// Variables de entorno para la conexión a MySQL (estas se definirán en docker-compose.yml)
$db_host = getenv('MYSQL_HOST'); // Usaremos el nombre del servicio definido en docker-compose
$db_name = getenv('MYSQL_DATABASE');
$db_user = getenv('MYSQL_USER');
$db_pass = getenv('MYSQL_PASSWORD');

echo "<h3>Intentando conectar a MySQL:</h3>";
echo "<p><strong>Host:</strong> " . htmlspecialchars($db_host ?: 'No definido') . "</p>";
echo "<p><strong>Database:</strong> " . htmlspecialchars($db_name ?: 'No definido') . "</p>";
echo "<p><strong>User:</strong> " . htmlspecialchars($db_user ?: 'No definido') . "</p>";


if ($db_host && $db_name && $db_user && $db_pass) {
    try {
        $conn = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
        // Establecer el modo de error PDO a excepción
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p style='color:green; font-weight:bold;'>¡Conexión exitosa a MySQL!</p>";

        // Ejemplo: Crear una tabla si no existe
        $conn->exec("CREATE TABLE IF NOT EXISTS visitas (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        fecha_visita TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        info_cliente VARCHAR(255)
                    )");
        
        $info_cliente = $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';
        $stmt_insert = $conn->prepare("INSERT INTO visitas (info_cliente) VALUES (:info_cliente)");
        $stmt_insert->bindParam(':info_cliente', $info_cliente);
        $stmt_insert->execute();

        // Ejemplo: Contar visitas
        $stmt_count = $conn->query("SELECT COUNT(*) AS total_visitas FROM visitas");
        $row = $stmt_count->fetch(PDO::FETCH_ASSOC);
        echo "<p>Total de visitas registradas en la tabla 'visitas': " . htmlspecialchars($row['total_visitas']) . "</p>";

        // Mostrar últimas 5 visitas
        $stmt_select = $conn->query("SELECT id, fecha_visita, info_cliente FROM visitas ORDER BY id DESC LIMIT 5");
        echo "<h4>Últimas 5 visitas:</h4><ul>";
        while($visita = $stmt_select->fetch(PDO::FETCH_ASSOC)) {
            echo "<li>ID: " . htmlspecialchars($visita['id']) . " | Fecha: " . htmlspecialchars($visita['fecha_visita']) . " | Info: " . htmlspecialchars(substr($visita['info_cliente'], 0, 50)) . "...</li>";
        }
        echo "</ul>";


    } catch(PDOException $e) {
        echo "<p style='color:red; font-weight:bold;'>Error de conexión a MySQL: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p>Por favor, verifica lo siguiente:</p>";
        echo "<ul>";
        echo "<li>Que el servicio MySQL ('db_mysql') esté ejecutándose en Docker.</li>";
        echo "<li>Que las variables de entorno (MYSQL_HOST, MYSQL_DATABASE, MYSQL_USER, MYSQL_PASSWORD) en <code>docker-compose.yml</code> coincidan con la configuración del servidor MySQL y con lo que espera este script.</li>";
        echo "<li>Que la red 'mi_red_dev' permita la comunicación entre los contenedores 'app' y 'db_mysql'.</li>";
        echo "</ul>";
    }
} else {
    echo "<p style='color:orange; font-weight:bold;'>Variables de entorno de MySQL no configuradas completamente en <code>docker-compose.yml</code> para el servicio 'app'.</p>";
    echo "<p>Faltan una o más de las siguientes variables para el servicio 'app': MYSQL_HOST, MYSQL_DATABASE, MYSQL_USER, MYSQL_PASSWORD.</p>";
}
?>