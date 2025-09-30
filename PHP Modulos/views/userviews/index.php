<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
</head>
<body>
    <a href="../index.php">Volver al Menu Principal</a>
    <a href="/PHP%20Modulos/Empleadoindex.php">Gestión de Empleados</a>
    
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
                    <?php 
                    $display_id = $index + 1;
                    $has_valid_data = !empty($usuario['Nombre:']) || !empty($usuario['Correo Electronico:']) || !empty($usuario['Telefono:']);
                    
                    if ($has_valid_data): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($display_id); ?></td>
                        <td><?php echo htmlspecialchars($usuario['Nombre:'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($usuario['Correo Electronico:'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($usuario['Telefono:'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="/PHP%20Modulos/Userindex.php?action=modify&id=<?php echo urlencode($display_id); ?>">
                                Modificar
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay clientes registrados.</p>
    <?php endif; ?>
</body>
</html>