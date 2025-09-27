<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empleados</title>
</head>
<body>
    <a href="../index.php">Volver al Menu Principal</a>
    <a href="/PHP%20Modulos/Userindex.php">Gestión de Clientes</a>
    
    <h1>Gestión de Empleados</h1>
    <?php if (!empty($mensaje)): ?>
        <div><?php echo $mensaje; ?></div>
    <?php endif; ?>
    
    <h2>Agregar Nuevo Empleado</h2>
    <form method="POST" action="">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required><br><br>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        
        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required><br><br>
        
        <button type="submit">Agregar Empleado</button>
    </form>
    
    <h2>Lista de Empleados</h2>
    <?php if (!empty($usuarios)): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $index => $usuario): ?>
                    <?php 
                    $display_id = $index + 1;
                    $real_id = $usuario['Id:'] ?? $display_id; // <-- ESTA CORREGIDO Y EL ERROR ERA QUE LA CLAVE ES "Id:" Y NO "id" :)))))))))))))))))))
                    $has_valid_data = !empty($usuario['Nombre:']) || !empty($usuario['Correo Electronico:']);
                    
                    if ($has_valid_data): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($real_id); ?></td>
                        <td><?php echo htmlspecialchars($usuario['Nombre:'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($usuario['Correo Electronico:'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="/PHP%20Modulos/Empleadoindex.php?action=modify&id=<?php echo urlencode($real_id); ?>">
                                Modificar
                            </a>
                            |
                            <form method="POST" action="/PHP%20Modulos/Empleadoindex.php?action=delete" style="display: inline;" onsubmit="return confirm('¿Está seguro de que desea eliminar este empleado? Esta acción no se puede deshacer.')">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($real_id); ?>">
                                <button type="submit" style="background: none; border: none; color: red; text-decoration: underline; cursor: pointer; font-size: inherit;">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay empleados registrados.</p>
    <?php endif; ?>
</body>
</html>