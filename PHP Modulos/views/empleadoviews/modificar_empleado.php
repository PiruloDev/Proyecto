<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Empleado</title>
</head>
<body>
    <h1>Modificar Empleado</h1>
    
    <?php if (!empty($mensaje)): ?>
        <?= $mensaje ?>
    <?php endif; ?>
    
    <?php if (!empty($empleado)): ?>
        <h2>Empleado a Modificar</h2>
        <p><strong>ID:</strong> <?= htmlspecialchars($empleado['id']) ?></p>
        <p><strong>Nombre:</strong> <?= htmlspecialchars($empleado['Nombre:'] ?? 'N/A') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($empleado['Correo Electronico:'] ?? 'N/A') ?></p>
          
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?= htmlspecialchars($empleado['id']) ?>">
            <hr>
            
            <h4>Cambiar Nombre</h4>
            <input type="text" name="nuevo_nombre" placeholder="Nuevo nombre" required>
            <button type="submit" name="accion" value="cambiar_nombre">Cambiar Nombre</button>
            <hr>
            <h4>Cambiar Email</h4>
            <input type="email" name="nuevo_email" placeholder="Nuevo email" required>
            <button type="submit" name="accion" value="cambiar_email">Cambiar Email</button>
            
            <hr>
            
            <h4>Cambiar Contraseña</h4>
            <input type="password" name="nueva_contrasena" placeholder="Nueva contraseña" required>
            <button type="submit" name="accion" value="cambiar_contrasena">Cambiar Contraseña</button>
        </form>
        
        <hr>
        
        <h4>Eliminar Empleado</h4>
        <p style="color: red;"><strong>¡Atención!</strong> Esta acción no se puede deshacer.</p>
        <form method="POST" action="/PHP%20Modulos/Empleadoindex.php?action=delete" onsubmit="return confirm('¿Está seguro de que desea eliminar este empleado? Esta acción no se puede deshacer.');">
            <input type="hidden" name="id" value="<?= htmlspecialchars($empleado['id']) ?>">
            <button type="submit" style="background-color: #dc3545; color: white; border: none; padding: 10px 20px; cursor: pointer;">Eliminar Empleado</button>
        </form>
        
    <?php else: ?>
        <p>No se encontro el empleado especificado.</p>
    <?php endif; ?>
    
    <br>
    <a href="/PHP%20Modulos/Empleadoindex.php">Volver al listado de empleados</a>
    <a href="../index.php">Volver al Menu Principal</a>
</body>
</html>