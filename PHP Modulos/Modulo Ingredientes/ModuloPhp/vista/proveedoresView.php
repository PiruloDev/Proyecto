<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Proveedores</title>
</head>
<body>
    <h1>Proveedores</h1>
    <?= $mensaje ?? "" ?>

    <h2>Agregar Proveedor</h2>
    <form method="POST" action="?modulo=proveedores&accion=agregar">
        Nombre: <input type="text" name="nombreProv"><br>
        Teléfono: <input type="text" name="telefonoProv"><br>
        Email: <input type="email" name="emailProv"><br>
        Dirección: <input type="text" name="direccionProv"><br>
        Activo: <input type="checkbox" name="activoProv"><br>
        <button type="submit">Guardar</button>
    </form>

    <h2>Lista de Proveedores</h2>
    <table border="1">
        <tr>
            <th>ID</th><th>Nombre</th><th>Teléfono</th><th>Email</th><th>Dirección</th><th>Activo</th>
        </tr>
        <?php if ($proveedores && is_array($proveedores)): ?>
            <?php foreach ($proveedores as $prov): ?>
                <tr>
                    <td><?= $prov["idProveedor"] ?></td>
                    <td><?= $prov["nombreProv"] ?></td>
                    <td><?= $prov["telefonoProv"] ?></td>
                    <td><?= $prov["emailProv"] ?></td>
                    <td><?= $prov["direccionProv"] ?></td>
                    <td><?= $prov["activoProv"] ? "Sí" : "No" ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">No hay proveedores</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Proveedores</title>
</head>
<body>
    <h1>Gestión de Proveedores</h1>
    
    <?php if (!empty($mensaje)) echo $mensaje; ?>

    <h2>Agregar Proveedor</h2>
    <form method="POST" action="?modulo=proveedores&accion=agregar">
        <input type="text" name="nombreProv" placeholder="Nombre" required>
        <input type="text" name="telefonoProv" placeholder="Teléfono" required>
        <input type="email" name="emailProv" placeholder="Correo">
        <input type="text" name="direccionProv" placeholder="Dirección">
        <label>Activo <input type="checkbox" name="activoProv" value="1"></label>
        <button type="submit">Agregar</button>
    </form>

    <h2>Lista de Proveedores</h2>
    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Dirección</th>
            <th>Activo</th>
            <th>Acciones</th>
        </tr>
        <?php if (!empty($proveedores)) : ?>
            <?php foreach ($proveedores as $prov): ?>
                <tr>
                    <td><?= htmlspecialchars($prov["idProveedor"]) ?></td>
                    <td><?= htmlspecialchars($prov["nombreProv"]) ?></td>
                    <td><?= htmlspecialchars($prov["telefonoProv"]) ?></td>
                    <td><?= htmlspecialchars($prov["emailProv"]) ?></td>
                    <td><?= htmlspecialchars($prov["direccionProv"]) ?></td>
                    <td><?= $prov["activoProv"] ? "Sí" : "No" ?></td>
                    <td>
                        <!-- Formulario actualizar -->
                        <form method="POST" action="?modulo=proveedores&accion=actualizar" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $prov["idProveedor"] ?>">
                            <input type="text" name="nombreProv" value="<?= htmlspecialchars($prov["nombreProv"]) ?>">
                            <input type="text" name="telefonoProv" value="<?= htmlspecialchars($prov["telefonoProv"]) ?>">
                            <input type="email" name="emailProv" value="<?= htmlspecialchars($prov["emailProv"]) ?>">
                            <input type="text" name="direccionProv" value="<?= htmlspecialchars($prov["direccionProv"]) ?>">
                            <label>Activo 
                                <input type="checkbox" name="activoProv" value="1" <?= $prov["activoProv"] ? "checked" : "" ?>>
                            </label>
                            <button type="submit">Actualizar</button>
                        </form>

                        <!-- Formulario eliminar -->
                        <form method="POST" action="?modulo=proveedores&accion=eliminar" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $prov["idProveedor"] ?>">
                            <button type="submit" onclick="return confirm('¿Eliminar proveedor?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr><td colspan="7">No hay proveedores</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
