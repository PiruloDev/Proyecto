<?php
// Variables que vienen del controller
$mensaje = $mensaje ?? '';
$ingredientes = $ingredientes ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ingredientes</title>
</head>
<body>
    <h1>Lista de Ingredientes</h1>
    
    <!-- Mostrar mensaje si existe -->
    <?php if (!empty($mensaje)): ?>
        <?= $mensaje ?>
    <?php endif; ?>
    
    <!-- Lista de ingredientes -->
    <?php if (is_array($ingredientes) && !empty($ingredientes)): ?>
        <h2>Ingredientes Registrados</h2>
        <?php foreach ($ingredientes as $i): ?>
            <div class="ingrediente-item">
                <?php if (is_array($i)): ?>
                    <!-- Formato array (datos de la API) -->
                    <strong>ID:</strong> <?= htmlspecialchars($i['idIngrediente'] ?? 'N/A') ?><br>
                    <strong>Proveedor ID:</strong> <?= htmlspecialchars($i['idProveedor'] ?? 'N/A') ?><br>
                    <strong>Categoría ID:</strong> <?= htmlspecialchars($i['idCategoria'] ?? 'N/A') ?><br>
                    <strong>Nombre:</strong> <?= htmlspecialchars($i['nombreIngrediente'] ?? 'N/A') ?><br>
                    <strong>Cantidad:</strong> <?= htmlspecialchars($i['cantidadIngrediente'] ?? 'N/A') ?><br>
                    <strong>Fecha Vencimiento:</strong> <?= htmlspecialchars($i['fechaVencimiento'] ?? 'N/A') ?><br>
                    <strong>Referencia:</strong> <?= htmlspecialchars($i['referenciaIngrediente'] ?? 'N/A') ?><br>
                    <strong>Fecha Entrega:</strong> <?= htmlspecialchars($i['fechaEntregaIngrediente'] ?? 'N/A') ?>
                <?php else: ?>
                    <!-- Formato string (tu formato original con separadores) -->
                    <?php
                    $partes = is_string($i) ? explode('________', $i) : [];
                    $idIngrediente = $partes[0] ?? 'N/A';
                    $idProveedor = $partes[1] ?? 'N/A';
                    $idCategoria = $partes[2] ?? 'N/A';
                    $nombreIngrediente = $partes[3] ?? 'N/A';
                    $cantidadIngrediente = $partes[4] ?? 'N/A';
                    $fechaVencimiento = $partes[5] ?? 'N/A';
                    $referenciaIngrediente = $partes[6] ?? 'N/A';
                    $fechaEntregaIngrediente = $partes[7] ?? 'N/A';
                    ?>
                    <strong>ID:</strong> <?= htmlspecialchars($idIngrediente) ?><br>
                    <strong>Proveedor ID:</strong> <?= htmlspecialchars($idProveedor) ?><br>
                    <strong>Categoría ID:</strong> <?= htmlspecialchars($idCategoria) ?><br>
                    <strong>Nombre:</strong> <?= htmlspecialchars($nombreIngrediente) ?><br>
                    <strong>Cantidad:</strong> <?= htmlspecialchars($cantidadIngrediente) ?><br>
                    <strong>Fecha Vencimiento:</strong> <?= htmlspecialchars($fechaVencimiento) ?><br>
                    <strong>Referencia:</strong> <?= htmlspecialchars($referenciaIngrediente) ?><br>
                    <strong>Fecha Entrega:</strong> <?= htmlspecialchars($fechaEntregaIngrediente) ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color:red;">No hay ingredientes registrados o error al obtener los datos.</p>
    <?php endif; ?>
    
    <hr>
    
    <!-- Formulario para agregar ingrediente -->
    <h2>Agregar Nuevo Ingrediente</h2>
<form method="POST" action="Ingredienteindex.php?modulo=ingredientes&accion=agregar">
    <div class="form-group">
        <label for="idProveedor">ID del Proveedor:</label>
        <input type="number" name="idProveedor" id="idProveedor" min="1" required>
    </div>
    
    <div class="form-group">
        <label for="idCategoria">ID de la Categoría:</label>
        <input type="number" name="idCategoria" id="idCategoria" min="1" required>
    </div>
    
    <div class="form-group">
        <label for="nombreIngrediente">Nombre del Ingrediente:</label>
        <input type="text" name="nombreIngrediente" id="nombreIngrediente" required>
    </div>
    
    <div class="form-group">
        <label for="cantidadIngrediente">Cantidad:</label>
        <input type="number" name="cantidadIngrediente" id="cantidadIngrediente" min="0" required>
    </div>
    
    <div class="form-group">
        <label for="fechaVencimiento">Fecha de Vencimiento:</label>
        <input type="date" name="fechaVencimiento" id="fechaVencimiento" required>
    </div>
    
    <div class="form-group">
        <label for="referenciaIngrediente">Referencia:</label>
        <input type="text" name="referenciaIngrediente" id="referenciaIngrediente" required>
    </div>
    
    <div class="form-group">
        <label for="fechaEntregaIngrediente">Fecha de Entrega:</label>
        <input type="date" name="fechaEntregaIngrediente" id="fechaEntregaIngrediente" required>
    </div>
    
    <input type="submit" value="Agregar Ingrediente">
</form>
</body>
</html>