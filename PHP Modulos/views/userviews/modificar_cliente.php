<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Cliente</title>
</head>
<body>
    <h1>Modificar Cliente</h1>
    
    <?php if (!empty($mensaje)): ?>
        <?= $mensaje ?>
    <?php endif; ?>
    
    <?php if (!empty($cliente)): ?>
        <h2>Cliente a Modificar</h2>
        <p><strong>ID:</strong> <?= htmlspecialchars($cliente['id']) ?></p>
        <p><strong>Nombre:</strong> <?= htmlspecialchars($cliente['Nombre:'] ?? 'N/A') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($cliente['Correo Electronico:'] ?? 'N/A') ?></p>
        <p><strong>Telefono:</strong> <?= htmlspecialchars($cliente['Telefono:'] ?? 'N/A') ?></p>
          
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?= htmlspecialchars($cliente['id']) ?>">
            <hr>
            
            <h4>Cambiar Nombre</h4>
            <input type="text" name="nuevo_nombre" placeholder="Nuevo nombre" required>
            <button type="submit" name="accion" value="cambiar_nombre">Cambiar Nombre</button>
            <hr>
            <h4>Cambiar Email</h4>
            <input type="email" name="nuevo_email" placeholder="Nuevo email" required>
            <button type="submit" name="accion" value="cambiar_email">Cambiar Email</button>
            
            <hr>
            
            <h4>Cambiar Telefono</h4>
            <input type="text" name="nuevo_telefono" placeholder="Nuevo telefono" required>
            <button type="submit" name="accion" value="cambiar_telefono">Cambiar Telefono</button>
            
            <hr>
            
            <h4>Cambiar Contraseña</h4>
            <input type="password" name="nueva_contrasena" placeholder="Nueva contraseña" required>
            <button type="submit" name="accion" value="cambiar_contrasena">Cambiar Contraseña</button>
        </form>
        
    <?php else: ?>
        <p>No se encontro el cliente especificado.</p>
    <?php endif; ?>
    
    <br>
    <a href="/PHP%20Modulos/Userindex.php">Volver al listado de clientes</a>
</body>
</html>