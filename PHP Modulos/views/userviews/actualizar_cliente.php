<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Cliente</title>
</head>
<body>
    <h1>Actualizar Cliente</h1>
    
    <?php if (!empty($mensaje)): ?>
        <div><?php echo $mensaje; ?></div>
    <?php endif; ?>
    
    <?php if ($cliente): ?>
        <p><strong>Actualizando cliente:</strong> <?php echo htmlspecialchars($cliente['Nombre:'] ?? $cliente['nombre'] ?? 'N/A'); ?></p>
        
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>">
            
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" 
                   placeholder="<?php echo htmlspecialchars($cliente['Nombre:'] ?? $cliente['nombre'] ?? ''); ?>"><br>
            <small>Dejar vacío si no desea cambiar</small><br><br>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" 
                   placeholder="<?php echo htmlspecialchars($cliente['Correo Electronico:'] ?? $cliente['email'] ?? ''); ?>"><br>
            <small>Dejar vacío si no desea cambiar</small><br><br>
            
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" 
                   placeholder="<?php echo htmlspecialchars($cliente['Telefono:'] ?? $cliente['telefono'] ?? ''); ?>"><br>
            <small>Dejar vacío si no desea cambiar</small><br><br>
            
            <label for="contrasena">Nueva Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena"><br>
            <small>Dejar vacío si no desea cambiar</small><br><br>
            
            <button type="submit">Actualizar Cliente</button>
            <a href="index.php">Cancelar</a>
            <a href="../index.php">Volver al Menu Principal</a>
        </form>
    <?php else: ?>
        <p>Cliente no encontrado.</p>
        <a href="index.php">Volver a la lista</a>
        <a href="../index.php">Volver al Menu Principal</a>
    <?php endif; ?>
</body>
</html>