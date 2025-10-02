<?php
// Verificar si hay una sesión de cliente activa
session_start();
$clienteLogueado = false;
$nombreCliente = '';

if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa']) {
    $clienteLogueado = true;
    $nombreCliente = $_SESSION['usuario_nombre'] ?? 'Cliente';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Castillo del Pan - Panadería</title>
</head>
<body>
    <header>
        <h1>🍞 El Castillo del Pan</h1>
        <nav>
            <?php if ($clienteLogueado): ?>
                <div style="float: right;">
                    <span>Bienvenido, <?php echo htmlspecialchars($nombreCliente); ?></span> |
                    <a href="PHP Modulos/logout.php">Cerrar Sesión</a>
                </div>
            <?php else: ?>
                <div style="float: right;">
                    <a href="PHP Modulos/index.php">Iniciar Sesión</a>
                </div>
            <?php endif; ?>
        </nav>
        <div style="clear: both;"></div>
    </header>

    <main>
        <section>
            <h2>Bienvenidos a Nuestra Panadería</h2>
            <p>El Castillo del Pan es una panadería de barrio que lleva más de seis meses ofreciendo los mejores productos horneados de la región.</p>
        </section>

        <?php if ($clienteLogueado): ?>
            <!-- Contenido para clientes autenticados -->
            <section>
                <h3>Panel de Cliente</h3>
                <div>
                    <h4>Opciones Disponibles:</h4>
                    <ul>
                        <li>📋 <a href="#">Ver Catálogo de Productos</a></li>
                        <li>🛒 <a href="#">Realizar Pedidos</a></li>
                        <li>📄 <a href="#">Historial de Pedidos</a></li>
                        <li>👤 <a href="#">Mi Perfil</a></li>
                        <li>📞 <a href="#">Contactar Soporte</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4>Estado de mi Cuenta:</h4>
                    <p>✅ Sesión activa</p>
                    <p>🔐 Autenticado con JWT</p>
                </div>
            </section>
        <?php else: ?>
            <!-- Contenido para visitantes no autenticados -->
            <section>
                <h3>Nuestros Productos</h3>
                <div>
                    <h4>Especialidades:</h4>
                    <ul>
                        <li>🍞 Panes Artesanales</li>
                        <li>🎂 Tortas y Pasteles</li>
                        <li>🧁 Postres Caseros</li>
                        <li>🥐 Hojaldres</li>
                        <li>🍪 Galletas</li>
                    </ul>
                </div>
                
                <div>
                    <p><strong>¿Eres cliente?</strong> <a href="PHP Modulos/index.php">Inicia sesión</a> para acceder a funciones exclusivas como realizar pedidos y ver tu historial.</p>
                </div>
            </section>
        <?php endif; ?>

        <section>
            <h3>Información de Contacto</h3>
            <p>📍 Dirección: Barrio Local</p>
            <p>📞 Teléfono: (123) 456-7890</p>
            <p>🕐 Horarios: Lunes a Domingo, 6:00 AM - 8:00 PM</p>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 El Castillo del Pan. Proyecto educativo SENA - Ficha 2996176</p>
        <p>Desarrollado por: Andrés Alcalá, Damián Ávila, Ana Goyeneche, Sharyth Zamora, Brayan Jiménez</p>
    </footer>
</body>
</html>