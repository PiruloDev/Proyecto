<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Ingredientes</title>
    <style>
        /* Estilos básicos para mejor visualización */
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1, h2 { border-bottom: 2px solid #ccc; padding-bottom: 5px; }
        /* Estilos de navegación */
        .nav-menu a { margin-right: 15px; text-decoration: none; color: #007bff; font-weight: bold; }
        .nav-menu a:hover { color: #0056b3; }
        /* Estilos de tabla */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 14px; }
        th { background-color: #f2f2f2; }
        /* Estilos de formularios */
        form { margin-top: 10px; padding: 15px; border: 1px solid #eee; border-radius: 5px; }
        label { display: inline-block; width: 180px; margin-bottom: 5px; font-weight: bold; }
        input:not([type="submit"]):not([type="button"]) { padding: 5px; margin-bottom: 10px; width: 250px; }
        button { padding: 8px 15px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<div class="container">
    <h1>Gestión de Inventario de Ingredientes</h1>
    
    <div class="nav-menu">
        <a href="Ingredienteindex.php?modulo=menu">Menú Principal</a>
        <a href="Ingredienteindex.php?modulo=ingredientes">Ingredientes</a>
        <a href="Ingredienteindex.php?modulo=proveedores">Proveedores</a>
        <a href="Ingredienteindex.php?modulo=categoria">Categorías</a>
        <a href="Ingredienteindex.php?modulo=detallePedidos">Detalle Pedidos</a>
    </div>
    
    <hr>
    
    <?php if (!empty($mensaje)): ?>
        <div style="margin: 15px 0; padding: 10px; border: 1px solid; border-radius: 5px; background-color: #e9ffea; border-color: #38c172; color: #1f7d4b;">
            <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <section id="listado">
        <h2>Listado de Ingredientes</h2>
        <a href="Ingredienteindex.php?modulo=ingredientes&accion=listar" style="float:right; text-decoration: none;">Recargar Lista</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Proveedor ID</th>
                    <th>Categoría ID</th>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Fec. Vencimiento</th>
                    <th>Referencia</th>
                    <th>Fec. Entrega</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ingredientes) && is_array($ingredientes)): ?>
                    <?php foreach ($ingredientes as $i): ?>
                        <tr>
                            <td><?= htmlspecialchars($i['id'] ?? $i['idIngrediente'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($i['idProveedor'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($i['idCategoria'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($i['nombreIngrediente'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($i['cantidadIngrediente'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($i['fechaVencimiento'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($i['referenciaIngrediente'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($i['fechaEntregaIngrediente'] ?? 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8">No hay ingredientes registrados o la API no devolvió un formato válido.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <hr>
    <section id="agregar">
        <h2>Agregar Nuevo Ingrediente (POST)</h2>
        <form method="POST" action="Ingredienteindex.php?modulo=ingredientes&accion=agregar">
            <label for="idProveedor">ID Proveedor:</label>
            <input type="number" id="idProveedor" name="idProveedor" required min="1"><br>
            
            <label for="idCategoria">ID Categoría:</label>
            <input type="number" id="idCategoria" name="idCategoria" required min="1"><br>
            
            <label for="nombreIngrediente">Nombre Ingrediente:</label>
            <input type="text" id="nombreIngrediente" name="nombreIngrediente" required><br>
            
            <label for="cantidadIngrediente">Cantidad Inicial:</label>
            <input type="number" id="cantidadIngrediente" name="cantidadIngrediente" required min="0"><br>
            
            <label for="fechaVencimiento">Fecha Vencimiento (YYYY-MM-DD):</label>
            <input type="date" id="fechaVencimiento" name="fechaVencimiento"><br>
            
            <label for="referenciaIngrediente">Referencia Ingrediente:</label>
            <input type="text" id="referenciaIngrediente" name="referenciaIngrediente" required><br>
            
            <label for="fechaEntregaIngrediente">Fecha Entrega (YYYY-MM-DD):</label>
            <input type="date" id="fechaEntregaIngrediente" name="fechaEntregaIngrediente"><br>
            
            <button type="submit">Agregar Ingrediente</button>
        </form>
    </section>

    <hr>

    <section id="actualizar">
        <h2>Actualizar Ingrediente Completo (PUT Lógico)</h2>
        <form method="POST" action="Ingredienteindex.php?modulo=ingredientes&accion=actualizar">
            <label for="id_put">ID Ingrediente a Actualizar:</label>
            <input type="number" id="id_put" name="id" required min="1"><br>
            
            <label for="idProveedor_put">Nuevo ID Proveedor:</label>
            <input type="number" id="idProveedor_put" name="idProveedor" required min="1"><br>
            
            <label for="idCategoria_put">Nuevo ID Categoría:</label>
            <input type="number" id="idCategoria_put" name="idCategoria" required min="1"><br>
            
            <label for="nombreIngrediente_put">Nuevo Nombre Ingrediente:</label>
            <input type="text" id="nombreIngrediente_put" name="nombreIngrediente" required><br>
            
            <label for="cantidadIngrediente_put">Nueva Cantidad:</label>
            <input type="number" id="cantidadIngrediente_put" name="cantidadIngrediente" required min="0"><br>
            
            <label for="fechaVencimiento_put">Nueva Fecha Vencimiento (YYYY-MM-DD):</label>
            <input type="date" id="fechaVencimiento_put" name="fechaVencimiento"><br>
            
            <label for="referenciaIngrediente_put">Nueva Referencia:</label>
            <input type="text" id="referenciaIngrediente_put" name="referenciaIngrediente" required><br>
            
            <label for="fechaEntregaIngrediente_put">Nueva Fecha Entrega (YYYY-MM-DD):</label>
            <input type="date" id="fechaEntregaIngrediente_put" name="fechaEntregaIngrediente"><br>
            
            <button type="submit">Actualizar Ingrediente</button>
        </form>
    </section>

    <hr>

    <section id="actualizarCantidad">
        <h2>Actualizar Solo Cantidad (PATCH Lógico)</h2>
        <form method="POST" action="Ingredienteindex.php?modulo=ingredientes&accion=actualizarCantidad">
            <label for="id_patch">ID Ingrediente:</label>
            <input type="number" id="id_patch" name="id" required min="1"><br>
            
            <label for="cantidadIngrediente_patch">Nueva Cantidad (Stock):</label>
            <input type="number" id="cantidadIngrediente_patch" name="cantidadIngrediente" required min="0"><br>
            
            <button type="submit">Actualizar Cantidad</button>
        </form>
    </section>

    <hr>

    <section id="eliminar">
        <h2>Eliminar Ingrediente (DELETE Lógico)</h2>
        <form method="POST" action="Ingredienteindex.php?modulo=ingredientes&accion=eliminar">
            <label for="id_delete">ID Ingrediente a Eliminar:</label>
            <input type="number" id="id_delete" name="id" required min="1"><br>
            
            <button type="submit" style="background-color: #dc3545;">Eliminar Ingrediente</button>
        </form>
    </section>

</div>

</body>
</html>