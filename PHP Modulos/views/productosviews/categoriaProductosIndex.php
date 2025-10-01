<?php
require_once __DIR__ . '/../../controllers/productoscontroller/CategoriaProductosController.php';
include __DIR__ . '/../../templates/header.php';
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Categorías de Productos</h2>

    <!-- Formulario para agregar -->
    <form method="POST" action="../../controllers/productoscontroller/CategoriaProductosController.php" class="d-flex mb-4">
        <input type="text" name="nombre" class="form-control me-2" placeholder="Nombre de la categoría" required>
        <input type="hidden" name="accion" value="crear">
        <button type="submit" class="btn btn-success">Agregar</button>
    </form>

    <!-- Tabla de categorías -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre Categoría</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($categorias)): ?>
                <?php foreach ($categorias as $categoria): ?>
                    <tr>
                        <td><?= htmlspecialchars($categoria['id']) ?></td>
                        <td><?= htmlspecialchars($categoria['nombre']) ?></td>
                        <td>
                            <form method="POST" action="../../controllers/productoscontroller/CategoriaProductosController.php" class="d-inline">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="id" value="<?= $categoria['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3" class="text-center">No hay categorías disponibles</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
