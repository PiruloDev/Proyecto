<?php
// Variables que vienen del controller
$mensaje = $mensaje ?? '';
$ingredientes = $ingredientes ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ingredientes</title>

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="/ModuloPhp/Proyecto/PHP%20Modulos/Modulo%20Ingredientes/ModuloPhp/css/estilos.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>


<body class="bg-light">
    <div class="container py-4">
        <h1 class="text-center mb-4">Lista de Ingredientes</h1>
        
        <!-- Mostrar mensaje si existe -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert <?= (strpos($mensaje, 'correctamente') !== false) ? 'alert-success' : 'alert-danger' ?> text-center">
                <?= $mensaje ?>
            </div>
        <?php endif; ?>
        
        <!-- Lista de ingredientes -->
        <?php if (is_array($ingredientes) && !empty($ingredientes)): ?>
            <h2 class="h4 mb-3">Ingredientes Registrados</h2>
            <div class="row g-3">
                <?php foreach ($ingredientes as $i): ?>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <?php if (is_array($i)): ?>
                                    <p><strong>ID:</strong> <?= htmlspecialchars($i['idIngrediente'] ?? 'N/A') ?></p>
                                    <p><strong>Proveedor ID:</strong> <?= htmlspecialchars($i['idProveedor'] ?? 'N/A') ?></p>
                                    <p><strong>Categoría ID:</strong> <?= htmlspecialchars($i['idCategoria'] ?? 'N/A') ?></p>
                                    <p><strong>Nombre:</strong> <?= htmlspecialchars($i['nombreIngrediente'] ?? 'N/A') ?></p>
                                    <p><strong>Cantidad:</strong> <?= htmlspecialchars($i['cantidadIngrediente'] ?? 'N/A') ?></p>
                                    <p><strong>Fecha Vencimiento:</strong> <?= htmlspecialchars($i['fechaVencimiento'] ?? 'N/A') ?></p>
                                    <p><strong>Referencia:</strong> <?= htmlspecialchars($i['referenciaIngrediente'] ?? 'N/A') ?></p>
                                    <p><strong>Fecha Entrega:</strong> <?= htmlspecialchars($i['fechaEntregaIngrediente'] ?? 'N/A') ?></p>
                                <?php else: ?>
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
                                    <p><strong>ID:</strong> <?= htmlspecialchars($idIngrediente) ?></p>
                                    <p><strong>Proveedor ID:</strong> <?= htmlspecialchars($idProveedor) ?></p>
                                    <p><strong>Categoría ID:</strong> <?= htmlspecialchars($idCategoria) ?></p>
                                    <p><strong>Nombre:</strong> <?= htmlspecialchars($nombreIngrediente) ?></p>
                                    <p><strong>Cantidad:</strong> <?= htmlspecialchars($cantidadIngrediente) ?></p>
                                    <p><strong>Fecha Vencimiento:</strong> <?= htmlspecialchars($fechaVencimiento) ?></p>
                                    <p><strong>Referencia:</strong> <?= htmlspecialchars($referenciaIngrediente) ?></p>
                                    <p><strong>Fecha Entrega:</strong> <?= htmlspecialchars($fechaEntregaIngrediente) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                No hay ingredientes registrados o error al obtener los datos.
            </div>
        <?php endif; ?>
        
        <hr class="my-4">
        
        <!-- Formulario para agregar ingrediente -->
        <h2 class="h4 mb-3">Agregar Nuevo Ingrediente</h2>
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="idProveedor" class="form-label">ID del Proveedor:</label>
                            <input type="number" class="form-control" name="idProveedor" id="idProveedor" min="1" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="idCategoria" class="form-label">ID de la Categoría:</label>
                            <input type="number" class="form-control" name="idCategoria" id="idCategoria" min="1" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="nombreIngrediente" class="form-label">Nombre del Ingrediente:</label>
                            <input type="text" class="form-control" name="nombreIngrediente" id="nombreIngrediente" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="cantidadIngrediente" class="form-label">Cantidad:</label>
                            <input type="number" class="form-control" name="cantidadIngrediente" id="cantidadIngrediente" min="0" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="fechaVencimiento" class="form-label">Fecha de Vencimiento:</label>
                            <input type="date" class="form-control" name="fechaVencimiento" id="fechaVencimiento" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="referenciaIngrediente" class="form-label">Referencia:</label>
                            <input type="text" class="form-control" name="referenciaIngrediente" id="referenciaIngrediente" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="fechaEntregaIngrediente" class="form-label">Fecha de Entrega:</label>
                            <input type="date" class="form-control" name="fechaEntregaIngrediente" id="fechaEntregaIngrediente" required>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <input type="submit" value="Agregar Ingrediente" class="btn btn-primary px-4">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (opcional si usas componentes dinámicos) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
