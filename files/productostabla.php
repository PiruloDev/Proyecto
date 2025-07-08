<?php
include 'conexion.php';

// Consultar todos los productos para mostrarlos en la tabla
$consulta = "SELECT * FROM Productos ORDER BY ID_PRODUCTO DESC";
$resultado = $conexion->query($consulta);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos</title>
    <style>
        /* Estilos básicos para mejorar la presentación */
        body { 
            font-family: Arial, sans-serif; 
            margin: 2em; 
            background-color: #f8f9fa;
        }
        
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        h2 {
            color: #495057;
            margin-top: 30px;
        }
        
        table { 
            border-collapse: collapse; 
            width: 100%; 
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        th, td { 
            border: 1px solid #dee2e6; 
            padding: 12px; 
            text-align: left; 
        }
        
        th { 
            background-color: #343a40; 
            color: white;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:hover {
            background-color: #e9ecef;
        }
        
        form { 
            margin-bottom: 2em; 
            padding: 20px; 
            border: 1px solid #dee2e6; 
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        label {
            font-weight: bold;
            color: #495057;
        }
        
        input, button {
            padding: 8px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 14px;
        }
        
        input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0,123,255,0.25);
        }
        
        button {
            background-color: #28a745;
            color: white;
            cursor: pointer;
            padding: 10px 20px;
            font-weight: bold;
        }
        
        button:hover {
            background-color: #218838;
        }
        
        .btn-borrar { 
            color: #dc3545; 
            text-decoration: none; 
            padding: 5px 10px;
            border: 1px solid #dc3545;
            border-radius: 3px;
            transition: all 0.3s;
            margin-left: 5px;
            font-size: 12px;
        }
        
        .btn-borrar:hover {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-gestionar { 
            color: #007bff; 
            text-decoration: none; 
            padding: 5px 10px;
            border: 1px solid #007bff;
            border-radius: 3px;
            transition: all 0.3s;
            font-size: 12px;
        }
        
        .btn-gestionar:hover {
            background-color: #007bff;
            color: white;
        }
        
        .precio {
            font-weight: bold;
            color: #28a745;
        }
        
        .estado-activo {
            color: #28a745;
            font-weight: bold;
        }
        
        .estado-inactivo {
            color: #dc3545;
            font-weight: bold;
        }
        
        .btn-toggle {
            color: #ffc107;
            text-decoration: none;
            padding: 5px 10px;
            border: 1px solid #ffc107;
            border-radius: 3px;
            transition: all 0.3s;
            font-size: 12px;
            margin-right: 5px;
        }
        
        .btn-toggle:hover {
            background-color: #ffc107;
            color: #212529;
        }
        
        .btn-eliminar-forzado {
            color: #dc3545;
            text-decoration: none;
            padding: 5px 10px;
            border: 2px solid #dc3545;
            border-radius: 3px;
            transition: all 0.3s;
            font-size: 11px;
            margin-left: 5px;
            background-color: #fff;
        }
        
        .btn-eliminar-forzado:hover {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>

    <h1>Gestión de Productos</h1>

    <h2 id="agregar">Añadir Nuevo Producto</h2>
    <form action="agregar_producto.php" method="POST">
        <label for="nombre">Nombre del Producto:</label><br>
        <input type="text" id="nombre" name="nombre_producto" required maxlength="100"><br><br>
        
        <label for="precio">Precio:</label><br>
        <input type="number" step="0.01" id="precio" name="precio_producto" required min="0.01" max="99999.99"><br><br>
        
        <label for="stock_min">Stock Mínimo:</label><br>
        <input type="number" id="stock_min" name="stock_min" required min="0" max="9999"><br><br>
        
        <label for="marca_tipo">Marca/Tipo:</label><br>
        <input type="text" id="marca_tipo" name="tipo_producto_marca" required maxlength="100"><br><br>
        
        <label for="fecha_vencimiento">Fecha de Vencimiento:</label><br>
        <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" required 
               min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"><br><br>
        
        <button type="submit">Agregar Producto</button>
    </form>

    <h2>Inventario Actual</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Stock Mínimo</th>
                <th>Marca/Tipo</th>
                <th>Fecha Vencimiento</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['ID_PRODUCTO']); ?></td>
                        <td><?php echo htmlspecialchars($fila['NOMBRE_PRODUCTO']); ?></td>
                        <td class="precio">$<?php echo number_format($fila['PRECIO_PRODUCTO'], 2); ?></td>
                        <td><?php echo htmlspecialchars($fila['PRODUCTO_STOCK_MIN']); ?></td>
                        <td><?php echo htmlspecialchars($fila['TIPO_PRODUCTO_MARCA']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($fila['FECHA_VENCIMIENTO_PRODUCTO'])); ?></td>
                        <td>
                            <?php if (isset($fila['ACTIVO']) && $fila['ACTIVO'] == 1): ?>
                                <span class="estado-activo">✓ Activo</span>
                            <?php else: ?>
                                <span class="estado-inactivo">✗ Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="toggle_producto.php?id=<?php echo $fila['ID_PRODUCTO']; ?>&estado=<?php echo isset($fila['ACTIVO']) ? $fila['ACTIVO'] : 1; ?>"
                            class="btn-toggle"
                            title="Cambiar estado del producto">
                            <?php echo (isset($fila['ACTIVO']) && $fila['ACTIVO'] == 1) ? 'Desactivar' : 'Activar'; ?>
                            </a>
                            <a href="gestionar_producto.php?id=<?php echo $fila['ID_PRODUCTO']; ?>"
                            class="btn-gestionar"
                            title="Gestionar producto de forma segura">
                            Gestionar
                            </a>
                            <a href="eliminar_forzado.php?id=<?php echo $fila['ID_PRODUCTO']; ?>"
                            class="btn-eliminar-forzado"
                            onclick="return confirm('⚠️ ELIMINACIÓN FORZADA ⚠️\\n\\nEsto eliminará PERMANENTEMENTE el producto: <?php echo htmlspecialchars($fila['NOMBRE_PRODUCTO']); ?>\\n\\n🚨 ADVERTENCIA: Si este producto está en pedidos, se ROMPERÁN las referencias de la base de datos.\\n\\n❌ Esta acción NO se puede deshacer.\\n\\n✅ Alternativa recomendada: Usa \\'Desactivar\\' en lugar de eliminar.\\n\\n¿Estás ABSOLUTAMENTE SEGURO de continuar?');"
                            title="ELIMINAR PERMANENTEMENTE (¡Peligroso!)">
                            🗑️ Forzar
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #6c757d; font-style: italic;">
                        No hay productos registrados en el inventario.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
