<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
</head>
<body>
    <h1>Gestión de Usuarios</h1>
    <?php if (!empty($mensaje)): ?>
        <div><?php echo $mensaje; ?></div>
    <?php endif; ?>
    <h2>Agregar Nuevo Cliente</h2>
    <form method="POST" action="">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required><br><br>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        
        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono" required><br><br>
        
        <label for="contraseña">Contraseña:</label>
        <input type="password" id="contraseña" name="contraseña" required><br><br>
        
        <button type="submit">Agregar Cliente</button>
    </form>
    <h2>Lista de Clientes</h2>
    <?php if (!empty($usuarios)): ?>
        <?php foreach ($usuarios as $index => $usuario): ?>
            <div>
                <strong>Cliente <?php echo ($index + 1); ?>:</strong><br>
                Nombre: <?php echo htmlspecialchars($usuario['Nombre:'] ?? $usuario['nombre'] ?? 'N/A'); ?><br>
                Teléfono: <?php echo htmlspecialchars($usuario['Telefono:'] ?? $usuario['telefono'] ?? 'N/A'); ?><br>
                Email: <?php echo htmlspecialchars($usuario['Correo Electronico:'] ?? $usuario['email'] ?? 'N/A'); ?><br>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>