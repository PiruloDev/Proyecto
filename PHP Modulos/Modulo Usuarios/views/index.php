<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
</head>
<body>
    <a href="../index.php">Volver al Menu Principal</a>
    
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
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $index => $usuario): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usuario['id'] ?? ($index + 1)); ?></td>
                        <td><?php echo htmlspecialchars($usuario['Nombre:'] ?? $usuario['nombre'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($usuario['Correo Electronico:'] ?? $usuario['email'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($usuario['Telefono:'] ?? $usuario['telefono'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="index.php?action=update&id=<?php echo urlencode($usuario['id'] ?? ($index + 1)); ?>">
                                Editar
                            </a>
                            <a href="index.php?action=modify&id=<?php echo urlencode($usuario['id'] ?? ($index + 1)); ?>">
                                Modificar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay clientes registrados.</p>
    <?php endif; ?>
</body>
</html>